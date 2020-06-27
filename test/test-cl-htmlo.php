<?php
include( '../cl-htmlo.php' );
include( 'cl-phpunit.php' );

check( rtrim( htmls( 'ab' ) ), 'ab' );
check( rtrim( htmls( "ab\ncd" ) ), "ab\ncd" );
check( rtrim( htmls( "ab\n<i>cd</i>" ) ), "ab\n<i>cd</i>" );

check( rtrim( htmls( '.# foo' ) ), '<!-- foo -->' );
check( rtrim( htmls( "ab\n .# foo" ) ), "ab\n <!-- foo -->" );
check( rtrim( htmls( '  .# foo' ) ), '  <!-- foo -->' );

check( rtrim( htmls( '.- foo' ) ), '' );
check( rtrim( htmls( "ab\n .- foo\ncd" ) ), "ab\ncd" );
check( rtrim( htmls( '  .- foo' ) ), '' );

check( rtrim( htmls( '.. foo' ) ), '</foo>' );
check( rtrim( htmls( '  .. foo' ) ), '  </foo>' );

check( rtrim( htmls( '.foo' ) ), '<foo>' );
check( rtrim( htmls( '. foo' ) ), '<foo>' );
check( rtrim( htmls( '.foo: bar' ) ), '<foo>bar</foo>' );
check( rtrim( htmls( '.foo: bar ' ) ), '<foo>bar</foo>' );
check( rtrim( htmls( '.i: .b: bar' ) ), '<i><b>bar</b></i>' );

check( rtrim( htmls( ".foo 'center left': bar" ) ), "<foo class='center left'>bar</foo>" );

check( rtrim( htmls( ".'center'" ) ), "<div class='center'>" );
check( rtrim( htmls( ". 'center'" ) ), "<div class='center'>" );
check( rtrim( htmls( ".'center' : special" ) ), "<span class='center'>special</span>" );
check( rtrim( htmls( ".'center bold' : special" ) ), "<span class='center bold'>special</span>" );

check( rtrim( htmls( '.img alt( a minor issue )' ) ), "<img alt='a minor issue'>" );
check( rtrim( htmls( '.img alt( a minor issue ):' ) ), "<img alt='a minor issue' />" );
check( rtrim( htmls( '.img images/src.png:' ) ), "<img src='images/src.png' />" );
check( rtrim( htmls( '.img images/src.png 0:' ) ), "<img src='images/src.png' border='0' />" );
check( rtrim( htmls( '.img images/src.png 0 alt( a minor issue ):' ) ), "<img src='images/src.png' border='0' alt='a minor issue' />" );
check( rtrim( htmls( '.img src(images/src.png):' ) ), "<img src='images/src.png' />" );

check( rtrim( htmls( '.a ./ : foo' ) ), "<a href='./'>foo</a>" );
check( rtrim( htmls( '.a /index.php : foo' ) ), "<a href='/index.php'>foo</a>" );
check( rtrim( htmls( ".a 'bold' /index.php : foo" ) ), "<a class='bold' href='/index.php'>foo</a>" );
check( rtrim( htmls( ".a http://codalogic.com/index.php : foo" ) ), "<a href='http://codalogic.com/index.php'>foo</a>" );
check( rtrim( htmls( ".a https://codalogic.com/index.php: foo" ) ), "<a href='https://codalogic.com/index.php'>foo</a>" );
check( rtrim( htmls( ".a mailto:nowhere@example.com: foo" ) ), "<a href='mailto:nowhere@example.com'>foo</a>" );
check( rtrim( htmls( ".a href( http://codalogic.com/index.php ) : foo" ) ), "<a href='http://codalogic.com/index.php'>foo</a>" );
check( rtrim( htmls( ".a # : foo" ) ), "<a href='#'>foo</a>" );
check( rtrim( htmls( ".a #bar : foo" ) ), "<a href='#bar'>foo</a>" );
check( rtrim( htmls( ".a: http://codalogic.com " ) ), "<a href='http://codalogic.com'>http://codalogic.com</a>" );
check( rtrim( htmls( ".a: https://codalogic.com " ) ), "<a href='https://codalogic.com'>https://codalogic.com</a>" );
check( rtrim( htmls( ".a: Not a URL " ) ), "<a>Not a URL</a>" );

check( rtrim( htmls( ".a name(bar) :" ) ), "<a name='bar' />" );

check( rtrim( htmls( ".a 'bold' /index.php : .img images/src.png 0:" ) ), "<a class='bold' href='/index.php'><img src='images/src.png' border='0' /></a>" );

check( rtrim( htmls( '  ...foo' ) ), "  </foo>\n  <foo>" );

check( rtrim( htmls( ".table\n.." ) ), "<table>\n</table>" );
check( rtrim( htmls( ".table\n    .." ) ), "<table>\n    </table>" );
check( rtrim( htmls( ".table 'center'\n    .." ) ), "<table class='center'>\n    </table>" );

