<?php

	/*
	 * Copyright (c) 2011 Movinpixel Ltd. All rights reserved.
	 *
	 * Redistribution and use in source and binary forms, with or without
	 * modification, are permitted provided that the following conditions
	 * are met:
	 * 1. Redistributions of source code must retain the above copyright
	 *    notice, this list of conditions and the following disclaimer.
	 * 2. Redistributions in binary form must reproduce the above copyright
	 *    notice, this list of conditions and the following disclaimer in the
	 *    documentation and/or other materials provided with the distribution.
	 * 3. Neither the name of the company nor the names of its contributors
	 *    may be used to endorse or promote products derived from this software
	 *    without specific prior written permission.
	 *
	 * THIS SOFTWARE IS PROVIDED BY MOVINPIXEL AND ITS CONTRIBUTORS ``AS IS'' AND
	 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
	 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
	 * ARE DISCLAIMED.  IN NO EVENT SHALL MOVINPIXEL OR ITS CONTRIBUTORS BE LIABLE
	 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
	 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
	 * OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
	 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
	 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
	 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
	 * SUCH DAMAGE.
	 */
	 
	/**
	 * This file initializes the Mango Framework environment and contains the
	 * basic mango functions you need to perform basic operations in Mango
	 *
	 * Including this file into your top-level Mango Framework script is required
	 * in order to use Mango Framework.
	 *
	 * This is usually done inside your index.php file by using the require() function
	 *
	 * @example require('system/MangoFramework.php');
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 *
	 */
	
	require_once('errors.php');
	require_once('packages.php');
	
	/******************** Boxing & Unboxing Strings ********************/
	
	/**
	 * Wraps up a string into an MString object
	 *
	 * This function boxes a string inside an MString object, this
	 * is essentially a convenience method and has the same effect
	 * as using new MString("your string");
	 *
	 * The inverse equivalent of this function is the mango str()
	 * function
	 *
	 * This function returns null if null is passed as the string
	 * parameter
	 *
	 * @see MString
	 * @see MString::__construct()
	 * @see str()
	 *
	 * @param string $string The string to box into an MString
	 *
	 * @return MString Returns the boxed string
	 */
	function S($string = null) {
		if (is_null($string)) {
			return null;
		}
		return new MString((string)$string);
	}
	
	/**
	 * Creates a new MString using the specified format
	 *
	 * This function works the same way as PHP's sprintf
	 * function but instead of outputting a string, it
	 * outputs a Mango String (MString) object. This is a
	 * convenience method, it has the same effect as using
	 * MString::stringWithFormat();
	 *
	 * @see MString
	 * @see MString::stringWithFormat()
	 *
	 * @param string $format The format string to use
	 * @param string[] $args The arguments to use inside the
	 * formatted string
	 *
	 * @return MString Returns the formatted String
	 */
	function Sf() {
		$args = func_get_args();
		return MString::_stringWithFormat($args);
	}
	
	/**
	 * Unwraps string out of an MString object
	 *
	 * This function unboxes a string inside an MString object, this
	 * is essentially a convenience method and has the same effect
	 * as using
	 * $mStringObject->stringValue();
	 * or
	 * (string)$mStringObject;
	 *
	 * The inverse equivalent of this function is the mango S()
	 * function
	 *
	 * @see MString
	 * @see MString::stringValue()
	 * @see S()
	 *
	 * @param MString $string The MString object to unbox
	 *
	 * @return string Returns the unboxed string
	 */
	function str(MString $string = null) {
		if (is_null($string)) {
			return null;
		}
		return $string->stringValue();
	}
	
	/******************** Boxing & Unboxing Arrays ********************/
	
	/**
	 * Wraps an array into an MArray object
	 *
	 * This function boxes an array inside an MArray object, this
	 * is essentially a convenience method and has the same effect
	 * as using
	 * new MArray($array);
	 *
	 * The inverse equivalent of this function is the mango arr()
	 * function
	 *
	 * @see MArray
	 * @see MArray::__construct()
	 * @see arr()
	 *
	 * @param array $array An array, or a series of objects to wrap inside an MArray
	 *
	 * @return MArray Returns the boxed array
	 */
	function A() {
		$args = func_get_args();
		if (count($args) == 1 && is_array($args[0])) {
			return new MArray($args[0]);
		} else {
			return new MArray($args);
		}
	}
	
	/**
	 * Unwraps an array out of an MArray object
	 *
	 * This function unboxes an array inside an MArray object, this
	 * is essentially a convenience method and has the same effect
	 * as using
	 * $mArrayObject->toArray();
	 *
	 * The inverse equivalent of this function is the mango A()
	 * function
	 *
	 * @see MArray
	 * @see MArray::toArray()
	 * @see A()
	 *
	 * @param MArray $array The MArray to unbox into an array
	 *
	 * @return array Returns the unboxed array
	 */
	function arr(MArray $array = null) {
		if (is_null($array)) {
			return null;
		}
		return $array->toArray();
	}
	
	/******************** Boxing & Unboxing Numbers ********************/
	
	/**
	 * Wraps a number into an MNumber object
	 *
	 * This function boxes a number inside an MNumber object, this
	 * is essentially a convenience method and has the same effect
	 * as using
	 * new MNumber($number)
	 *
	 * @see MNumber
	 * @see MNumber::__construct()
	 * @see i()
	 * @see f()
	 * @see b()
	 *
	 * @param number $number The number to box into an MNumber
	 *
	 * @return MNumber Returns the boxed number
	 */
	function N($number) {
		return MNumber::parse($number);
	}
	
	/**
	 * Unwraps an integer out of an MNumber object
	 *
	 * This function unboxes an integer inside an MNumber object, this
	 * is essentially a convenience method and has the same effect
	 * as using
	 * $mNumberObject->intValue();
	 *
	 * @see MNumber
	 * @see MNumber::intValue()
	 * @see N()
	 * @see f()
	 * @see b()
	 *
	 * @param MNumber $number The MNumber to unbox into an integer
	 *
	 * @return int Returns the unboxed integer
	 */
	function i(MNumber $number = null) {
		if (is_null($number)) {
			return null;
		}
		return $number->intValue();
	}
	
	/**
	 * Unwraps a float out of an MNumber object
	 *
	 * This function unboxes a float inside an MNumber object, this
	 * is essentially a convenience method and has the same effect
	 * as using
	 * $mNumberObject->floatValue()
	 *
	 * @see MNumber
	 * @see MNumber::floatValue()
	 * @see N()
	 * @see i()
	 * @see b()
	 *
	 * @param MNumber $number The MNumber to unbox into a float
	 *
	 * @return float Returns the unboxed float
	 */
	function f(MNumber $number = null) {
		if (is_null($number)) {
			return null;
		}
		return $number->floatValue();
	}
	
	/**
	 * Unwraps a bool out of an MNumber object
	 *
	 * This function unboxes a bool inside an MNumber object, this
	 * is essentially a convenience method and has the same effect
	 * as using
	 * $mNumberObject->boolValue()
	 *
	 * @see MNumber
	 * @see MNumber::boolValue()
	 * @see N()
	 * @see i()
	 * @see f()
	 *
	 * @param bool $number The MNumber to unbox into a boolean
	 *
	 * @return bool Returns the unboxed bool
	 */
	function b(MNumber $number = null) {
		if (is_null($number)) {
			return null;
		}
		return $number->boolValue();
	}
	
	/******************** Boxing & Unboxing Ranges ********************/
	
	/**
	 * Creates a new MRange object using the specified location and length
	 *
	 * This function is a convenience function for creating MRange objects,
	 * it has the same effect as using
	 * new MRange($location, $length)
	 *
	 * @see MRange
	 * @see MRange::__construct()
	 *
	 * @param number $location The offset to start your range from
	 * @param number $length The length of the range
	 *
	 * @return MRange Returns a new MRange object creating using the
	 * values specified by $location and $length
	 */
	function MRangeMake($location, $length) {
		return new MRange($location, $length);
	}
	
	/******************** Asserting Data Types & Values ********************/
	
	/**
	 * Asserts weather a variable or set of variables are of a certain type
	 *
	 * This function can be used to circumvent PHP's lack of strong typing.
	 * By calling this method at the beginning of your functions you ensure that
	 * each parameter passed to your function is of a certain type.
	 *
	 * @example
	 * function setArray($mArray, $mString) {
	 * 	MAssertTypes('MArray', $mArray, 'MString', $mString);
	 *  // If we reach this point, $mArray IS an instance of the MArray class
	 *  // and $mString IS an instance of MString
	 * }
	 * 
	 * This function can be used to enforce any type to any variable, including
	 * PHP primitives and any Mango class or any classes you create. Below is
	 * a list of supported primitives:
	 * array
	 * bool
	 * boolean
	 * callback
	 * double
	 * float
	 * int
	 * integer
	 * long
	 * resource
	 * string
	 *
	 * MAssertTypes throws an MInvalidDataTypeException if the variable type
	 * does not match the specified data type
	 *
	 * @see MInvalidDataTypeException
	 *
	 * @param string $type A string with the type to enforce
	 * @param mixed $var The variable to enforce the type on
	 * @param array $args ...
	 *
	 * @return void
	 */
	function MAssertTypes() {
		import('mango.system.exceptions.MInvalidDataTypeException');	
		$args = func_get_args();
		for ($i = 0; $i < count($args); $i++) {
			$type = $args[$i];
			$value = $args[++$i];
			if (!is_null($value)) {
				if ($type == "array") {
					if (!is_array($value)) {
						throw new MInvalidDataTypeException(S($type), S(gettype($value)));
					}
				} else if ($type == "bool" || $type == "boolean") {
					if (!is_bool($value)) {
						throw new MInvalidDataTypeException(S($type), S(gettype($value)));
					}
				} else if ($type == "callback") {
					if (!is_callable($value)) {
						throw new MInvalidDataTypeException(S($type), S(gettype($value)));
					}
				} else if ($type == "double") {
					if (!is_double($value)) {
						throw new MInvalidDataTypeException(S($type), S(gettype($value)));
					}
				} else if ($type == "float") {
					if (!is_float($value)) {
						throw new MInvalidDataTypeException(S($type), S(gettype($value)));
					}
				} else if ($type == "int") {
					if (!is_int($value)) {
						throw new MInvalidDataTypeException(S($type), S(gettype($value)));
					}
				} else if ($type == "integer") {
					if (!is_integer($value)) {
						throw new MInvalidDataTypeException(S($type), S(gettype($value)));
					}
				} else if ($type == "long") {
					if (!is_long($value)) {
						throw new MInvalidDataTypeException(S($type), S(gettype($value)));
					}
				} else if ($type == "resource") {
					if (!is_resource($value)) {
						throw new MInvalidDataTypeException(S($type), S(gettype($value)));
					}
				} else if ($type == "string") {
					if (!is_string($value)) {
						throw new MInvalidDataTypeException(S($type), S(gettype($value)));
					}
				} else {
					if (!is_a($value, $type)) {
						throw new MInvalidDataTypeException(S($type), S(gettype($value)));
					}
				}
			}
		}
	}
	
	/**
	 * Asserts if a number is within a certain range
	 *
	 * This function asserts that the specified number is smaller than
	 * max and greater than min. If it is not, this function throws
	 * an MNumberOutOfRangeException 
	 * 
	 * @param MNumber $number The number to assert
	 * @param MNumber $min The minimum value for the number
	 * @param MNumber $max The maximum value for the number
	 *
	 * @return void
	 */
	function MAssertRange(MNumber $number, MNumber $min, MNumber $max) {
		if (!$number->isWithinBounds($min, $max)) {
			import('mango.system.exceptions.MNumberOutOfRangeException');
			throw new MNumberOutOfRangeException($number, $min, $max);
		}
	}
	
	/******************** Writing to the System Log ********************/
	
	/**
	 * Outputs information to the PHP error log
	 *
	 * This function outputs a formatted string to the PHP error log.
	 * It takes the same parameters as Sf(), but instead of returning
	 * the formatted string, it outputs it to the error log by using
	 * PHP's error_log()
	 *
	 * @see Sf()
	 *
	 * @param string $format The format string to use
	 * @param string[] $args The arguments to use inside the
	 * formatted string
	 *
	 * @return void
	 */
	function MLog() {
		$args = func_get_args();
		$str = call_user_func_array("Sf", $args);
		error_log($str);
	}
	
	function MLogStackTrace() {
		logBackTrace();
	}
	
	/**
	 * Outputs the value of a variable to the PHP error log
	 *
	 * This function outputs the contents of a variable or object
	 * to the PHP error log
	 *
	 * @see MLog()
	 *
	 * @param mixed $object The object or variable to output to the
	 * error log
	 *
	 * @return void
	 */
	function MVarExport($object) {
		MLog(var_export($object, true));
	}
	
	/**
	 * Kills the current application and returns $response to the client
	 *
	 * This function responds to the client using an instance of HTTPResponse
	 * specified by $response and kills the execution of the application
	 * imediately after.
	 *
	 * @see MHTTPResponse
	 *
	 * @param MHTTPResponse $response The HTTP response to return to the client
	 * before killing the application
	 *
	 * @return void
	 */
	function MDie(MHTTPResponse $response = null, $returnCode = 0) {
		MAssertTypes('int', $returnCode);
		
		MAppDelegate()->willTerminateWithResponse($response, $returnCode);
		
		if (!is_null($response)) {
			MSendResponse($response);
		}
		
		die($returnCode);
	}
	
	/******************** Getting the Application Object & Delegate ********************/
	
	/**
	 * Returns the current MApplication instance
	 *
	 * This function returns the MApplication instance that represents the
	 * currently running application
	 *
	 * @see MApplication
	 *
	 * @return MApplication The current MApplication instance
	 */
	function MApp() {
		return MApplication::sharedApplication();
	}
	
	/**
	 * Returns the current MApplicationDelegate instance
	 *
	 * This function returns the MApplicationDelegate instance that represents
	 * the application delegate for the currently running application
	 *
	 * @see MApplicationDelegate
	 *
	 * @return MApplicationDelegate The current MApplicationDelegate instance
	 */
	function MAppDelegate() {
		return MApp()->delegate();
	}
	
	/******************** HTTP Request & Response Helpers ********************/
	
	/**
	 * Returns the MHTTPRequest which contains all information about the current HTTP request
	 *
	 * This function returns the current MHTTPRequest object being processed by your
	 * application
	 *
	 * @see MHTTPRequest
	 *
	 * @return MHTTPRequest The current MHTTPRequest object
	 */
	function MHTTPRequest() {
		return MHTTPRequest::request();
	}
	
	/**
	 * Sends a MHTTPResponse object to the client
	 *
	 * This funciton is used to send a HTTP response back to the client,
	 * the response is represented by an instance of the MHTTPResponse
	 * class
	 *
	 * @see MHTTPResponse
	 *
	 * @param MHTTPResponse $response The HTTP response to send back to the client
	 *
	 * @return void
	 */
	function MSendResponse(MHTTPResponse $response) {
		$body = $response->body();
		
		header(sprintf("HTTP/1.1 %d %s", $response->code(), $response->responseString()));
		if ($response->headers()->count() > 0) {
			foreach ($response->headers()->allKeys()->toArray() as $header) {
				header(sprintf("%s: %s", $header, $response->headers()->objectForKey($header)));
			}
		}
		if ($body) {
			echo $body;
		}
	}
	
	/******************** Internal Stuff ********************/
	
	/**
	 * @internal
	 *
	 * return string
	 */
	function addLeadingSpaces($string, $totalLength) {
		return str_repeat(" ", $totalLength - strlen($string)) . $string;
	}

	/**
	 * @internal
	 *
	 * Returns a boolean indicating whether or not this application
	 * was invoked from the command line.
	 *
	 * @return bool true if this application is running from the command
	 * line, false otherwise.
	 */
	function isRunningFromCommandLine() {
		if (PHP_SAPI == 'cli') {
			return true;
		}
		return false;
	}

	/**
	 * @internal
	 *
	 * Returns a boolean indicating whether or not this application
	 * is running in simulated request mode.
	 *
	 * @return bool true if this application is running in simulated
	 * request mode.
	 */
	function isRunningInSimulatedRequestMode() {
		global $argv;
		if (count($argv) > 0) {
			if ($argv[1] == "--simulate-request") {
				return true;
			}
		}
		return false;
	}

	/**
	 * @internal
	 *
	 * Returns the file name containing the simulated request
	 * parameters. This file name is specified in the command
	 * line when calling the application in simulated mode.
	 *
	 * @see isRunningInSimulatedRequestMode()
	 * @see "--simulate-request"
	 *
	 * @return string The file name containing the simulated request
	 * or null if not running in simulated request mode or if no
	 * file name is specified.
	 */
	function simulatedRequestFileName() {
		global $argv;
		if (isRunningFromCommandLine() && isRunningInSimulatedRequestMode()) {
			if (isset($argv[2])) {
				return $argv[2];
			}
		}
		return null;
	}

	/**
	 * @internal
	 *
	 * Returns the name of the request that should be used
	 *
	 * @see isRunningInSimulaterRequestMode()
	 * @see "--simulate-request"
	 *
	 * @return string The name of the request that should be used
	 */
	function simulatedRequestName() {
		global $argv;
		if (isRunningFromCommandLine() && isRunningInSimulatedRequestMode()) {
			if (isset($argv[3])) {
				return $argv[3];
			}
		}
		return null;
	}

?>