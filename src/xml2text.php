#!/usr/bin/env php
<?php
/*
	xml2text (CLI version, implemented with XMLReader)
	Author: Jan Prazak
	Website: https://github.com/Amarok24
	Date: 2023-04-05
	License: The Unlicense
*/

declare(strict_types=1);

require 'lib/stdouterr.php';

/**
 * @return bool True on success, false on failure.
 */
function parse_xml(): bool
{
	$space = '';
	$xmlreader = null;
	$result = true;

 	// Manually process errors at a later point.
	libxml_use_internal_errors(true);

	// https://www.php.net/manual/en/book.xmlreader.php
	$xmlreader = new XMLReader();
	$xmlreader->open('php://stdin');

	while ($xmlreader->read()) {
		if ($xmlreader->nodeType === XMLReader::ELEMENT) {
			$space = str_repeat("\t", $xmlreader->depth);
			out("\n" . $space . '[' . $xmlreader->name . ']');

			if ($xmlreader->hasAttributes) {
				$xmlreader->moveToFirstAttribute();
				out(' @' . $xmlreader->name . " \"{$xmlreader->value}\"");
			}
		}

		if (
			$xmlreader->nodeType === XMLReader::TEXT
			|| $xmlreader->nodeType === XMLReader::CDATA
		) {
			// TEXT nodes always have a value
			out(" \"{$xmlreader->value}\"");
		}
	}

	// XMLReader cannot be used in a try..catch block, it does not throw
	// any Throwable objects, it outputs the errors directly.
	// Therefore a manual processing of errors is nice.

	if (libxml_get_last_error()) {
		foreach (libxml_get_errors() as $e) {
			// each $error is a libXMLError object
			// https://www.php.net/manual/en/class.libxmlerror
			err("\nXMLReader error at line {$e->line} in source.\n");
			err('Error level: ');

			switch ($e->level) {
				case 1:
					err('XML_ERR_WARNING');
					break;
				case 2:
					err('XML_ERR_ERROR');
					$result = false;
					break;
				case 3:
					err('XML_ERR_FATAL');
					$result = false;
					break;
				default:
					err($e->level);
					break;
			}

			err("\nError message: {$e->message}\n");
		}

		libxml_clear_errors();
	}

	$xmlreader->close();
	return $result;
}

// main
if (parse_xml())
	exit(0);
else
	exit(1);

/*

Usage on command line:
Rename xml2text.php to xml2text, make sure it can be executed or run
chmod +x ./xml2text

Read an XML with cURL, use a pipe into xml2text and write output to a file,
errors will be written to stderr (they will be printed into the terminal).
>> curl "http://example.com/rss-feed" | xml2text > feed.txt

Same as above, but redirect errors to error.log file.
>> curl "http://example.com/rss-feed" | xml2text > feed.txt 2> error.log

Read an XML with cURL, use a pipe into xml2text and output it through `more`.
>> curl "http://example.com/rss-feed" | xml2text | more

*/
