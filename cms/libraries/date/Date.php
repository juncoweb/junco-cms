<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class Date extends DateTime
{
	// vars
	private static $yesterday = null;

	/**
	 * Format the date
	 *
	 * @param string $format
	 */
	public function format(string $format): string
	{
		$format = preg_replace('#(?<!\\\\)+M#', '\0:\\\\\\0', $format);
		$output = parent::format($format);

		// pretty format
		$output = preg_replace_callback('#\[(.*)?\]#', function ($match) {
			if (isset($match[1])) {
				if (!self::$yesterday) {
					$date = getdate();
					self::$yesterday = $date[0]
						- $date['hours'] * 3600
						- $date['minutes'] * 60
						- $date['seconds'] - 86400;
				}
				$timestamp = $this->getTimestamp();

				if ($timestamp > self::$yesterday) {
					if ($timestamp < self::$yesterday + 86400) {
						return _t('Yesterday');
					} elseif ($timestamp < self::$yesterday + 172800) {
						return _t('Today');
					} elseif ($timestamp < self::$yesterday + 259200) {
						return _t('Tomorrow');
					}
				}

				return $match[1];
			}
		}, $output);

		// translate
		$pattern = '#(' .
			'Mon(?:day)?|Tue(?:sday)?|Wed(?:nesday)?|Thu(?:rsday)?|Fri(?:day)?|Sat(?:urday)?|Sun(?:day)?|' .
			'Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|' .
			'Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?' .
			')(:M)?#';

		return preg_replace_callback($pattern, function ($match) {
			if ($match[1] == 'May' && isset($match[2])) {
				return _t('[short]:May');
			}

			return _t($match[1]);
		}, $output);
	}

	/**
	 * Format interval.
	 *
	 * @param string|DateTime $datetime2
	 * @param int             $granularity
	 *
	 * @return string or null
	 */
	public function formatInterval($datetime2, int $granularity = 2)
	{
		if ($granularity < 1) {
			return;
		}

		if (is_string($datetime2)) {
			$datetime2 = new DateTime($datetime2);
		}

		$interval = $this->diff($datetime2);
		$output   = [];
		$count    = 0;

		foreach (['y', 'm', 'd', 'h', 'i', 's'] as $i) {
			$value = $interval->$i;
			if ($count || $value) {
				if (++$count > $granularity) {
					break;
				}

				switch ($i) {
					case 'y':
						$text = _nt('%d year', '%d years', $value);
						break;
					case 'm':
						$text = _nt('%d month', '%d months', $value);
						break;
					case 'd':
						$text = _nt('%d day', '%d days', $value);
						break;
					case 'h':
						$text = _nt('%d hour', '%d hours', $value);
						break;
					case 'i':
						$text = _nt('%d minute', '%d minutes', $value);
						break;
					case 's':
						$text = _nt('%d second', '%d seconds', $value);
						break;
				}

				$output[] = sprintf($text, $value);
			}
		}

		return implode(' ', $output);
	}


	/**
	 * Magic method to access properties of the date.
	 *
	 * @param string $name The name of the property.
	 *
	 * @return mixed
	 */
	public function __get(string $name)
	{
		switch (strtolower($name)) {
				// Day
			case 'day':
				return parent::format('d');

			case 'shortdayname':
				switch (parent::format('w')) {
					case 1:
						return _t('Mon');
					case 2:
						return _t('Tue');
					case 3:
						return _t('Wed');
					case 4:
						return _t('Thu');
					case 5:
						return _t('Fri');
					case 6:
						return _t('Sat');
					case 0:
						return _t('Sun');
				}

			case 'dayname':
				switch (parent::format('w')) {
					case 1:
						return _t('Monday');
					case 2:
						return _t('Tuesday');
					case 3:
						return _t('Wednesday');
					case 4:
						return _t('Thursday');
					case 5:
						return _t('Friday');
					case 6:
						return _t('Saturday');
					case 0:
						return _t('Sunday');
				}

			case 'dayofweek':
				return parent::format('N');

			case 'dayofyear':
				return parent::format('z');

				// Week
			case 'week':
				return parent::format('W');

				// Month
			case 'monthname':
				switch (parent::format('n')) {
					case 1:
						return _t('January');
					case 2:
						return _t('February');
					case 3:
						return _t('March');
					case 4:
						return _t('April');
					case 5:
						return _t('May');
					case 6:
						return _t('June');
					case 7:
						return _t('July');
					case 8:
						return _t('August');
					case 9:
						return _t('September');
					case 10:
						return _t('October');
					case 11:
						return _t('November');
					case 12:
						return _t('December');
				}

			case 'month':
				return parent::format('m');

			case 'shortmonthname':
				switch (parent::format('n')) {
					case 1:
						return _t('Jan');
					case 2:
						return _t('Feb');
					case 3:
						return _t('Mar');
					case 4:
						return _t('Apr');
					case 5:
						return _t('[short]:May');
					case 6:
						return _t('Jun');
					case 7:
						return _t('Jul');
					case 8:
						return _t('Aug');
					case 9:
						return _t('Sep');
					case 10:
						return _t('Oct');
					case 11:
						return _t('Nov');
					case 12:
						return _t('Dec');
				}

			case 'daysinmonth':
				return parent::format('t');

				// Year
			case 'isleapyear':
				return parent::format('L');

			case 'year':
				return parent::format('Y');

				// Time
			case 'hour':
				return parent::format('H');

			case 'minute':
				return parent::format('i');

			case 'second':
				return parent::format('s');

				// Timezone
				// Full Date/Time
				// input formt
			case 'inputformat':
				return parent::format('Y-m-d\TH:i');

			default:
				$trace = debug_backtrace();
				trigger_error(
					'Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
					E_USER_NOTICE
				);
				return null;
		}
	}

	/**
	 * Returns an array with the months of the year.
	 */
	public static function getMonths()
	{
		return [
			1	=> _t('January'),
			2	=> _t('February'),
			3	=> _t('March'),
			4	=> _t('April'),
			5	=> _t('May'),
			6	=> _t('June'),
			7	=> _t('July'),
			8	=> _t('August'),
			9	=> _t('September'),
			10	=> _t('October'),
			11	=> _t('November'),
			12	=> _t('December')
		];
	}
}
