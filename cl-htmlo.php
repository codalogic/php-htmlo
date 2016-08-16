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

    private $nesting = array();
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
        if( preg_match( '/^(\s*)\.#\s*(.*)/', $line, $matches ) ) {     // Comments : .#
            return $matches[1] . "<!-- " . $matches[2] . " -->";
        }
        else if( preg_match( '/^(\s*)\.\.\.\s*(\w\S+)(.*)/', $line, $matches ) ) {   // End followed by start tag : ...[a-z]
            return $matches[1] . "</" . $matches[2] . ">" . $this->tag( $matches[2] . $matches[3] );
        }
        else if( preg_match( '/^(\s*)\.\.\s*(.*)/', $line, $matches ) ) {   // End tags : .. tag
            return $matches[1] . "</" . $matches[2] . ">";
        }
        else if( preg_match( '/^(\s*)\.\s*(\w.*)/', $line, $matches ) ) {   // Start tags : .[a-z]
            return $matches[1] . $this->tag( $matches[2] );
        }
        else if( preg_match( '/^(\s*)\.\s*(\'.*)/', $line, $matches ) ) {   // class : .'
            return $matches[1] . $this->div_class( $matches[2] );
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
		}
		return $output;
    }

    private function div_class( $line )
    {
		$segments = $this->segment( $line );
		if( count( $segments >= 3 ) && $segments[count($segments)-2] == ':' ) {
			return "<span class={$segments[1]}>" . $this->process_line( $segments[count($segments)-1] ) . "</span>";
		}
		return "<div class={$segments[1]}>";
    }

    private function segment( $line )
    {
        $segments = array();
        $this->peel( $segments, $line, '\w+', self::TAG );
        while( $this->peel( $segments, $line, '\w+\([^)]*\)', self::NAMED ) ||
				$this->peel( $segments, $line, '\d+', self::NUMBER ) ||
				$this->peel( $segments, $line, '\w[^:\s]*', self::TOKEN ) ||
				$this->peel( $segments, $line, '\'[^\']+\'', self::CSSCLASS ) ) {
        }
		if( $this->peel( $segments, $line, ':' ) )
            $segments[] = ltrim( $line );
		return $segments;
    }

    private function peel( &$segments, &$line, $pattern, $cat = '' )
    {
        if( preg_match( '/^\s*(' . $pattern . ')/', $line, $matches ) ) {
            if( $cat != '' )
				$segments[] = $cat;
            $segments[] = $matches[1];
            $line = preg_replace( '/^\s*' . $pattern . '/', '', $line );
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
		return '';
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
