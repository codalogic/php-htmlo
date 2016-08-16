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

check( htmls('  ...foo'), '  </foo><foo>' );

check( htmls(".table\n.."), "<table>\n</table>" );
check( htmls(".table\n    .."), "<table>\n    </table>" );

report();
?>
