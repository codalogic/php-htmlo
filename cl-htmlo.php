<?php
//----------------------------------------------------------------------------
// Copyright (c) 2016, Codalogic Ltd (http://www.codalogic.com)
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the "Software"),
// to deal in the Software without restriction, including without limitation
// the rights to use, copy, modify, merge, publish, distribute, sublicense,
// and/or sell copies of the Software, and to permit persons to whom the
// Software is furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
// THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
// FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
// DEALINGS IN THE SOFTWARE.
//----------------------------------------------------------------------------

namespace CL {

class Htmlo
{
    const TAG = 'Tag';
    const CSSCLASS = 'Class';
    const TOKEN = 'Token';
    const NUMBER = 'Number';
    const NAMED = 'Named';

    private $tag_stack = array();
    private $output = '';

    public function htmls( $input )
    {
        foreach( preg_split( '/\r\n|\n|\r/', $input ) as $line ) {
            $this->output .= $this->process_line( $line ) . "\n";
        }
        return $this->output;
    }

    private function process_line( $line )
    {
        $trimmed_line = ltrim( $line );
        if( $trimmed_line[0] == '.' ) {
            if( preg_match( '/^(\s*)\.#\s*(.*)/', $line, $matches ) ) {     // Comments : .#
                return $matches[1] . "<!-- " . $matches[2] . " -->";
            }
            else if( preg_match( '/^(\s*)\.\s*(\w.*)/', $line, $matches ) ) {   // Start tags : .[a-z]
                return $matches[1] . $this->tag( $matches[2] );
            }
            else if( preg_match( '/^(\s*)\.\s*(\'.*)/', $line, $matches ) ) {   // class : .'
                return $matches[1] . $this->div_class( $matches[2] );
            }
            else if( preg_match( '/^(\s*)\.\.\s*(\w+)/', $line, $matches ) ) {   // End tags : .. tag
                $this->remove_stack_tag( $matches[2] );
                return $matches[1] . "</" . $matches[2] . ">";
            }
            else if( preg_match( '/^(\s*)\.\.\s*$/', $line, $matches ) ) {   // Automatic end tag : ..
                return $matches[1] . "</" . $this->unstack_tag() . ">";
            }
            else if( preg_match( '/^(\s*)\.\.\.\s*(\w.*)/', $line, $matches ) ) {   // End followed by start tag : ...[a-z]
                $this->remove_stack_tag( $matches[2] );
                return $matches[1] . "</" . $matches[2] . ">\n" . $matches[1] . $this->tag( $matches[2] . $matches[3] );
            }
            else if( preg_match( '/^(\s*)\.\.\.\s*(\'.*)/', $line, $matches ) ) {   // End tag followed by class : ...'
                $this->remove_stack_tag( 'div' );
                return $matches[1] . "</div>\n" . $matches[1] . $this->div_class( $matches[2] . $matches[3] );
            }
            else if( preg_match( '/^(\s*)\.\s*!(\w.*)/', $line, $matches ) ) {   // Call function : .![a-z]
                return $this->call_func( $matches[2] );
            }
            else if( preg_match( '/^(\s*)\.\s*!!(\w.*)/', $line, $matches ) ) {   // Call function with echo : .!![a-z]
                echo $this->output;
                echo $this->call_func( $matches[2] );
                $this->output = '';
                return '';
            }
            else if( preg_match( '/^(\s*)\.\s*:(.*)/', $line, $matches ) ) {   // HTML escape output : .:
                return $matches[1] . htmlentities( $matches[2], ENT_COMPAT | ENT_HTML401, 'UTF-8', false );
            }
        }

        return $line;
    }

    private function tag( $line )
    {
        $segments = $this->segment( $line );
        $output = '<' . $segments[1];
        $class = $this->find_class( $segments );
        if( $class != '' )
            $output .= " class=$class";
        $output .= $this->tag_specific_args( $segments );
        $output .= $this->named_args( $segments );
        if( $this->has_content( $segments ) ) {
            $content = $this->process_line( $this->find_content( $segments ) );
            if( $content != '' )
                $output .= '>' . $content . '</' . $segments[1] . '>';
            else
                $output .= ' />';
        }
        else {
            $output .= '>';
            $this->stack_tag( $segments[1] );
        }
        return $output;
    }

    private function div_class( $line )
    {
        $segments = $this->segment( $line );
        if( count( $segments >= 3 ) && $segments[count($segments)-2] == ':' ) {
            return "<span class={$segments[1]}>" . $this->process_line( $segments[count($segments)-1] ) . "</span>";
        }
        $this->stack_tag( 'div' );
        return "<div class={$segments[1]}>";
    }

