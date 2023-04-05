# xml-tools
Various XML tools and tests written in PHP.

I recommend any GNU/Linux distro to run PHP scripts. Make sure you have the default `php` package installed, also install `php-xml`. Make sure you have PHP 8.1 or newer.

## xml2text
A very simple CLI tool for converting any XML file into a more human-friendly text format.

It makes use of [XMLReader](http://docs.php.net/manual/en/book.xmlreader.php), so any document which XMLReader can process is a valid input file.

### Usage in any Unix-compatible terminal like Bash or Zsh
Rename `xml2text.php` to `xml2text`, make sure it can be executed or set executable mode with `chmod +x xml2text`.

### Read an XML with cURL, use a pipe into xml2text and write output to a file
Errors will be written to stderr.

`curl "http://example.com/rss-feed" | xml2text > feed.txt`

### Other usage
Run `xml2text -h` to see more examples.
