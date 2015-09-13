<?php

/**
 * Validators for sanitising input data
 *
 */

namespace Freshsauce;

/**
 * Class Validate
 * @package Freshsauce
 */
class Validate {

	private function __construct() {
		# all methods should be static
	}

	/**
	 * return true if an int and optional up to max characters long
	 *
	 * @param string $string
	 * @param array $options ['max_len']
	 *
	 * @return int
	 */
	public static function validateInt( $string, $options ) {
		$max_length = isset( $options['max_len'] ) ? $options['max_len'] : null;

		return preg_match( '/^\d' . ( $max_length ? '{1,' . $max_length . '}' : '+' ) . '$/', $string );
	}

	/**
	 * return true if a float no longer than overall length characters (less decimal point) and up to max decimals
	 *
	 * @param string $string
	 * @param array $options ['max_len'] = array(int,int)
	 *
	 * @return int
	 */
	public static function validateFloat( $string, $options ) {
		$overall_length = isset( $options['max_len'][0] ) ? $options['max_len'][0] : 1;
		$max_decimals   = isset( $options['max_len'][1] ) ? $options['max_len'][1] : 0;
		$overall_length = $overall_length - $max_decimals;

		return preg_match( '/^\d{1,' . $overall_length . '}(\.\d{0,' . $max_decimals . '})?$/', $string );
	}

	/**
	 * return true if string is in a valid bool
	 *
	 * @param string $string
	 *
	 * @return bool
	 */
	public static function validateBool( $string ) {
		return $string === true || $string === false ? true : false;
	}

	/**
	 * return true, or if max_length provided true if no longer than
	 *
	 * @param string $string
	 * @param array $options ['max_len']
	 *
	 * @return bool
	 */
	public static function validateString( $string, $options ) {
		$max_length = isset( $options['max_len'] ) ? $options['max_len'] : null;
		return $max_length ? ( mb_strlen( $string ) <= $max_length ) : true;
	}

	/**
	 * return true, if username is ok in length and contains only valid characters
	 *
	 * @param string $string
	 * @param array $options ['max_len', 'min_len']
	 *
	 * @return bool
	 */
	public static function validateUsername( $string, $options ) {
		$max_length = isset( $options['max_len'] ) ? $options['max_len'] : null;
		$min_length = isset( $options['min_len'] ) ? $options['min_len'] : null;
		if ( $max_length && mb_strlen( $string ) > $max_length ) {
			return false;
		}
		if ( $min_length && mb_strlen( $string ) < $min_length ) {
			return false;
		}
		// no leading underscores or numbers in username, and only alpha, numbers, underscore and hyphen
		if ( ! preg_match( '/^[A-Za-z]{1}[a-zA-Z\-_\.0-9]{2,}$/', $string ) ) {
			return false;
		}

		return true;
	}

	/**
	 * return true if string is in the array of values
	 *
	 * @param string $string
	 * @param array $options ['values']
	 *
	 * @return bool
	 */
	public static function validateEnum( $string, $options ) {
		return in_array( $string, $options['values'] );
	}

	/**
	 * return true if string is in a valid date
	 * format YYYY-MM-DD HH:MM:SS
	 *
	 * @param string $string
	 *
	 * @return int
	 */
	public static function validateDatetime( $string ) {
		return preg_match( '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $string );
	}

	/**
	 * return true if string is in a valid email address
	 *
	 * @param string $string
	 *
	 * @return bool
	 */
	public static function validateEmail( $string ) {
		return filter_var( $string, FILTER_VALIDATE_EMAIL ) !== false;
	}

}
