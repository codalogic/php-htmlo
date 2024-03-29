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

abstract class HtmloCore
{
    const TAG = 'Tag';
    const CSSCLASS = 'Class';
    const TOKEN = 'Token';
    const NUMBER = 'Number';
    const NAMED = 'Named';

    private $tag_stack = array();
    private $is_output_disabled = 0;

    abstract protected function emit( $output );

    public function htmlo( $input )
    {
        foreach( preg_split( '/\r\n|\n|\r/', $input ) as $line ) {
            $result = $this->process_line( $line );
            if( isset( $result ) )
                $this->emit( "\n" );
        }
    }

    private function process_line( $line )
    {
        $trimmed_line = ltrim( $line );
        if( $trimmed_line != '' && $trimmed_line[0] == '.' && strlen( $trimmed_line ) >= 2 ) {
            $cmd = $trimmed_line[1];
            if( $cmd == '/' && strlen( $trimmed_line ) >= 3 && $trimmed_line[2] == '*' ) {     // Start of block comment: ./*
                ++$this->is_output_disabled;
                return NULL;
            }
            else if( $cmd == '*' && preg_match( '/^\.\*+\w*\**\//', $trimmed_line ) ) {     // End of block comment: .*/ (or .***/ or ***_A_code***/)
                if( $this->is_output_disabled > 0 )
                    --$this->is_output_disabled;
                return NULL;
            }
            else if( $this->is_output_disabled ) {
                return NULL;    // This only blocks processing lines beginning with . (e.g. prevents functions being called)
            }
            else if( $cmd == '-' && preg_match( '/^(\s*)\.-/', $line, $matches ) ) {       // Ignored line : .-
                return NULL;
            }
            else if( $cmd == '#' && preg_match( '/^(\s*)\.#\s*(.*)/', $line, $matches ) ) {     // Comments : .#
                $this->emit( $matches[1] . "<!-- " . $matches[2] . " -->" );
            }
            else if( $cmd == '|' && preg_match( '/^(\s*)\.\|([^:]*):\s*(.*)/', $line, $matches ) ) {       // Split line : .|, e.g. .tr .|| .td A | .td B | .td C
                $this->emit( $matches[1] );
                $this->split_line( $matches[2], $matches[3] );
            }
            else if( (ctype_alpha( $cmd ) || ctype_space( $cmd )) && preg_match( '/^(\s*)\.\s*(\w.*)/', $line, $matches ) ) {   // Start tags : .[a-z]
                $this->emit( $matches[1] );
                $this->tag( $matches[2] );
            }
            else if( ($cmd == "'" || ctype_space( $cmd )) && preg_match( '/^(\s*)\.\s*(\'.*)/', $line, $matches ) ) {   // class : .'
                $this->emit( $matches[1] );
                $this->div_class( $matches[2] );
            }
            else if( $cmd == '.' ) {
                if( preg_match( '/^(\s*)\.\.\s*(\w+)/', $line, $matches ) ) {   // End tags : .. tag
                    $this->remove_stack_tag( $matches[2] );
                    $this->emit( $matches[1] . "</" . $matches[2] . ">" );
                }
                else if( preg_match( '/^(\s*)\.\.\s*$/', $line, $matches ) ) {   // Automatic end tag : ..
                    $this->emit( $matches[1] . "</" . $this->unstack_tag() . ">" );
                }
                else if( preg_match( '/^(\s*)\.\.\.\s*$/', $line, $matches ) ) {   // Automatic end & reopen tag : ...
                    list( $name, $tag ) = $this->peek_stack_tag_pair();
                    $this->emit( $matches[1] . "</" . $name . ">\n" . $matches[1] . $tag );
                }
                else if( preg_match( '/^(\s*)\.\.\.\s*(\w.*)/', $line, $matches ) ) {   // End followed by start tag : ...[a-z]
                    $this->remove_stack_tag( $matches[2] );
                    $this->emit( $matches[1] . "</" . $matches[2] . ">\n" . $matches[1] );
                    $this->tag( $matches[2] );
                }
                else if( preg_match( '/^(\s*)\.\.\.\s*(\'.*)/', $line, $matches ) ) {   // End tag followed by class : ...'
                    $this->remove_stack_tag( 'div' );
                    $this->emit( $matches[1] . "</div>\n" . $matches[1] );
                    $this->div_class( $matches[2] );
                }
            }
            else if( $cmd == '!' && preg_match( '/^(\s*)\.!([^\w\s]*)\s*(\w*)\s*(.*)/', $line, $matches ) ) {   // Call function : .![a-z] opt-args or .!/[a-z] opt-args
                $this->call_func( $matches[3], $matches[2], $matches[4] );   // parameters are <function name>, <optional parameter separator>, <parameters>
            }
            else if( $cmd == ':' && preg_match( '/^(\s*)\.:(.*)/', $line, $matches ) ) {   // HTML escape output : .:
                $this->emit( $matches[1] . htmlentities( $matches[2], ENT_COMPAT | ENT_HTML401, 'UTF-8', false ) );
            }
            else {
                $this->emit( $line );
            }
            return '';
        }

        if( $this->is_output_disabled )
            return NULL;

        $this->emit( $line );
        return '';
    }