check( rtrim( htmls( ".'center'\n.." ) ), "<div class='center'>\n</div>" );
check( rtrim( htmls( ".'center'\n.p\n..\n.." ) ), "<div class='center'>\n<p>\n</p>\n</div>" );
check( rtrim( htmls( ".'center'\n.p\n..p\n.." ) ), "<div class='center'>\n<p>\n</p>\n</div>" );
check( rtrim( htmls( ".'center'\n.p\n  ...p\n..\n.." ) ), "<div class='center'>\n<p>\n  </p>\n  <p>\n</p>\n</div>" );

check( rtrim( htmls( ".'center'\n.p\n  ...\n..\n.." ) ), "<div class='center'>\n<p>\n  </p>\n  <p>\n</p>\n</div>" );

check( rtrim( htmls( ".'center'\n...'left'\n.." ) ), "<div class='center'>\n</div>\n<div class='left'>\n</div>" );

check( rtrim( htmls( "Start\n.: if( a < b && b <= c )\nEnd" ) ), "Start\n if( a &lt; b &amp;&amp; b &lt;= c )\nEnd" );
check( rtrim( htmls( ".: .p: stuff" ) ), " .p: stuff" );

check( rtrim( htmls( "Start\n.!wibble\nEnd" ) ), "Start\nwobble\nEnd" );
check( rtrim( htmls( "Start\n.! wibble\nEnd" ) ), "Start\nwobble\nEnd" );
$in = 'huff';
$out = 'puff';
check( rtrim( htmls( "Start\n.!wibble1 $in\nEnd" ) ), "Start\nwobblehuff\nEnd" );
check( rtrim( htmls( "Start\n.!wibble2 $in $out\nEnd" ) ), "Start\nwobblehuffpuff\nEnd" );
check( rtrim( htmls( "Start\n.!wibble3 $in $out f3\nEnd" ) ), "Start\nwobblehuffpufff3\nEnd" );
check( rtrim( htmls( "Start\n.!wibble4 $in $out f3 f4\nEnd" ) ), "Start\nwobblehuffpufff3f4\nEnd" );
check( rtrim( htmls( "Start\n.! wibble5 $in $out f3 f4 f5\nEnd" ) ), "Start\nwobblehuffpufff3f4f5\nEnd" );
check( rtrim( htmls( "Start\n.!!wibble1 $in\nEnd" ) ), "Start\nwobblehuff\nEnd" );
check( rtrim( htmls( "Start\n.!!wibble2 $in ! $out\nEnd" ) ), "Start\nwobblehuffpuff\nEnd" );
check( rtrim( htmls( "Start\n.!!wibble3 $in ! $out ! f3\nEnd" ) ), "Start\nwobblehuffpufff3\nEnd" );
check( rtrim( htmls( "Start\n.!!wibble4 $in ! $out ! f3 ! f4\nEnd" ) ), "Start\nwobblehuffpufff3f4\nEnd" );
check( rtrim( htmls( "Start\n.!!wibble5 $in ! $out ! f3 ! f4 ! f5\nEnd" ) ), "Start\nwobblehuffpufff3f4f5\nEnd" );
check( rtrim( htmls( "Start\n.!! wibble1 This is a string \nEnd" ) ), "Start\nwobbleThis is a string\nEnd" );
check( rtrim( htmls( "Start\n.!! wibble2 This is a string ! 2 \nEnd" ) ), "Start\nwobbleThis is a string2\nEnd" );
check( rtrim( htmls( "Start\n.!/wibble1 $in\nEnd" ) ), "Start\nwobblehuff\nEnd" );
check( rtrim( htmls( "Start\n.!/wibble2 $in / $out\nEnd" ) ), "Start\nwobblehuffpuff\nEnd" );
check( rtrim( htmls( "Start\n.!/wibble3 $in / $out / f3\nEnd" ) ), "Start\nwobblehuffpufff3\nEnd" );
check( rtrim( htmls( "Start\n.!/wibble4 $in / $out / f3 / f4\nEnd" ) ), "Start\nwobblehuffpufff3f4\nEnd" );
check( rtrim( htmls( "Start\n.!/wibble5 $in / $out / f3 / f4 / f5\nEnd" ) ), "Start\nwobblehuffpufff3f4f5\nEnd" );
check( rtrim( htmls( "Start\n.!/ wibble1 This is a string \nEnd" ) ), "Start\nwobbleThis is a string\nEnd" );
check( rtrim( htmls( "Start\n.!/ wibble2 This is a string / 2 \nEnd" ) ), "Start\nwobbleThis is a string2\nEnd" );
check( rtrim( htmls( "Start\n.!^^ wibble1 $in\nEnd" ) ), "Start\nwobblehuff\nEnd" );
check( rtrim( htmls( "Start\n.!^^ wibble2 $in ^^ $out\nEnd" ) ), "Start\nwobblehuffpuff\nEnd" );
check( rtrim( htmls( "Start\n.!^^ wibble3 $in ^^ $out ^^ f3\nEnd" ) ), "Start\nwobblehuffpufff3\nEnd" );
check( rtrim( htmls( "Start\n.!^^ wibble4 $in ^^ $out ^^ f3 ^^ f4\nEnd" ) ), "Start\nwobblehuffpufff3f4\nEnd" );
check( rtrim( htmls( "Start\n.!^^ wibble5 $in ^^ $out ^^ f3 ^^ f4 ^^ f5\nEnd" ) ), "Start\nwobblehuffpufff3f4f5\nEnd" );
check( rtrim( htmls( "Start\n.!^^wibble1 This is a string \nEnd" ) ), "Start\nwobbleThis is a string\nEnd" );
check( rtrim( htmls( "Start\n.!^^wibble2 This is a string ^^ 2 \nEnd" ) ), "Start\nwobbleThis is a string2\nEnd" );

