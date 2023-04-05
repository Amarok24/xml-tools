# xml-tools
Various XML tools and tests written in PHP.

## xml2text
### Usage on command line:
Rename xml2text.php to xml2text, make sure it can be executed or run
`chmod +x ./xml2text`

### Read an XML with cURL, use a pipe into xml2text and write output to a file,
errors will be written to stderr (they will be printed into the terminal).

`curl "http://example.com/rss-feed" | xml2text > feed.txt`

### Same as above, but redirect errors to error.log file.
`curl "http://example.com/rss-feed" | xml2text > feed.txt 2> error.log`

### Read an XML with cURL, use a pipe into xml2text and output it through `more`.
`curl "http://example.com/rss-feed" | xml2text | more`

### Examples
Input file `/src/samples/bbc-world.xml`, output `/src/samples/bbc-world.txt`.
