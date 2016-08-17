<?php
include( '../cl-htmlo.php' );
include( 'cl-phpunit.php' );

check( htmls('ab'), 'ab' );
check( htmls("ab\ncd"), "ab\ncd" );

check( htmls('.# foo'), '<!-- foo -->' );
check( htmls("ab\n .# foo"), "ab\n <!-- foo -->" );
check( htmls('  .# foo'), '  <!-- foo -->' );

check( htmls('.. foo'), '</foo>' );
check( htmls('  .. foo'), '  </foo>' );

check( htmls('.foo'), '<foo>' );
check( htmls('.foo: bar'), '<foo>bar</foo>' );
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

check( htmls('.a ./ : foo'), "<a href='./'>foo</a>" );
check( htmls('.a /index.php : foo'), "<a href='/index.php'>foo</a>" );
check( htmls(".a 'bold' /index.php : foo"), "<a class='bold' href='/index.php'>foo</a>" );

check( htmls(".a 'bold' /index.php : .img images/src.png 0:"), "<a class='bold' href='/index.php'><img src='images/src.png' border='0' /></a>" );

check( htmls('  ...foo'), "  </foo>\n  <foo>" );

check( htmls(".table\n.."), "<table>\n</table>" );
check( htmls(".table\n    .."), "<table>\n    </table>" );
check( htmls(".table 'center'\n    .."), "<table class='center'>\n    </table>" );

check( htmls(".'center'\n.."), "<div class='center'>\n</div>" );
check( htmls(".'center'\n.p\n..\n.."), "<div class='center'>\n<p>\n</p>\n</div>" );
check( htmls(".'center'\n.p\n..p\n.."), "<div class='center'>\n<p>\n</p>\n</div>" );
check( htmls(".'center'\n.p\n  ...p\n..\n.."), "<div class='center'>\n<p>\n  </p>\n  <p>\n</p>\n</div>" );

check( htmls(".'center'\n...'left'\n.."), "<div class='center'>\n</div>\n<div class='left'>\n</div>" );

check( htmls("Start\n.!wibble\nEnd"), "Start\nwobble\nEnd" );
$in = 'huff';
$out = 'puff';
check( htmls("Start\n.!wibble1 $in\nEnd"), "Start\nwobblehuff\nEnd" );
check( htmls("Start\n.!wibble2 $in $out\nEnd"), "Start\nwobblehuffpuff\nEnd" );
check( htmls("Start\n.!wibble3 $in $out f3\nEnd"), "Start\nwobblehuffpufff3\nEnd" );
check( htmls("Start\n.!wibble4 $in $out f3 f4\nEnd"), "Start\nwobblehuffpufff3f4\nEnd" );
check( htmls("Start\n.!wibble5 $in $out f3 f4 f5\nEnd"), "Start\nwobblehuffpufff3f4f5\nEnd" );

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

report();
?>
