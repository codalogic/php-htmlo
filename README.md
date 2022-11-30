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

Three dots will close a tag and start a new one, for example:

    ...'my-class'

will cause:

    </div>
    <div class='my-class'>

`htmlo` attempts to keep track of tag nesting, so two dots on their own
close the last opened tag.  For example:

    .p
        Text
    ..

will yield:

    <p>
        Text
    </p>

Similarly, 3 dots on their own will close the current tag and then re-open it.  So:

    .p
        Text
    ...
        More Text
    ..

will yield:

    <p>
        Text
    </p>
    <p>
        More Text
    </p>

and:

    .'my-class'
        Text
    ...
        More Text
    ..

will yield:

    <div class='my-class'>
        Text
    </div>
    <div class='my-class'>
        More Text
    </div>

Note that each call to the `htmlo()` or
`htmls()` functions maintains their own tag stack.  Therefore tag nesting
can not be pushed in one function call and automatically popped in
a subsequent function call.

`.|` splits the content at the `|` symbol and processes each part as
if it were a stand-alone line.  For example:

    .|: .i: A | .b: B

will yield:

    <i>A</i><b>B</b>

If it is desired to perform the split using a different character to `|`, or a
character sequence, this can be specified between the `|` and `:` characters.  For example:

    .|^^: .i: A ^^ .b: B ^^ .u: U

yields:

    <i>A</i><b>B</b><u>U</u>

This can be helpful when generating table rows etc.  E.g.:

    .tr: .|: .td: A | .td: B | .td: C

outputs:

    <tr><td>A</td><td>B</td><td>C</td></tr>

`.|` will trim whitespace from both ends of each sub-branch, as can be seen
above.  To include whitespace at the beginning or end of a sub-branch, put
it in its own sub-branch.  Any sub-branch containing only whitespace will be
output as-is, without any further processing.  For example:

    .|: .i: A | | .b: B

will result in:

    <i>A</i> <b>B</b>

`.|` can also be used as a way of outputting the contents of a function call
in the middle of a line being processed.  See below for details.

Certain HTML elements have special handling.

In the case of the `a` element the linked URL gets special treatment. Thus:

    .a http://codalogic.com: My home page

yields:

    <a href='http://codalogic.com'>My home page</a>

If a URL is not specified as a parameter and the contents of the directive
is an http or https URL then the contents is used for the href parameter:

    .a: http://codalogic.com

yields:

    <a href='http://codalogic.com'>http://codalogic.com</a>

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

For `br` elements:

    .br: A line

will output:

    A line<br />

As mentioned above, `htmlo` directives can form the content of a directive,
so you can do:

    .a http://codalogic.com: .img images/logo.png alt( Company logo ):

yielding:

    <a href='http://codalogic.com'><img src='images/logo.png' alt='Company logo' /></a>

And:

    .br: .a http://codalogic.com: .img images/logo.png alt( Company logo ):

yielding:

    <a href='http://codalogic.com'><img src='images/logo.png' alt='Company logo' /></a><br />

HTML comments can be output by preceding them with `.#`, for example:

    .# My comment

yields:

    <!-- My comment -->

Lines beginning with `.-` are ignored, and no output is generated, for example:

    .- .p: This will be ignored

A block of lines can be ignored by preceding them with `./*` and following
them with `.*/`, for example:

    Included
    ./*
    This is ignored
    .*/
    This is included

Ignore blocks can be nested:

    Included
    ./*
        This is ignored
        ./*
            This is ignored
        .*/
        This is ignored
    .*/
    This is included

Multiple `*` characters are permitted in the token marking the beginning and
end of ignore blocks in order to make them clearer (the number of `*`
characters do not have to match):

    Included
    ./*****
        This is ignored
        ./***
            This is ignored
        .****/
        This is ignored
    .**********/
    This is included

The sequence of `*` characters marking an ignore block may also include
alphanumeric and underscore characters to act as a way of associating one end
of an ignore block with the other.  Note, this is purely cosmetic, and it is
not verified that the sequences in the two ends match:

    Included
    ./****_Outer_*
        This is ignored
        ./**_Inner_*
            This is ignored
        .***_Inner_*/
        This is ignored
    .*********_Outer_*/
    This is included

Content can be HTML escaped using `.:`, for example:

    .: if( a < b && b <= c )

yields:

    if( a &lt; b &amp;&amp; b &lt;= c )

`.:` can also be used to escape character sequences at a start of a line that happen
to look like htmlo directives.  For example, if you want your line to start with
`.p: stuff`, and have the `.p:` part output, you can do:

    .:.p: stuff

PHP functions can be called using the `.!` directive.  The
following:

    .! my_func $i $j

will effectively call the PHP function:

    my_func( $i, $j )

By default, the function's parameters are separated by spaces.  To pass a
string containing spaces, it is necessary to specify an alternative separator
character.  This can be specified immediately after the `.!` token:

    .!! my_func This is a string argument to the function

Or:

    .!/ my_func top left / top right / 0

The function parameter separater can consist of multiple non-word, non-space
characters, as in:

    .!^^ my_func top left ^^ top right ^^ 0

Between 0 and 5 function arguments are currently supported.

Note that the function arguments (i.e. `$i` and `$j` in the above case) are
interpolated as part of string expansion before the `htmlo` functions are
actually called.  Hence if `$i` were `Fred` and `$j` were `Bloggs`, the
actual directive seen by the `htmlo` functions would be:

    .! my_func Fred Bloggs

This does however yield the desired results.

To output the results of a function in the middle of a line, a trick using
the `.|` line splitter option can be used.  For example:

    .|: This is text | .!wibble | More text

yields:

    This is textwobbleMore text

and, using the technique to ensure spaces between the fields:

    .|: This is text | | .!wibble | | More text

yields:

    This is text wobble More text

## Testing

Open a shell and `cd` to the test directory.  Then do
`php test-cl-htmlo.php`.  (The full path will be needed to be included on
the `php` command if the command is not on the command path.)
