<?php
// https://www.php.net/manual/en/features.commandline.io-streams.php
// Already opened streams STDIN, STDOUT and STDERR do not need to be
// explicitly closed, they are closed automatically at the end of a program.

/**
 * Writes to STDOUT.
 * @return bool|int The number of bytes written or false on failure.
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
