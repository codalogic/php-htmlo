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

check( htmls('  ...foo'), '  </foo><foo>' );

report();
?>
