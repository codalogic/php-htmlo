<?php
include( '../cl-htmlo.php' );
include( 'cl-phpunit.php' );

checkrtrim( htmls( 'ab' ), 'ab' );
checkrtrim( htmls( "ab\ncd" ), "ab\ncd" );
checkrtrim( htmls( "ab\n<i>cd</i>" ), "ab\n<i>cd</i>" );

checkrtrim( htmls( '.# foo' ), '<!-- foo -->' );
checkrtrim( htmls( "ab\n .# foo" ), "ab\n <!-- foo -->" );
checkrtrim( htmls( '  .# foo' ), '  <!-- foo -->' );

checkrtrim( htmls( '.- foo' ), '' );
checkrtrim( htmls( "ab\n .- foo\ncd" ), "ab\ncd" );
checkrtrim( htmls( '  .- foo' ), '' );

checkrtrim( htmls( '.. foo' ), '</foo>' );
checkrtrim( htmls( '  .. foo' ), '  </foo>' );

checkrtrim( htmls( '.foo' ), '<foo>' );
checkrtrim( htmls( '. foo' ), '<foo>' );
checkrtrim( htmls( '.foo: bar' ), '<foo>bar</foo>' );
checkrtrim( htmls( '.foo: bar ' ), '<foo>bar</foo>' );
checkrtrim( htmls( '.i: .b: bar' ), '<i><b>bar</b></i>' );

checkrtrim( htmls( ".foo 'center left': bar" ), "<foo class='center left'>bar</foo>" );

checkrtrim( htmls( ".'center'" ), "<div class='center'>" );
checkrtrim( htmls( ". 'center'" ), "<div class='center'>" );
checkrtrim( htmls( ".'center' : special" ), "<span class='center'>special</span>" );
checkrtrim( htmls( ".'center bold' : special" ), "<span class='center bold'>special</span>" );

checkrtrim( htmls( '.img alt( a minor issue )' ), "<img alt='a minor issue'>" );
checkrtrim( htmls( '.img alt( a minor issue ):' ), "<img alt='a minor issue' />" );
checkrtrim( htmls( '.img images/src.png:' ), "<img src='images/src.png' />" );
checkrtrim( htmls( '.img images/src.png 0:' ), "<img src='images/src.png' border='0' />" );
checkrtrim( htmls( '.img images/src.png 0 alt( a minor issue ):' ), "<img src='images/src.png' border='0' alt='a minor issue' />" );
checkrtrim( htmls( '.img src(images/src.png):' ), "<img src='images/src.png' />" );

checkrtrim( htmls( '.a ./ : foo' ), "<a href='./'>foo</a>" );
checkrtrim( htmls( '.a /index.php : foo' ), "<a href='/index.php'>foo</a>" );
checkrtrim( htmls( ".a 'bold' /index.php : foo" ), "<a class='bold' href='/index.php'>foo</a>" );
checkrtrim( htmls( ".a http://codalogic.com/index.php : foo" ), "<a href='http://codalogic.com/index.php'>foo</a>" );
checkrtrim( htmls( ".a https://codalogic.com/index.php: foo" ), "<a href='https://codalogic.com/index.php'>foo</a>" );
checkrtrim( htmls( ".a mailto:nowhere@example.com: foo" ), "<a href='mailto:nowhere@example.com'>foo</a>" );
checkrtrim( htmls( ".a href( http://codalogic.com/index.php ) : foo" ), "<a href='http://codalogic.com/index.php'>foo</a>" );
checkrtrim( htmls( ".a # : foo" ), "<a href='#'>foo</a>" );
checkrtrim( htmls( ".a #bar : foo" ), "<a href='#bar'>foo</a>" );
checkrtrim( htmls( ".a: http://codalogic.com " ), "<a href='http://codalogic.com'>http://codalogic.com</a>" );
checkrtrim( htmls( ".a: https://codalogic.com " ), "<a href='https://codalogic.com'>https://codalogic.com</a>" );
checkrtrim( htmls( ".a: Not a URL " ), "<a>Not a URL</a>" );

checkrtrim( htmls( ".a name(bar) :" ), "<a name='bar' />" );

