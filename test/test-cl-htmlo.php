<?php
include( '../cl-htmlo.php' );
include( 'cl-phpunit.php' );

check( htmls('ab'), 'ab' );
check( htmls("ab\ncd"), "ab\ncd" );
check( htmls("ab\n<i>cd</i>"), "ab\n<i>cd</i>" );

check( htmls('.# foo'), '<!-- foo -->' );
check( htmls("ab\n .# foo"), "ab\n <!-- foo -->" );
check( htmls('  .# foo'), '  <!-- foo -->' );

check( htmls('.- foo'), '' );
check( htmls("ab\n .- foo\ncd"), "ab\ncd\n" );
check( htmls('  .- foo'), '' );

check( htmls('.. foo'), '</foo>' );
check( htmls('  .. foo'), '  </foo>' );

check( htmls('.foo'), '<foo>' );
check( htmls('.foo: bar'), '<foo>bar</foo>' );
check( htmls('.foo: bar '), '<foo>bar</foo>' );
check( htmls('.i: .b: bar'), '<i><b>bar</b></i>' );

check( htmls(".foo 'center left': bar"), "<foo class='center left'>bar</foo>" );

check( htmls(".'center'"), "<div class='center'>" );
check( htmls(".'center' : special"), "<span class='center'>special</span>" );
check( htmls(".'center bold' : special"), "<span class='center bold'>special</span>" );

check( htmls('.img alt( a minor issue )'), "<img alt='a minor issue'>" );
check( htmls('.img alt( a minor issue ):'), "<img alt='a minor issue' />" );
check( htmls('.img images/src.png:'), "<img src='images/src.png' />" );
check( htmls('.img images/src.png 0:'), "<img src='images/src.png' border='0' />" );
check( htmls('.img images/src.png 0 alt( a minor issue ):'), "<img src='images/src.png' border='0' alt='a minor issue' />" );
check( htmls('.img src(images/src.png):'), "<img src='images/src.png' />" );

check( htmls('.a ./ : foo'), "<a href='./'>foo</a>" );
check( htmls('.a /index.php : foo'), "<a href='/index.php'>foo</a>" );
check( htmls(".a 'bold' /index.php : foo"), "<a class='bold' href='/index.php'>foo</a>" );
check( htmls(".a http://codalogic.com/index.php : foo"), "<a href='http://codalogic.com/index.php'>foo</a>" );
check( htmls(".a https://codalogic.com/index.php: foo"), "<a href='https://codalogic.com/index.php'>foo</a>" );
check( htmls(".a mailto:nowhere@example.com: foo"), "<a href='mailto:nowhere@example.com'>foo</a>" );
check( htmls(".a href( http://codalogic.com/index.php ) : foo"), "<a href='http://codalogic.com/index.php'>foo</a>" );
check( htmls(".a # : foo"), "<a href='#'>foo</a>" );
check( htmls(".a #bar : foo"), "<a href='#bar'>foo</a>" );

check( htmls(".a name(bar) :"), "<a name='bar' />" );

check( htmls(".a 'bold' /index.php : .img images/src.png 0:"), "<a class='bold' href='/index.php'><img src='images/src.png' border='0' /></a>" );

check( htmls('  ...foo'), "  </foo>\n  <foo>" );

check( htmls(".table\n.."), "<table>\n</table>" );
check( htmls(".table\n    .."), "<table>\n    </table>" );
check( htmls(".table 'center'\n    .."), "<table class='center'>\n    </table>" );

check( htmls(".'center'\n.."), "<div class='center'>\n</div>" );
check( htmls(".'center'\n.p\n..\n.."), "<div class='center'>\n<p>\n</p>\n</div>" );
check( htmls(".'center'\n.p\n..p\n.."), "<div class='center'>\n<p>\n</p>\n</div>" );
check( htmls(".'center'\n.p\n  ...p\n..\n.."), "<div class='center'>\n<p>\n  </p>\n  <p>\n</p>\n</div>" );

check( htmls(".'center'\n.p\n  ...\n..\n.."), "<div class='center'>\n<p>\n  </p>\n  <p>\n</p>\n</div>" );

check( htmls(".'center'\n...'left'\n.."), "<div class='center'>\n</div>\n<div class='left'>\n</div>" );

