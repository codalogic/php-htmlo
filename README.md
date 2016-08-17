# php-htmlo

## Introduction

`htmlo` is for those people that find writing HTML tedious.  It provides
a short-hand way of writing HTML, but allows for richer markup than
techniques such as markdown.

For example:

    htmlo( '.h1: Hello, World!' );

will generate:

    <h1>Hello, World!</h1>

## Usage

Include `cl-htmlo.php` in the file that wants to use it.  An `exodep` file
for this is available at `exodep-exports/php-htmlo.exodep` which can be
copied and modified to your project to facilitate updates.

## Detail

`htmlo` has two main functions.  `htmlo()` echos the generated string to the
output, and `htmls()` returns the generated string - as a string.

In this overview the examples will talk about the string that is input to
either `htmlo()` or `htmls()` rather than expliciting showing these function
calls in the examples.  Thus the initial example shown above would be shown
as:

    .h1: Hello, World!

`htmlo` directives are line based.  `htmlo` directive lines consist of leading
whitespace followed by a `.`.  Any non-directive line is output unmodified.
HTML can be mixed into the output as needed (just by including it in the
input string).

For example:

    .p

will yield:

    <p>

Directives can optionally have content that follows a `:`. For example:

    .p: My paragraph

will yield:

    <p>My paragraph</p>

Note that when a directive contains content it is automatically closed.

The content can in fact be another directive.  For example:

    .i:.b: SHOUT

yields:

    <i><b>SHOUT</b></i>

With CSS, classes on tags are common.  A class is specified within single
quotes, for example:

    .p 'big': My para

will yield:

    <p class='big'>My para</p>

`div` and `span` with classes have special forms.  For example:

    .'my-class'

yields:

    <div class='my-class'>

and:

    .'my-class': important

yields:

    <span class='my-class'>important</span>

Two dots will create an end tag.  For example:

    ..p

will create:

    </p>

`htmlo` attempts to keep track of tag nesting, so two dots on their own
close the last opened tag.  For example:

    .p
    Text
    ..

will yield:

    <p>
    Text
    </p>

Three dots will close a tag and start a new one, for example:

    ...'my-class'

will cause:

    </div>
    <div class='my-class'>

Certain HTML elements have special handling.

In the case of the `a` element:

    .a http://codalogic.com: My home page

yields:

    <a href='http://codalogic.com'>My home page</a>

As a CSS class is always in single quotes, both the following:

    .a 'my-class' http://codalogic.com: My home page
    .a http://codalogic.com 'my-class': My home page

yield:

    <a class='my-class' href='http://codalogic.com'>My home page</a>

In the case of the `img` tag:

    .img images/logo.png

yields:

    <img src='images/logo.png'>

Named start tag attributes can be included using the format
`<attribute name>( <attribute value> )`.  Thus to add an `alt` attribute to
an `img` element, you can do:

    .img images/logo.png alt( Company logo )

This will yield:

    <img src='images/logo.png' alt='Company logo'>

If you wish your `img` elements to be empty, you can include empty content
at the end.  For example:

    .img images/logo.png alt( Company logo ):

will yield:

    <img src='images/logo.png' alt='Company logo' />

As mentioned above, `htmlo` directives can form the content of a directive,
so you can do:

    .a http://codalogic.com: .img images/logo.png alt( Company logo ):

yielding:

    <a href='http://codalogic.com'><img src='images/logo.png' alt='Company logo' /></a>

HTML comments can be output by preceding them with `.#`, for example:

    .# My comment

yields:

    <!-- My comment -->

Content can be HTML escaped using `.:`, for example:

    .: if( a < b && b <= c )

yields:

    if( a &lt; b &amp;&amp; b &lt;= c )

PHP functions can be called using the `.!` and `.!!` directives.  The
following:

    .! my_func $i $j

will effectively call the PHP function:

    my_func( $i, $j )

Between 0 and 5 function arguments are currently supported.

Note that the function arguments (i.e. `$i` and `$j` in the above case) are
interpolated as part of string expansion before the `htmlo` functions are
actually called.  Hence if `$i` were `Fred` and `$j` were `Bloggs`, the
actual directive seen by the `htmlo` functions would be:

    .! my_func Fred Bloggs

This does however yield the desired results.

The `.!!` form is more applicable in the case of the `htmlo()` function.
When this directive is encountered it `echo`s the string accumulated so
far, then `echo`s the string returned by the function, and then continues
to accumulate output.  This avoids accumulating lots of output into a string
before outputting it, and thus maybe more efficient if multiple nested
functions are called.  It can also give more consistent results in the case
where the called functions `echo`s out its data directly rather than
returning it as a string.

## Testing

Open a shell and `cd` to the test directory.  Then do
`php test-cl-htmlo.php`.  (The full path will be needed to be included on
the `php` command if the command is not on the command path.)