checkrtrim( htmls( ".a 'bold' /index.php : .img images/src.png 0:" ), "<a class='bold' href='/index.php'><img src='images/src.png' border='0' /></a>" );

checkrtrim( htmls( ".br:" ), "<br />" );
checkrtrim( htmls( ".br: A line" ), "A line<br />" );
checkrtrim( htmls( ".br: .a http://codalogic.com: .img images/logo.png alt( Company logo ):" ), "<a href='http://codalogic.com'><img src='images/logo.png' alt='Company logo' /></a><br />" );

checkrtrim( htmls( '  ...foo' ), "  </foo>\n  <foo>" );

checkrtrim( htmls( ".table\n.." ), "<table>\n</table>" );
checkrtrim( htmls( ".table\n    .." ), "<table>\n    </table>" );
checkrtrim( htmls( ".table 'center'\n    .." ), "<table class='center'>\n    </table>" );

checkrtrim( htmls( ".'center'\n.." ), "<div class='center'>\n</div>" );
checkrtrim( htmls( ".'center'\n.p\n..\n.." ), "<div class='center'>\n<p>\n</p>\n</div>" );
checkrtrim( htmls( ".'center'\n.p\n..p\n.." ), "<div class='center'>\n<p>\n</p>\n</div>" );
checkrtrim( htmls( ".'center'\n.p\n  ...p\n..\n.." ), "<div class='center'>\n<p>\n  </p>\n  <p>\n</p>\n</div>" );

checkrtrim( htmls( ".'center'\n.p\n  ...\n..\n.." ), "<div class='center'>\n<p>\n  </p>\n  <p>\n</p>\n</div>" );

checkrtrim( htmls( ".'center'\n...\n.." ), "<div class='center'>\n</div>\n<div class='center'>\n</div>" );
checkrtrim( htmls( ".'center'\n...'left'\n.." ), "<div class='center'>\n</div>\n<div class='left'>\n</div>" );

checkrtrim( htmls( ".td valign(top)\n...\n.." ), "<td valign='top'>\n</td>\n<td valign='top'>\n</td>" );

checkrtrim( htmls( "Start\n.: if( a < b && b <= c )\nEnd" ), "Start\n if( a &lt; b &amp;&amp; b &lt;= c )\nEnd" );
checkrtrim( htmls( ".: .p: stuff" ), " .p: stuff" );

