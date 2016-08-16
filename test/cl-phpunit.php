<?php
$fails = $tests = 0;

function check( $a, $b )
{
    global $fails, $tests;
    $tests++;

    $a = rtrim( $a );
    $b = rtrim( $b );
    if( $a === $b ) {
        print( "    ok: $a\n" );
    }
    else {
        print( "Not ok: $a\n" .
               "        $b\n" );
        $fails++;
    }
}

function report()
{
    global $fails, $tests;
    print( "$fails fails, $tests tests\n" );
}
?>
