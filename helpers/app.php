<?php

/**
 * Generate a URL friendly "slug" from a given string.
 * Based on \Illuminate\Support\Str::slug
 *
 * @param  string  $title
 * @param  string  $separator
 * @param  string|null  $language
 * @return string
 */
function slugify($value, $separator = '-')
{
	// Convert all dashes/underscores into separator
	$flip = $separator === '-' ? '_' : '-';

	$value = preg_replace('!['.preg_quote($flip).']+!u', $separator, $value);

	// Replace @ with the word 'at'
	$value = str_replace('@', $separator.'at'.$separator, $value);

	// Remove all characters that are not the separator, letters, numbers, or whitespace.
	$value = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', strtolower($value));

	// Replace all separator characters and whitespace by a single separator
	$value = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $value);

	return trim($value, $separator);
}