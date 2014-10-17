<?hh

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
 * @see MString
 * @see MString::__construct()
 * @see str()
 *
 * @param string $string The string to box into an MString
 *
 * @return MString Returns the boxed string
 */
function S(?string $string = null) : MString {
	if ($string === null) {
		return null;
	} else {
		return new MString($string);
	}
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
function Sf(...) : MString {
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
function str(MString $string) : string {
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
function A(...) : MArray {
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
function arr(MArray $array) : array {
	return $array->toArray();
}

/******************** Parsing Numbers ********************/

/**
 * Wraps a number into it's respective MNumber class
 * The return values are as follows:
 *
 * If `$number` is an int, returns an MInteger containing 
 * the number.
 * 
 * If `$number` is a float, returns an MFloat containing
 * the number.
 *
 * if `$number` is a bool, returns an MBool containing
 * the number.
 *
 * @param mixed $number The number to wrap.
 *
 * @return MNumber An MNumber instance containing a representation
 * of the passed in `$number`.
 */
function N(mixed $number) : MNumber {
	if (is_int($number)) {
		return new MInteger($number);
	} else if (is_float($number)) {
		return new MFloat($number);
	} else if (is_bool($number)) {
		return new MBool($number);
	} else {
		throw new MNaNException($number);
	}
}

/**
 * Wraps an `int` into an `MInteger`
 *
 * @param int $int The `int` to wrap
 *
 * @return MInteger An instance of `MInteger` containing the
 * value of `$int`.
 */
function I(int $int) : MInteger {
	return (MInteger)N($int);
}

/**
 * Wraps a `float` into an `MFloat`
 *
 * @param float $float The `float` to wrap
 *
 * @return MFloat An instance of `MFloat` containing the
 * value of `$float`.
 */
function F(float $float) : MFloat {
	return (MFloat)N($float);
}

/**
 * Wraps a `bool` into an `MBoolean`
 *
 * @param bool $bool The `bool` to wrap
 *
 * @return MBoolean An instance of `MBoolean` containing the
 * value of `$bool`.
 */
function B(bool $bool) : MBoolean {
	return (MBoolean)N($bool);
}

/**
 * Unwraps an `MInteger` into an `int`.
 *
 * @param MInteger $integer The integer to unwrap
 *
 * @return int An `int` containing the value of `$integer`.
 */
function i(MInteger $integer) : int {
	return $integer->intValue();
}

/**
 * Unwraps an `MFloat` into a `float`.
 *
 * @param MFloat $float The float to unwrap
 * 
 * @return float A `float` containing the value of `$float`.
 */
function f(MFloat $float) : float {
	return $float->floatValue();
}

/**
 * Unwraps an `MBoolean` into a `bool`.
 *
 * @param MBoolean $boolean The boolean to unwrap
 *
 * @return bool A ``bool` containing the value of `$boolean`.
 */
function b(MBoolean $boolean) : bool {
	return $boolean->boolValue();
}

/******************** Boxing & Unboxing Ranges ********************/

/**
 * Creates a new `MRange` object using the specified location and length
 *
 * This function is a convenience function for creating `MRange` objects,
 * it has the same effect as using `new MRange($location, $length)`
 *
 * @see MRange
 * @see MRange::__construct()
 *
 * @param int $location The offset to start your range from
 * @param int $length The length of the range
 *
 * @return MRange Returns a new `MRange` object creating using the
 * values specified by `$location` and `$length`
 */
function MRangeMake(int $location, int $length) : MRange {
	return new MRange($location, $length);
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
function MLog(...) : void {
	$args = func_get_args();
	$str = call_user_func_array("Sf", $args);
	error_log($str);
}

/**
 * Outputs the log backtrace to the PHP error log or
 * the standard output if running in CLI mode
 * 
 * @return void
 */
function MLogStackTrace() : void {
	logBackTrace();
}

/**
 * Outputs the value of a variable to the PHP error log
 *
 * This function outputs the contents of  object
 * to the PHP error log
 *
 * @see MLog()
 *
 * @param mixed $object The object to output to the
 * error log
 *
 * @return void
 */
function MVarExport(MObject $object) : void {
	MLog(var_export($object, true));
}
	
/**
 * Kills the current application and returns $response to the client
 *
 * This function responds to the client using an instance of `MHTTPResponse`
 * specified by `$response` and kills the execution of the application
 * imediately after.
 *
 * @see MHTTPResponse
 *
 * @param MHTTPResponse $response The HTTP response to return to the client
 * before killing the application
 *
 * @return void
 */
function MDie(?MHTTPResponse $response = null, int $returnCode = 0) : void {
	MAppDelegate()->willTerminateWithResponse($response, $returnCode);
	
	if ($response !== null) {
		MSendResponse($response);
	}
	
	die($returnCode);
}

/******************** Getting the Application Object & Delegate ********************/

/**
 * Returns the currently running `MApplication` instance
 *
 * This function returns the `MApplication` instance that represents the
 * currently running application
 *
 * @see MApplication
 *
 * @return MApplication The current `MApplication` instance
 */
function MApp() : MApplication {
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
function MAppDelegate() : MApplicationDelegate {
	return MApp()->delegate();
}

/******************** HTTP Request & Response Helpers ********************/

/**
 * Returns the `MHTTPRequest` which contains all information about the current HTTP request
 *
 * This function returns the current `MHTTPRequest` object being processed by your
 * application
 *
 * @see MHTTPRequest
 *
 * @return MHTTPRequest The current `MHTTPRequest` object
 */
function MHTTPRequest() : MHTTPRequest {
	return MHTTPRequest::request();
}

/**
 * Sends a `MHTTPResponse` object to the client
 *
 * This funciton is used to send a HTTP response back to the client,
 * the response is represented by an instance of the `MHTTPResponse`
 * class
 *
 * @see MHTTPResponse
 *
 * @param MHTTPResponse $response The HTTP response to send back to the client
 *
 * @return void
 */
function MSendResponse(MHTTPResponse $response) : void {
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
function addLeadingSpaces(string $string, int $totalLength) : string {
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
function isRunningFromCommandLine() : bool {
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
function isRunningInSimulatedRequestMode() : bool {
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
function simulatedRequestFileName() : string {
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
function simulatedRequestName() : string {
	global $argv;
	if (isRunningFromCommandLine() && isRunningInSimulatedRequestMode()) {
		if (isset($argv[3])) {
			return $argv[3];
		}
	}
	return null;
}

