<?php
/**
 * Date helpers
 *
 * @package    JSON CMS
 * @subpackage Classes
 * @category   Content
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'BLUDIT' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class Date {

	/**
	 * Translate date
	 *
	 * Returns string with the date translated.
	 *
	 * @example English to Spanish: 'Mon, 27th March' > 'Lun, 27th Marzo'
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $date
	 * @global object $L The Language class.
	 * @return string
	 */
	public static function translate( $date ) {

		// Access global variables.
		global $L;

		// If English default language don't translate.
		if ( 'en' == $L->currentLanguage() ) {
			return $date;
		}

		// Get the array of dates from the language file.
		$dates = $L->getDates();
		foreach ( $dates as $english => $anotherLang ) {
			$date = preg_replace( '/\b' . $english . '\b/u', $anotherLang, $date );
		}
		return $date;
	}

	/**
	 * UNIX time
	 *
	 * Returns current Unix timestamp, GMT+0.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @return string
	 */
	public static function unixTime() {
		return time();
	}

	/**
	 * Current date & time
	 *
	 * Returns the local time/date according to locale settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $format
	 * @return string
	 */
	public static function current( $format ) {

		$Date   = new \DateTime();
		$output = $Date->format( $format );

		return self :: translate( $output );
	}

	/**
	 * Current date & time with offset
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $format
	 * @param  integer $offset
	 * @return string
	 */
	public static function currentOffset( $format, $offset ) {

		$Date = new \DateTime();
		$Date->modify( $offset );
		$output = $Date->format( $format );

		return self :: translate( $output );
	}

	/**
	 * Date & time offset
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $date
	 * @param  string $format
	 * @param  integer $offset
	 * @return string
	 */
	public static function offset( $date, $format, $offset ) {

		$Date = new \DateTime( $date );
		$Date->modify( $offset );
		$output = $Date->format( $format );

		return self :: translate( $output );
	}

	/**
	 * Date format
	 *
	 * Format a local time/date according to locale settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $date
	 * @param  string $currentFormat
	 * @param  string $outputFormat
	 * @return string
	 */
	public static function format( $date, $currentFormat, $outputFormat ) {

		// Returns a new DateTime instance or FALSE on failure.
		$Date = \DateTime :: createFromFormat( $currentFormat, $date );

		if ( false !== $Date ) {
			$output = $Date->format( $outputFormat );
			return self :: translate( $output );
		}
		return false;
	}

	/**
	 * Convert to UTC
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $date
	 * @param  string $currentFormat
	 * @param  string $outputFormat
	 * @return string
	 */
	public static function convertToUTC( $date, $currentFormat, $outputFormat ) {

		$Date = \DateTime :: createFromFormat( $currentFormat, $date );
		$Date->setTimezone( new \DateTimeZone( 'UTC' ) );
		$output = $Date->format( $outputFormat );

		return self :: translate( $output );
	}

	/**
	 * Time ago
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $time
	 * @return string
	 */
	public static function timeago( $time ) {

		$time   = time() - $time;
		$tokens = [
			31536000 => 'year',
			2592000 => 'month',
			604800 => 'week',
			86400 => 'day',
			3600 => 'hour',
			60 => 'minute',
			1 => 'second'
		];

		foreach ( $tokens as $unit => $text ) {
			if ( $time < $unit ) {
				continue;
			}
			$numberOfUnits = floor( $time / $unit );
			return $numberOfUnits . ' ' . $text . ( ( $numberOfUnits > 1 ) ? 's' : '' );
		}
	}

	/**
	 * Timezone list
	 *
	 * Returns `array( 'Africa/Abidjan' => 'Africa/Abidjan (GMT+0)', ..., 'Pacific/Wallis' => 'Pacific/Wallis (GMT+12)' );`
	 *
	 * @link http://php.net/manual/en/timezones.php
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @return array
	 */
	public static function timezoneList() {

		$tmp = [];
		$timezone_identifiers_list = timezone_identifiers_list();

		foreach ( $timezone_identifiers_list as $timezone_identifier ) {

			$date_time_zone = new \DateTimeZone( $timezone_identifier );
			$date_time = new \DateTime( 'now', $date_time_zone );

			$hours = floor( $date_time_zone->getOffset( $date_time ) / 3600 );
			$mins = floor( ( $date_time_zone->getOffset( $date_time ) - ( $hours * 3600 ) ) / 60 );

			$hours = 'GMT' . ( $hours < 0 ? $hours : '+' . $hours );
			$mins  = ( $mins > 0 ? $mins : '0' . $mins );
			$text  = str_replace( '_', ' ', $timezone_identifier );

			$tmp[$timezone_identifier] = $text . ' (' . $hours . ':' . $mins . ')';
		}
		return $tmp;
	}
}
