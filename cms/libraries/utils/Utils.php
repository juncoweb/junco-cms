<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class Utils
{
	/**
	 * Cuts a text if its length is greater than the required
	 *
	 * @param string $text      The text to be cut out.
	 * @param int    $max       The maximum length of the text.
	 * @param string $ellipsis  If necessary, the ellipsis.
	 */
	public static function cutText(string $text = '', int $max = 100, string $ellipsis = '...')
	{
		if ($text && mb_strlen($text) > $max) {
			$space = mb_strrpos(mb_substr($text, 0, $max + 1), ' ');
			$text = mb_substr($text, 0, $space ?: $max) . $ellipsis;
		}

		return $text;
	}

	/**
	 * Transform a text to serve as a slug
	 * 
	 * @deprecated since version 14
	 * 
	 * @param string $name
	 * 
	 * @return string
	 */
	public static function cleanName(string $name)
	{
		return self::sanitizeSlug($name);
	}

	/**
	 * Transform a text to serve as a slug
	 * 
	 * @param string $slug
	 * 
	 * @return string
	 */
	public static function sanitizeSlug(string $slug, string $replace = '-')
	{
		$slug = str_replace(
			['á', 'é', 'í', 'ó', 'ú', 'à', 'è', 'ì', 'ò', 'ù', 'â', 'ê', 'î', 'ô', 'û', 'ä', 'ë', 'ï', 'ö', 'ü', 'Á', 'É', 'Í', 'Ó', 'Ú', 'À', 'È', 'Ì', 'Ò', 'Ù', 'Â', 'Ê', 'Î', 'Ô', 'Û', 'Ä', 'Ë', 'Ï', 'Ö', 'Ü', 'ñ', 'Ñ', 'ç', 'Ç', 'º', 'ª'],
			['a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'n', 'n', 'c', 'c', 'o', 'a'],
			trim($slug)
		);

		return strtolower(preg_replace('%[^\w-]%', $replace, $slug));
	}

	/**
	 * Array toggle
	 * 
	 * @param array  $array
	 * @param mixed  $name
	 * @param ?bool  $status
	 * 
	 * @return array
	 */
	public static function arrayToggle(array $array, mixed $item, ?bool $status = null): array
	{
		if ($status === null) {
			$status = !in_array($item, $array);
		}

		if ($status) {
			if (!in_array($item, $array)) {
				$array[] = $item;
			}
		} else {
			$array = array_diff($array, [$item]);
		}

		return $array;
	}
}
