<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filesystem;

use Junco\Filesystem\Image\ImageInterface;
use Junco\Filesystem\Image\Gif;
use Junco\Filesystem\Image\Jpeg;
use Junco\Filesystem\Image\Png;
use Junco\Filesystem\Image\Webp;
use Exception;

class ImageResizer
{
	// const
	const RESIZE_PROPORTIONAL		= 1;
	const RESIZE_PROPORTIONAL_AREA	= 2;
	const RESIZE_SQUARE				= 3;
	const RESIZE_EXACT				= 4;
	const RESIZE_EXACT_WIDTH		= 5;
	const RESIZE_EXACT_HEIGHT		= 6;

	/**
	 * Resize
	 *
	 * @param string      $src_file The source file path.
	 * @param string      $rsz_file The resize file path.
	 * @param string|int  $max_wh   Max. width or height (depending on the shape of the original image).
	 * @param int         $mode     Modes in which resizing will be performed.
	 *   1 - proportional
	 *   2 - proportional area
	 *   3 - square
	 *   4 - Exact
	 *   5 - width
	 *   6 - height
	 * @param bool        $force_rsz Force size if the original image is smaller than the resize.
	 *
	 * @throws Exception
	 * 
	 * @return void
	 */
	public function resize(
		string     $src_file,
		string     $rsz_file,
		string|int $max_wh,
		int        $mode = 1,
		bool       $force_rsz = false
	): void {
		if (!is_file($src_file)) {
			throw new Exception(sprintf(_t('Error %s! failed resizing the image.'), 0));
		}

		$original = getimagesize($src_file);

		if ($original === false) {
			throw new Exception(sprintf(_t('Error %s! failed resizing the image.'), __LINE__));
		}

		$max_wh	  = $this->getMaxWH($max_wh, $mode);
		$resizing = $this->calculateResizing($original, $max_wh, $mode, $force_rsz);

		// the image does not require resizing.
		if (!$resizing[0] && !$resizing[1]) {
			if (!copy($src_file, $rsz_file)) {
				throw new Exception(sprintf(_t('Error %s! failed resizing the image.'), __LINE__));
			}
			return;
		}

		// resize
		$adapter = $this->getAdapter($original[2]);

		if (!$adapter) {
			throw new Exception(sprintf(_t('Error %s! failed resizing the image.'), __LINE__));
		}

		$srcImg = $adapter->createImageFromFile($src_file);

		if (!$srcImg) {
			throw new Exception(sprintf(_t('Error %s! failed resizing the image.'), __LINE__));
		}

		$rszImg = $adapter->createImage($resizing[0], $resizing[1], $srcImg);

		if (!$rszImg) {
			throw new Exception(sprintf(_t('Error %s! failed resizing the image.'), __LINE__));
		}

		$result = imagecopyresampled(
			$rszImg,
			$srcImg,
			$resizing['x'],
			$resizing['y'],
			$original['x'],
			$original['y'],
			$resizing[0],
			$resizing[1],
			$original[0],
			$original[1]
		);

		if (!$result) {
			throw new Exception(sprintf(_t('Error %s! failed resizing the image.'), __LINE__));
		}

		if (!$adapter->write($rsz_file, $rszImg)) {
			throw new Exception(sprintf(_t('Error %s! failed resizing the image.'), __LINE__));
		}
	}

	/**
	 * Returns the modes in which an image can be resized
	 */
	public static function getModes(): array
	{
		return [
			self::RESIZE_PROPORTIONAL		=> '1 - Proportional',
			self::RESIZE_PROPORTIONAL_AREA	=> '2 - Proportional area',
			self::RESIZE_SQUARE				=> '3 - Square',
			self::RESIZE_EXACT				=> '4 - Exact',
			self::RESIZE_EXACT_WIDTH		=> '5 - Exact width',
			self::RESIZE_EXACT_HEIGHT		=> '6 - Exact height'
		];
	}

	/**
	 * Sanitize the width and height of an image.
	 *
	 * @param string $max_wh
	 * @param int    $mode
	 * 
	 * @return void
	 */
	public static function sanitizeWH(string &$max_wh, int $mode): void
	{
		if ($mode == self::RESIZE_EXACT) {
			$max_wh = explode('x', $max_wh, 2);

			if (count($max_wh) == 1) {
				$max_wh[1] = $max_wh[0];
			}

			$max_wh[0] = (int)$max_wh[0];
			$max_wh[1] = (int)$max_wh[1];

			if ($max_wh[0] < 10) {
				$max_wh[0] = 10;
			}

			if ($max_wh[1] < 10) {
				$max_wh[1] = 10;
			}

			$max_wh = implode('x', $max_wh);
		} else {
			$max_wh = (int)$max_wh;

			if ($max_wh < 10) {
				$max_wh = 10;
			}
		}
	}