    private function call_func( $line )
    {
        $segments = $this->segment( $line );
        switch( count( $segments ) ) {
            case 2: return $segments[1]();
            case 4: return $segments[1]( $segments[3] );
            case 6: return $segments[1]( $segments[3], $segments[5] );
            case 8: return $segments[1]( $segments[3], $segments[5], $segments[7] );
            case 10: return $segments[1]( $segments[3], $segments[5], $segments[7], $segments[9] );
            case 12: return $segments[1]( $segments[3], $segments[5], $segments[7], $segments[9], $segments[11] );
        }
        return $segments[1]();
    }

    private function segment( $line )
    {
        $segments = array();
        $this->peel( $segments, $line, '/^\w+/', self::TAG );
        while( ($line = ltrim( $line )) != '' &&
                ( $this->peel( $segments, $line, '/^\w+\([^)]*\)/', self::NAMED ) ||
                    $this->peel( $segments, $line, '/^\d+/', self::NUMBER ) ||
                    $this->peel( $segments, $line, '/^\'[^\']+\'/', self::CSSCLASS ) ||
                    $this->peel( $segments, $line, '/^(?:https?|mailto):[^:\s]*/', self::TOKEN ) ||        // Needs to be before the second token form
                    $this->peel( $segments, $line, '/^[^\s:\'][^:\s]*/', self::TOKEN ) ) ) {
        }
        if( $this->peel( $segments, $line, '/^:/' ) )
            $segments[] = trim( $line );
        return $segments;
    }

    private function peel( &$segments, &$line, $pattern, $cat = '' )
    {
        if( preg_match( $pattern, $line, $matches ) ) {
            if( $cat != '' )
                $segments[] = $cat;
            $segments[] = $matches[0];
            $line = substr( $line, strlen( $matches[0] ) );
            return True;
        }
        return False;
    }

    private function tag_specific_args( &$segments )
    {
        if( $segments[1] == 'a' )
            return $this->address_args( $segments );
        else if( $segments[1] == 'img' )
            return $this->img_args( $segments );
        return '';
    }

    private function address_args( &$segments )
    {
        $output = '';
        $href = $this->find_token( $segments );
        if( $href != '' )
            $output .= " href='" . $href . "'";
        return $output;
    }

    private function img_args( &$segments )
    {
        $output = '';
        $src = $this->find_token( $segments );
        if( $src != '' )
            $output .= " src='" . $src . "'";
        $border = $this->find_number( $segments );
        if( $border != '' )
            $output .= " border='" . $border . "'";
        return $output;
    }

    private function named_args( &$segments )
    {
        $output = '';
        for( $i=0; count( $arg = $this->find_named( $segments, $i ) ) > 0; ++$i ) {
            $output .= " {$arg[0]}='{$arg[1]}'";
        }
        return $output;
    }

    private function find_class( &$segments )
    {
        return $this->find_item( $segments, self::CSSCLASS );
    }

    private function find_token( &$segments, $index = 0 )
    {
        return $this->find_item( $segments, self::TOKEN, $index );
    }

    private function find_number( &$segments, $index = 0 )
    {
        return $this->find_item( $segments, self::NUMBER, $index );
    }

    private function find_named( &$segments, $index = 0 )
    {
        $named = $this->find_item( $segments, self::NAMED, $index );
        if( $named != '' && preg_match( '/(\w+)\s*\(([^\)]*)\)/', $named, $matches ) ) {
            return array( $matches[1], trim( $matches[2] ) );
        }
        return array();
    }

    private function find_item( &$segments, $what, $index = 0 )
    {
        for( $i=0; $i<count( $segments ); $i += 2 ) {
            if( $segments[$i] == $what ) {
                if( $index <= 0 )
                    return $segments[$i+1];
                else
                    $index--;
            }
        }
        return '';
    }

    private function has_content( &$segments )
    {
        return count( $segments >= 3 ) && $segments[count($segments)-2] == ':';
    }

    private function find_content( &$segments )
    {
        if( $this->has_content( $segments ) )
            return $segments[count($segments)-1];
        return '';
    }

    static $non_stackable_tags = array( 'img', 'br', 'hr' );

    private function stack_tag( $tag )
    {
        if( ! in_array( $tag, self::$non_stackable_tags ) ) {
            $this->tag_stack[] = $tag;
        }
    }

    private function unstack_tag()
    {
        if( count( $this->tag_stack ) > 0 ) {
            return array_pop( $this->tag_stack );
        }
        return '';
    }

    private function remove_stack_tag( $tag )
    {
        if( count( $this->tag_stack ) > 0 && $this->tag_stack[count( $this->tag_stack )-1] == $tag ) {
            array_pop( $this->tag_stack );
        }
    }
}

}   // End of namespace CL

namespace { // Global namespace

function htmlo( $input )
{
    echo htmls( $input );
}

function htmls( $input )
{
    $h = new CL\Htmlo();
    return $h->htmls( $input );
}

}   // End of global namespace
?>
