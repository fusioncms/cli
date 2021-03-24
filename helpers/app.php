<?php

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Generate a URL friendly "slug" from a given string.
 *
 * @param  string  $title
 * @param  string  $separator
 * @return string
 */
function slugify($value, $separator = '-')
{
	$value = strtolower($value);
	$value = preg_replace('/[^0-9a-z]/', $separator, $value);

	return trim($value, $separator);
}

/**
 * Target file path and run string replacement method.
 * 
 * @param  string  $path
 * @param  array   $replacements
 * @return void
 */
function replacePlaceholders($path, $replacements = [])
{
    if (file_exists($path)) {
        file_put_contents(
            $path,
            strtr(
                file_get_contents($path),
                $replacements
            )
        );
    }
}

/**
 * Clear contents of specified path.
 * Ignores dot files.
 * 
 * @param  string $path
 * @return void
 */
function cleanDirectory($path)
{
	if (is_dir($path)) {
		$files = (new Finder())->in($path)->depth('== 0');
		$paths = [];

		foreach ($files as $file) {
			$paths[] = $file->getPathname();
		}

		(new Filesystem())->remove($paths);
	}
}