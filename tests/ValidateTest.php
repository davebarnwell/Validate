<?php

/*
 * Validate Class tests
 */

namespace Freshsauce;

Class ValidateTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers \Freshsauce\Validate::validateInt()
	 */
	public function testValidateInt() {

		$test_data = [
			[ '0', 1, 11, "zero string" ],
			[ '1', 1, 11, "one string" ],
			[ '', 0, 11, "empty string" ],
			[ 'A', 0, 11, "alpha string" ],
			[ '999.99', 0, 11, "float string" ],
			[ '-999.99', 0, 11, "negative float string" ],
			[ '.99', 0, 11, "float string no leading decimal point" ],
			[ 999.99, 0, 11, "float" ],
			[ - 999.99, 0, 11, "negative float" ],
			[ 999, 1, 11, "int passed" ],
			[ - 999, 0, 11, "negative int passed" ],
			[ 999, 0, 2, "int but too long" ],
		];

		foreach ( $test_data as $data ) {
			list( $input, $expected, $length, $msg ) = $data;
			$this->assertEquals(
				$expected,
				Validate::validateInt( $input, [ 'max_len' => $length ] ),
				$msg
			);
		}
	}

	/**
	 * @covers \Freshsauce\Validate::validateFloat()
	 */
	public function testValidateFloat() {

		$test_data = [
			[ '0', 1, [ 'max_len' => [ 10, 2 ] ], "zero string" ],
			[ '1', 1, [ 'max_len' => [ 10, 2 ] ], "one string" ],
			[ '', 0, [ 'max_len' => [ 10, 2 ] ], "empty string" ],
			[ 'A', 0, [ 'max_len' => [ 10, 2 ] ], "alpha string" ],
			[ '999.99', 1, [ 'max_len' => [ 10, 2 ] ], "float string" ],
			[ '-999.99', 0, [ 'max_len' => [ 10, 2 ] ], "negative float string" ],
			[ '.99', 0, [ 'max_len' => [ 10, 2 ] ], "float string no leading decimal point" ],
			[ 999.99, 1, [ 'max_len' => [ 10, 2 ] ], "float" ],
			[ - 999.99, 0, [ 'max_len' => [ 10, 2 ] ], "negative float" ],  // leading negative sign not allowed
			[ 999, 1, [ 'max_len' => [ 10, 2 ] ], "int passed" ],
			[ - 999, 0, [ 'max_len' => [ 10, 2 ] ], "negative int passed" ], // leading negative sign not allowed
			[ 999, 0, [ 'max_len' => [ 2, 0 ] ], "int but too long" ],
			[ 999.99, 0, [ ], "float, no options" ], // with no options expects zero decimals
			[ - 999.99, 0, [ ], "negative float, no options" ],  // leading negative sign not allowed
			[ 999, 0, [ ], "int passed, no options" ], // with no options length defaults to 1
			[ 9, 1, [ ], "int passed, no options" ], // with no options length defaults to 1
			[ - 999, 0, [ ], "negative int passed, no options" ], // leading negative sign not allowed
		];

		foreach ( $test_data as $data ) {
			list( $input, $expected, $options, $msg ) = $data;
			$this->assertEquals(
				$expected,
				Validate::validateFloat( $input, $options ),
				$msg
			);
		}
	}

	/**
	 * @covers \Freshsauce\Validate::validateBool()
	 */
	public function testValidateBool() {

		$test_data = [
			[ true, true, "true passed" ],
			[ false, true, "false passed" ],
			[ 'some string', false, "random string" ],
			[ '12345', false, "numeric string" ],
			[ [ ], false, "array" ],
			[ 1, false, "int" ],
		];

		foreach ( $test_data as $data ) {
			list( $input, $expected, $msg ) = $data;
			if ( $expected === true ) {
				$this->assertTrue(
					Validate::validateBool( $input ),
					$msg
				);
			} else {
				$this->assertFalse(
					Validate::validateBool( $input ),
					$msg
				);
			}
		}
	}

	/**
	 * @covers \Freshsauce\Validate::validateString()
	 */
	public function testValidateString() {

		$test_data = [
			[ '0', true, [ 'max_len' => 11 ], "zero string" ],
			[ '12345678901', true, [ 'max_len' => 11 ], "match max length" ],
			[ '123456789012', false, [ 'max_len' => 11 ], "over max length" ],
			[ '123456789012', true, [ ], "string no options" ],
			[ 123456789012, true, [ ], "int no options" ],
			[ 1234567890, true, [ 'max_len' => 11 ], "int under max length" ],
			[ 12345678901, true, [ 'max_len' => 11 ], "int max length" ],
			[ 123456789012, false, [ 'max_len' => 11 ], "int over max length" ],
			[ 123456789012.00, false, [ 'max_len' => 11 ], "float over max length" ],
			[ 12345678.01, true, [ 'max_len' => 11 ], "float at max length" ],
			[ 123456789.01, false, [ 'max_len' => 11 ], "float over max length" ],
		];

		foreach ( $test_data as $data ) {
			list( $input, $expected, $options, $msg ) = $data;
			if ( $expected === true ) {
				$this->assertTrue(
					Validate::validateString( $input, $options ),
					$msg
				);
			} else {
				$this->assertFalse(
					Validate::validateString( $input, $options ),
					$msg
				);
			}
		}
	}

	/**
	 * @covers \Freshsauce\Validate::validateUsername()
	 */
	public function testValidateUsername() {

		$test_data = [
			[ 'a', false, [ 'min_len' => 2, 'max_len' => 50 ], "to short" ],
			[ 'abc', true, [ 'min_len' => 2, 'max_len' => 3 ], "maximum" ],
			[ 'abcd', false, [ 'min_len' => 2, 'max_len' => 3 ], "over maximum" ],
			[ '9er', false, [ 'min_len' => 2, 'max_len' => 50 ], "bad leading number" ],
			[ '_er', false, [ 'min_len' => 2, 'max_len' => 50 ], "bad leading underscore" ],
			[ 'This__should-be--ok-now', true, [ 'min_len' => 2, 'max_len' => 50 ], "ok random string" ],
			[ 'This__should-be--ok-now&^%$£^**', false, [ 'min_len' => 2, 'max_len' => 50 ], "fail random string" ],
		];

		foreach ( $test_data as $data ) {
			list( $input, $expected, $options, $msg ) = $data;
			if ( $expected === true ) {
				$this->assertTrue(
					Validate::validateUsername( $input, $options ),
					$msg
				);
			} else {
				$this->assertFalse(
					Validate::validateUsername( $input, $options ),
					$msg
				);
			}
		}
	}

	/**
	 * @covers \Freshsauce\Validate::validateEnum()
	 */
	public function testValidateEnum() {

		$test_data = [
			[ 'a', false, ['values' => []], "empty array" ],
			[ 'a', true, ['values' => ['a']], "single element array" ],
			[ 'a', true, ['values' => ['a','a','b','c']], "repeated matches" ],
			[ 'b', true, ['values' => ['a','a','b','c']], "match non first element" ],
			[ 'z', false, ['values' => ['a']], "no-match single element array" ],
			[ 'z', false, ['values' => ['a','a','b','c']], "no-match repeated matches" ],
			[ 'z', false, ['values' => ['a','a','b','c']], "no-match match non first element" ],
		];

		foreach ( $test_data as $data ) {
			list( $input, $expected, $options, $msg ) = $data;
			if ( $expected === true ) {
				$this->assertTrue(
					Validate::validateEnum( $input, $options ),
					$msg
				);
			} else {
				$this->assertFalse(
					Validate::validateEnum( $input, $options ),
					$msg
				);
			}
		}
	}

	/**
	 * @covers \Freshsauce\Validate::validateDatetime()
	 */
	public function testValidateDatetime() {
		date_default_timezone_set('UTC');
		$test_data = [
			[ date('Y-m-d H:i:s'), 1, "date('Y-m-d H:i:s')" ],
			[ '0000-00-00 00:00:00', 1, "zero datetime" ],
			[ date('Y-m-d'), 0, "today date only" ],
			[ 'some text', 0, "text string" ],
			[ 1, 0, "An int" ],
			[ 99.01, 0, "A float" ],
		];

		foreach ( $test_data as $data ) {
			list( $input, $expected, $msg ) = $data;
			$this->assertEquals(
				$expected,
				Validate::validateDatetime( $input ),
				$msg
			);
		}
	}

	/**
	 * @covers \Freshsauce\Validate::validateEmail()
	 */
	public function testValidateEmail() {
		$test_data = [
			[ 'user@test.com', true, "1.valid email" ],
			[ 'a.user@test.com', true, "2.valid email" ],
			[ 'another.user@test.com', true, "3.valid email" ],
			[ '@test.com', false, "1.invalid email" ],
			[ 'user@test', false, "2.invalid email" ],
			[ 'user@', false, "3.invalid email" ],
		];

		foreach ( $test_data as $data ) {
			list( $input, $expected, $msg ) = $data;
			if ( $expected === true ) {
				$this->assertTrue(
					Validate::validateEmail( $input ),
					$msg
				);
			} else {
				$this->assertFalse(
					Validate::validateEmail( $input ),
					$msg
				);
			}
		}
	}


}