    private function split_line( $separator, $directives )
    {
        if( $separator == '' )
            $separator = '|';
        foreach( explode( $separator, $directives ) as $sub_line ) {
            if( preg_match( '/^\s*$/', $sub_line ) ) // If all whitespace...just emit result
                $this->emit( $sub_line );
            else {
                $this->process_line( trim( $sub_line ) );
            }
        }
    }

    private function tag( $line )
    {
        $segments = $this->segment( $line );
        if( $segments[1] == 'br' )
            return $this->br_tag( $segments );
        $output = '<' . $segments[1];
        $class = $this->find_class( $segments );
        if( $class != '' )
            $output .= " class=$class";
        $output .= $this->tag_specific_args( $segments );
        $output .= $this->named_args( $segments );
        if( $this->has_content( $segments ) ) {
            $this->emit( $output );
            $content = $this->find_content( $segments );
            if( $content != '' ) {
                $this->emit( '>' );
                $this->process_line( $content );
                $this->emit( '</' . $segments[1] . '>' );
            }
            else
                $this->emit( ' />' );
        }
        else {
            $output .= '>';
            $this->emit( $output );
            $this->stack_tag( $segments[1], $output );
        }
    }

    private function br_tag( &$segments )
    {
        if( $this->has_content( $segments ) ) {
            $this->process_line( $this->find_content( $segments ) );
        }
        $this->emit( "<br />" );
    }

    private function div_class( $line )
    {
        $segments = $this->segment( $line );
        if( count( $segments ) >= 3 && $segments[count($segments)-2] == ':' ) {
            $this->emit( "<span class={$segments[1]}>" );
            $this->process_line( $segments[count($segments)-1] );
            $this->emit( "</span>" );
        }
        else {
            $output = "<div class={$segments[1]}>";
            $this->stack_tag( 'div', $output );
            $this->emit( $output );
        }
    }

    private function call_func( $fname, $optional_separator, $parameter_string )
    {
        if( $optional_separator == '' && strpos( $parameter_string, ',' ) !== false )
            $optional_separator = ',';

        $parameters = array();
        if( $optional_separator == '' )
            $parameters = preg_split( '/\s+/', trim( $parameter_string ) );
        else {
            $parameters = explode( $optional_separator, $parameter_string );
            foreach( $parameters as &$sub )
                $sub = trim( $sub );
        }

        $result = $this->invoke_call_func( $fname, $parameters );
        if( isset( $result ) && $result != '' )
            $this->emit( $result );
    }

    private function invoke_call_func( $fname, $parameters )
    {
        switch( count( $parameters ) ) {
            case 0: return $fname();
            case 1: return $fname( $parameters[0] );
            case 2: return $fname( $parameters[0], $parameters[1] );
            case 3: return $fname( $parameters[0], $parameters[1], $parameters[2] );
            case 4: return $fname( $parameters[0], $parameters[1], $parameters[2], $parameters[3] );
            case 5: return $fname( $parameters[0], $parameters[1], $parameters[2], $parameters[3], $parameters[4] );
        }
        return $fname();
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
        else if( $segments[1] == 'br' )
            return $this->br_args( $segments );
        return '';
    }

    private function address_args( &$segments )
    {
        $output = '';
        $href = $this->find_token( $segments );
        if( $href == '' ) {
            $content = $this->find_content( $segments );
            if( preg_match( '|^\s*https?://|', $content ) )
                $href = trim( $content );
        }
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
        return count( $segments ) >= 3 && $segments[count($segments)-2] == ':';
    }

    private function find_content( &$segments )
    {
        if( $this->has_content( $segments ) )
            return $segments[count($segments)-1];
        return '';
    }

    static $non_stackable_tags = array( 'img', 'br', 'hr' );

    private function stack_tag( $name, $tag )
    {
        if( ! in_array( $name, self::$non_stackable_tags ) ) {
            $this->tag_stack[] = array( $name, $tag );
        }
    }

    private function unstack_tag()
    {
        if( count( $this->tag_stack ) > 0 ) {
            $top = array_pop( $this->tag_stack );
            return $top[0];
        }
        return '';
    }

    private function peek_stack_tag_pair()
    {
        if( count( $this->tag_stack ) > 0 ) {
            return $this->tag_stack[count( $this->tag_stack )-1];
        }
        return array( '', '' );
    }

    private function remove_stack_tag( $tag )
    {
        if( count( $this->tag_stack ) > 0 && $this->tag_stack[count( $this->tag_stack )-1][0] == $tag ) {
            array_pop( $this->tag_stack );
        }
    }
}

class HtmloEcho extends HtmloCore
{
    protected function emit( $output )
    {
        echo( $output );
    }
}

class HtmloString extends HtmloCore
{
    private $output_array = array();

    protected function emit( $output )
    {
        $this->output_array[] = $output;
    }

    public function htmls( $input )
    {
        $this->htmlo( $input );
        return implode( $this->output_array );
    }
}

}   // End of namespace CL

namespace { // Global namespace

function htmlo( $input )
{
    $h = new CL\HtmloEcho();
    $h->htmlo( $input );
}

function htmls( $input )
{
    $h = new CL\HtmloString();
    return $h->htmls( $input );
}

}   // End of global namespace
?>