	/**
	 * Get
	 * 
	 * @param string|int $max_wh
	 * @param int        $mode
	 * 
	 * @return array|int
	 */
	protected function getMaxWH(string|int $max_wh, int $mode): array|int
	{
		if ($mode == self::RESIZE_EXACT) {
			$max_wh = explode('x', $max_wh, 2);
			$max_wh[1] ??= $max_wh[0];

			return array_map('intval', $max_wh);
		}

		return (int)$max_wh;
	}

	/**
	 * Get
	 * 
	 * @param array      &$original
	 * @param array|int  $max_wh
	 * @param int        $mode
	 * @param bool       $force_rsz
	 * 
	 * @return array
	 */
	protected function calculateResizing(array &$original, array|int $max_wh, int $mode, bool $force_rsz): array
	{
		$resizing = [0, 0, 'x' => 0, 'y' => 0];
		$original += ['x' => 0, 'y' => 0];
		$cut_org = false;

		switch ($mode) {
			default:
			case self::RESIZE_PROPORTIONAL:
				if (
					$original[0] > $max_wh
					|| $original[1] > $max_wh
					|| ($force_rsz && $original[0] < $max_wh && $original[1] < $max_wh)
				) {
					if ($original[0] > $original[1]) {
						$resizing[0] = $max_wh;
					} else {
						$resizing[1] = $max_wh;
					}
				}
				break;

			case self::RESIZE_PROPORTIONAL_AREA:
				// $ratio + is_vertical
				if ($original[0] < $original[1]) {
					$ratio = $original[0] / $original[1];
					$wh = 1;
				} else {
					$ratio = $original[1] / $original[0];
					$wh = 0;
				}

				// normalize ratio
				$ratio_1 = 46 / 68;
				$ratio_2 = 43 / 80;
				$ratio_3 = 37 / 86;
				$ratio_4 = 37 / 94;

				if ($ratio >= $ratio_1) {
					$normalized = 0.68;
				} elseif ($ratio >= $ratio_2 && $ratio < $ratio_1) {
					$normalized = 0.8;
				} elseif ($ratio >= $ratio_3 && $ratio < $ratio_2) {
					$normalized = 0.87;
				} elseif ($ratio >= $ratio_4 && $ratio < $ratio_3) {
					$normalized = 0.94;
				} else {
					$normalized = 1;
				}

				// resizing
				$resizing[$wh] = (int)($normalized * $max_wh);

				if (!$force_rsz && $original[$wh] <= $resizing[$wh]) {
					$resizing[$wh] = 0;
				}
				break;

			case self::RESIZE_SQUARE:
				if (
					$original[0] > $max_wh
					|| $original[1] > $max_wh
					|| ($force_rsz && ($original[0] < $max_wh || $original[1] < $max_wh))
				) {
					$resizing[0] = $resizing[1] = $max_wh;
					$cut_org = true;
				}
				break;

			case self::RESIZE_EXACT:
				if (
					$original[0] > $max_wh[0]
					|| $original[1] > $max_wh[1]
					|| ($force_rsz && ($original[0] < $max_wh[0] || $original[1] < $max_wh[1]))
				) {
					$resizing[0] = $max_wh[0];
					$resizing[1] = $max_wh[1];
					$cut_org  = true;
				}
				break;

			case self::RESIZE_EXACT_WIDTH:
				if ($original[0] > $max_wh || ($force_rsz && $original[0] < $max_wh)) {
					$resizing[0] = $max_wh;
				}
				break;

			case self::RESIZE_EXACT_HEIGHT:
				if ($original[1] > $max_wh || ($force_rsz && $original[1] < $max_wh)) {
					$resizing[1] = $max_wh;
				}
				break;
		}

		//
		if ($resizing[0] && !$resizing[1]) {
			$resizing[1] = floor(($resizing[0] / $original[0]) * $original[1]);
		} elseif (!$resizing[0] && $resizing[1]) {
			$resizing[0] = floor(($resizing[1] / $original[1]) * $original[0]);
		} elseif ($cut_org) {
			$org_ratio	= $original[0] / $original[1];
			$rsz_ratio	= $resizing[0] / $resizing[1];

			if ($org_ratio > $rsz_ratio) {
				$new_w			= $rsz_ratio * $original[1];
				$original['x']	= floor(($original[0] - $new_w) / 2);
				$original[0]	= floor($new_w);
			} elseif ($org_ratio < $rsz_ratio) {
				$new_h 			= $original[0] / $rsz_ratio;
				$original['y']	= floor(($original[1] - $new_h) / 2);
				$original[1]	= floor($new_h);
			}
		}

		return $resizing;
	}

	/**
	 * Get
	 * 
	 * @param int $type
	 * 
	 * @return ?ImageInterface
	 */
	protected function getAdapter(int $type): ?ImageInterface
	{
		switch ($type) {
			case IMAGETYPE_GIF:
				return new Gif;

			case IMAGETYPE_JPEG:
				return new Jpeg;

			case IMAGETYPE_PNG:
				return new Png;

			case IMAGETYPE_WEBP:
				return new Webp;
		}

		return null;
	}
}