check( htmls("Start\n.: if( a < b && b <= c )\nEnd"), "Start\n if( a &lt; b &amp;&amp; b &lt;= c )\nEnd" );
check( htmls(".: .p: stuff"), " .p: stuff" );

check( htmls("Start\n.!wibble\nEnd"), "Start\nwobble\nEnd" );
check( htmls("Start\n.! wibble\nEnd"), "Start\nwobble\nEnd" );
check( htmls("Start\n. !wibble\nEnd"), "Start\nwobble\nEnd" );
$in = 'huff';
$out = 'puff';
check( htmls("Start\n.!wibble1 $in\nEnd"), "Start\nwobblehuff\nEnd" );
check( htmls("Start\n.!wibble2 $in $out\nEnd"), "Start\nwobblehuffpuff\nEnd" );
check( htmls("Start\n.!wibble3 $in $out f3\nEnd"), "Start\nwobblehuffpufff3\nEnd" );
check( htmls("Start\n.!wibble4 $in $out f3 f4\nEnd"), "Start\nwobblehuffpufff3f4\nEnd" );
check( htmls("Start\n.!wibble5 $in $out f3 f4 f5\nEnd"), "Start\nwobblehuffpufff3f4f5\nEnd" );
check( htmls("Start\n.!!wibble1 $in\nEnd"), "Start\nwobblehuff\nEnd" );
check( htmls("Start\n.!!wibble2 $in ! $out\nEnd"), "Start\nwobblehuffpuff\nEnd" );
check( htmls("Start\n.!!wibble3 $in ! $out ! f3\nEnd"), "Start\nwobblehuffpufff3\nEnd" );
check( htmls("Start\n.!!wibble4 $in ! $out ! f3 ! f4\nEnd"), "Start\nwobblehuffpufff3f4\nEnd" );
check( htmls("Start\n.!!wibble5 $in ! $out ! f3 ! f4 ! f5\nEnd"), "Start\nwobblehuffpufff3f4f5\nEnd" );
check( htmls("Start\n.!!wibble1 This is a string \nEnd"), "Start\nwobbleThis is a string\nEnd" );
check( htmls("Start\n.!!wibble2 This is a string ! 2 \nEnd"), "Start\nwobbleThis is a string2\nEnd" );
check( htmls("Start\n.!/wibble1 $in\nEnd"), "Start\nwobblehuff\nEnd" );
check( htmls("Start\n.!/wibble2 $in / $out\nEnd"), "Start\nwobblehuffpuff\nEnd" );
check( htmls("Start\n.!/wibble3 $in / $out / f3\nEnd"), "Start\nwobblehuffpufff3\nEnd" );
check( htmls("Start\n.!/wibble4 $in / $out / f3 / f4\nEnd"), "Start\nwobblehuffpufff3f4\nEnd" );
check( htmls("Start\n.!/wibble5 $in / $out / f3 / f4 / f5\nEnd"), "Start\nwobblehuffpufff3f4f5\nEnd" );
check( htmls("Start\n.!/wibble1 This is a string \nEnd"), "Start\nwobbleThis is a string\nEnd" );
check( htmls("Start\n.!/wibble2 This is a string / 2 \nEnd"), "Start\nwobbleThis is a string2\nEnd" );

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

check( htmls("  .|: .i: A | .b: B"), "  <i>A</i><b>B</b>" );
check( htmls("  .|^: .i: A ^ .b: B ^ .u: U"), "  <i>A</i><b>B</b><u>U</u>" );
check( htmls("  .|^^: .i: A ^^ .b: B ^^ .u: U"), "  <i>A</i><b>B</b><u>U</u>" );
check( htmls("  .tr: .|: .td: A | .td: B | .td: C"), "  <tr><td>A</td><td>B</td><td>C</td></tr>" );
check( htmls("  .tr: .|~~: .td: A ~~ .td: B ~~ .td: C"), "  <tr><td>A</td><td>B</td><td>C</td></tr>" );
check( htmls("  .|: .i: A | | .b: B"), "  <i>A</i> <b>B</b>" );

check( htmls("  .|: This is text | .!wibble | More text"), "  This is textwobbleMore text" );
check( htmls("  .|: This is text | | .!wibble | | More text"), "  This is text wobble More text" );

report();
?>
