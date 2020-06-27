<?php
$fails = $tests = 0;

function check( $a, $b )
{
    global $fails, $tests;
    $tests++;

    if( $a === $b ) {
        print( "    ok: $a\n" );
    }
    else {
        print( "Not ok: $a\n" .
               "        $b\n" );
        $fails++;
    }
}

function checkrtrim( $a, $b )
{
    check( rtrim( $a ), rtrim( $b ) );
}

function failed( $reason )
{
    global $fails, $tests;
    $tests++;
    $fails++;
    print( "Not ok: $reason\n" );
}

function report()
{
    global $fails, $tests;
    print( "$fails fails, $tests tests\n" );
}
?>