checkrtrim( htmls( "Start\n.!wibble\nEnd" ), "Start\nwobble\nEnd" );
checkrtrim( htmls( "Start\n.! wibble\nEnd" ), "Start\nwobble\nEnd" );
$in = 'huff';
$out = 'puff';
checkrtrim( htmls( "Start\n.!wibble1 $in\nEnd" ), "Start\nwobblehuff\nEnd" );
checkrtrim( htmls( "Start\n.!wibble2 $in $out\nEnd" ), "Start\nwobblehuffpuff\nEnd" );
checkrtrim( htmls( "Start\n.!wibble3 $in $out f3\nEnd" ), "Start\nwobblehuffpufff3\nEnd" );
checkrtrim( htmls( "Start\n.!wibble4 $in $out f3 f4\nEnd" ), "Start\nwobblehuffpufff3f4\nEnd" );
checkrtrim( htmls( "Start\n.! wibble5 $in $out f3 f4 f5\nEnd" ), "Start\nwobblehuffpufff3f4f5\nEnd" );
checkrtrim( htmls( "Start\n.!wibble2 This is a string, 2 \nEnd" ), "Start\nwobbleThis is a string2\nEnd" );
checkrtrim( htmls( "Start\n.!!wibble1 $in\nEnd" ), "Start\nwobblehuff\nEnd" );
checkrtrim( htmls( "Start\n.!!wibble2 $in ! $out\nEnd" ), "Start\nwobblehuffpuff\nEnd" );
checkrtrim( htmls( "Start\n.!!wibble3 $in ! $out ! f3\nEnd" ), "Start\nwobblehuffpufff3\nEnd" );
checkrtrim( htmls( "Start\n.!!wibble4 $in ! $out ! f3 ! f4\nEnd" ), "Start\nwobblehuffpufff3f4\nEnd" );
checkrtrim( htmls( "Start\n.!!wibble5 $in ! $out ! f3 ! f4 ! f5\nEnd" ), "Start\nwobblehuffpufff3f4f5\nEnd" );
checkrtrim( htmls( "Start\n.!! wibble1 This is a string \nEnd" ), "Start\nwobbleThis is a string\nEnd" );
checkrtrim( htmls( "Start\n.!! wibble2 This is a string ! 2 \nEnd" ), "Start\nwobbleThis is a string2\nEnd" );
checkrtrim( htmls( "Start\n.!/wibble1 $in\nEnd" ), "Start\nwobblehuff\nEnd" );
checkrtrim( htmls( "Start\n.!/wibble2 $in / $out\nEnd" ), "Start\nwobblehuffpuff\nEnd" );
checkrtrim( htmls( "Start\n.!/wibble3 $in / $out / f3\nEnd" ), "Start\nwobblehuffpufff3\nEnd" );
checkrtrim( htmls( "Start\n.!/wibble4 $in / $out / f3 / f4\nEnd" ), "Start\nwobblehuffpufff3f4\nEnd" );
checkrtrim( htmls( "Start\n.!/wibble5 $in / $out / f3 / f4 / f5\nEnd" ), "Start\nwobblehuffpufff3f4f5\nEnd" );
checkrtrim( htmls( "Start\n.!/ wibble1 This is a string \nEnd" ), "Start\nwobbleThis is a string\nEnd" );
checkrtrim( htmls( "Start\n.!/ wibble2 This is a string / 2 \nEnd" ), "Start\nwobbleThis is a string2\nEnd" );
checkrtrim( htmls( "Start\n.!^^ wibble1 $in\nEnd" ), "Start\nwobblehuff\nEnd" );
checkrtrim( htmls( "Start\n.!^^ wibble2 $in ^^ $out\nEnd" ), "Start\nwobblehuffpuff\nEnd" );
checkrtrim( htmls( "Start\n.!^^ wibble3 $in ^^ $out ^^ f3\nEnd" ), "Start\nwobblehuffpufff3\nEnd" );
checkrtrim( htmls( "Start\n.!^^ wibble4 $in ^^ $out ^^ f3 ^^ f4\nEnd" ), "Start\nwobblehuffpufff3f4\nEnd" );
checkrtrim( htmls( "Start\n.!^^ wibble5 $in ^^ $out ^^ f3 ^^ f4 ^^ f5\nEnd" ), "Start\nwobblehuffpufff3f4f5\nEnd" );
checkrtrim( htmls( "Start\n.!^^wibble1 This is a string \nEnd" ), "Start\nwobbleThis is a string\nEnd" );
checkrtrim( htmls( "Start\n.!^^wibble2 This is a string ^^ 2 \nEnd" ), "Start\nwobbleThis is a string2\nEnd" );

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

checkrtrim( htmls( "  .|: .i: A | .b: B" ), "  <i>A</i><b>B</b>" );
checkrtrim( htmls( "  .|^: .i: A ^ .b: B ^ .u: U" ), "  <i>A</i><b>B</b><u>U</u>" );
checkrtrim( htmls( "  .|^^: .i: A ^^ .b: B ^^ .u: U" ), "  <i>A</i><b>B</b><u>U</u>" );
checkrtrim( htmls( "  .tr: .|: .td: A | .td: B | .td: C" ), "  <tr><td>A</td><td>B</td><td>C</td></tr>" );
checkrtrim( htmls( "  .tr: .|~~: .td: A ~~ .td: B ~~ .td: C" ), "  <tr><td>A</td><td>B</td><td>C</td></tr>" );
checkrtrim( htmls( "  .|: .i: A | | .b: B" ), "  <i>A</i> <b>B</b>" );

checkrtrim( htmls( "  .|: This is text | .!wibble | More text" ), "  This is textwobbleMore text" );
checkrtrim( htmls( "  .|: This is text | | .!wibble | | More text" ), "  This is text wobble More text" );

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
