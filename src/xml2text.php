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
	//$xmlreader->open(STDIN); // Doesn't work because STDIN is a resource.

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

	out("\n");

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
				case LIBXML_ERR_WARNING:
					err('XML_ERR_WARNING');
					break;
				case LIBXML_ERR_ERROR:
					err('XML_ERR_ERROR');
					$result = false;
					break;
				case LIBXML_ERR_FATAL:
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

/**
 * Writes to STDOUT.
 * @return bool|int The number of bytes written or false on failure.
 *
 * https://www.php.net/manual/en/features.commandline.io-streams.php
 * Already opened streams STDIN, STDOUT and STDERR don't need to be
 * explicitly closed, they are closed automatically at the end of a program.
 */
function out(string $s): bool|int
{
	return fwrite(STDOUT, $s);
}

/**
 * Writes to STDERR.
 * @return bool|int The number of bytes written or false on failure.
 */
function err(string $s): bool|int
{
	return fwrite(STDERR, $s);
}


function main(int $argc, array $argv): int
{
	$USAGE = <<<TEXT
xml2text 1.0
Converts XML to human-readable text.

PARAMETERS:
-x  Prints libXML version used in local PHP installation.
-p  Prints PHP version of local installation.
-h  Shows this help page.

USAGE EXAMPLES:
curl "http://example.com/rss-feed" | xml2text > output.txt
cat feed.xml | xml2text > output.txt 2> errors.log
echo '<msg id="1"><content>Hello</content></msg>' | xml2text
\n
TEXT;

	if ($argc === 2) {
		switch ($argv[1]) {
			case '-x':
				print 'libXML version ' . LIBXML_DOTTED_VERSION . "\n";
				return 0;
			case '-p':
				print 'PHP version ' . PHP_VERSION . "\n";
				return 0;
			case '-h':
				print $USAGE;
				return 0;
			default:
				print "Invalid parameter.\n";
				return 1;
		}
	}

	if ($argc > 2) {
		print "Too many parameters.\n";
		return 1;
	}

	if ($argc === 1 && stream_isatty(STDIN)) {
		print "Nothing to do. Use -h parameter to show help.\n";
		// file_get_contents('php://stdin');
		return 0;
	}

	if (parse_xml())
		return 0;
	else
		return 1;
}


exit(main($argc, $argv));