echo "Visually check echo is: Start\nwobblehuffpufff3f4f5\nEnd\n";
htmlo("Start\n.!wibble5 $in $out f3 f4 f5\nEnd\n");
echo "Visually check echo is: Start\nwobbleechohuffpufff3f4f5\nEnd\n";
htmlo("Start\n.!wibble5echo $in $out f3 f4 f5\nEnd\n");

function wibble()
{
    return 'wobble';
}

function wibble1( $what )
{
    return 'wobble' . $what;
}

function wibble2( $what1, $what2 )
{
    return 'wobble' . $what1 . $what2;
}

function wibble3( $what1, $what2, $what3 )
{
    return 'wobble' . $what1 . $what2 . $what3;
}

function wibble4( $what1, $what2, $what3, $what4 )
{
    return 'wobble' . $what1 . $what2 . $what3 . $what4;
}

function wibble5( $what1, $what2, $what3, $what4, $what5 )
{
    return 'wobble' . $what1 . $what2 . $what3 . $what4 . $what5;
}

function wibble5echo( $what1, $what2, $what3, $what4, $what5 )
{
    echo( 'wobbleecho' . $what1 . $what2 . $what3 . $what4 . $what5 . "\n" );
}

check( rtrim( htmls( "  .|: .i: A | .b: B" ) ), "  <i>A</i><b>B</b>" );
check( rtrim( htmls( "  .|^: .i: A ^ .b: B ^ .u: U" ) ), "  <i>A</i><b>B</b><u>U</u>" );
check( rtrim( htmls( "  .|^^: .i: A ^^ .b: B ^^ .u: U" ) ), "  <i>A</i><b>B</b><u>U</u>" );
check( rtrim( htmls( "  .tr: .|: .td: A | .td: B | .td: C" ) ), "  <tr><td>A</td><td>B</td><td>C</td></tr>" );
check( rtrim( htmls( "  .tr: .|~~: .td: A ~~ .td: B ~~ .td: C" ) ), "  <tr><td>A</td><td>B</td><td>C</td></tr>" );
check( rtrim( htmls( "  .|: .i: A | | .b: B" ) ), "  <i>A</i> <b>B</b>" );

check( rtrim( htmls( "  .|: This is text | .!wibble | More text" ) ), "  This is textwobbleMore text" );
check( rtrim( htmls( "  .|: This is text | | .!wibble | | More text" ) ), "  This is text wobble More text" );

test_block_ignores();
    $is_block_comment_called = false;
    function test_block_ignores()
    {
        function do_not_call() { failed( 'Should not be called do_not_call()' ); }
        function must_call() { global $is_block_comment_called; $is_block_comment_called = true; }

        // Test ./* ... .*/ ignore block
        global $is_block_comment_called;
        $is_block_comment_called = false;
        htmlo("Start\n./*\n.!do_not_call\n.*/\n.!must_call");
        if( ! $is_block_comment_called )
            failed( 'Should have called must_call()' );

        // Test nested ./* ... .*/ ignore block
        global $is_block_comment_called;
        $is_block_comment_called = false;
        htmlo("Start\n" .
                "./*\n" .
                    ".!do_not_call\n" .
                    "./*\n" .
                        ".!do_not_call\n" .
                    ".*/\n" .
                    ".!do_not_call\n" .
                ".*/\n" .
                ".!must_call");
        if( ! $is_block_comment_called )
            failed( 'Should have called must_call()' );

        // Test multiple stars with nested ./**** ... .*****/ ignore block
        global $is_block_comment_called;
        $is_block_comment_called = false;
        htmlo("Start\n" .
                "./****\n" .
                    ".!do_not_call\n" .
                    "./***\n" .
                        ".!do_not_call\n" .
                    ".***/\n" .
                    ".!do_not_call\n" .
                ".****/\n" .
                ".!must_call");
        if( ! $is_block_comment_called )
            failed( 'Should have called must_call()' );

        // Test multiple stars with alphanum sequence with nested ./**_a1_** ... .***_a1_**/ ignore block
        global $is_block_comment_called;
        $is_block_comment_called = false;
        htmlo("Start\n" .
                "./**_Outer1_**\n" .
                    ".!do_not_call\n" .
                    "./**_Inner_*\n" .
                        ".!do_not_call\n" .
                    ".***_Inner_/\n" .
                    ".!do_not_call\n" .
                ".**_Outer1_**/\n" .
                ".!must_call");
        if( ! $is_block_comment_called )
            failed( 'Should have called must_call()' );
    }

report();
?>
