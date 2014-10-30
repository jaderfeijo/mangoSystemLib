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
 * This contains various utility Mango System functions you need
 * in order to perform basic operations in Mango.
 *
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
	
require_once('errors.hh');
require_once('types.hh');

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
function S(string $string) : MString {
	return new MString($string);
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
	return MString::stringWithFormat($args);
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
function A<T>(...) : MArray<T> {
	$args = func_get_args();
	if (count($args) == 1 && is_array($args[0])) {
		return MArray::withArray($args[0]);
	} else if (count($args) == 1 && $args[0] instanceof Traversable) {
		return MArray::withObjects($args[0]);
	} else if (count($args) == 1 && $args[0] instanceof MArray) {
		return MArray::withObjectsFromArray($args[0]);
	} else {
		return MArray::withArray($args);
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
 * @todo update
 *
 * Sends a `MHTTPResponse` object to the client
 *
 * This funciton is used to send a HTTP response back to the client,
 * the response is represented by an instance of the `MHTTPResponse`
 * class
 *
 * @todo explain MResponseAlreadySentException
 *
 * @see MHTTPResponse
 *
 * @param MHTTPResponse $response The HTTP response to send back to the client
 *
 * @return void
 */
function MSendResponse(MHTTPResponse $response) : bool {
	if (MHTTPResponse::responseSent()) {
		throw new MResponseAlreadySentException();
	}

	if (MAppDelegate()->willSendResponse($response) {
		header(sprintf("HTTP/1.1 %d %s", $response->code(), $response->responseString()));
		if ($response->headers()->count() > 0) {
			foreach ($response->headers()->allKeys()->toArray() as $header) {
				header(sprintf("%s: %s", $header, $response->headers()->objectForKey($header)));
			}
		}
		
		MExec($response->body(), $body) {
			echo $body;
		});

		return true;
	} else {
		return false;
	}
}

/******************** Miscelaneous ********************/

/**
 * @todo update
 *
 * Main Mango Function
 *
 * This function shoud be called from within an application's `index`
 * file and is responsible for initialising the MApplication instance
 * and kick starting Mango Framework for the given application.
 *
 * @param array $argv An array containing CLI arguments passed to the
 * application
 * @param ?MString $delegateClass A String containing the name of the
 * Application Delegate class to use.
 *
 * @see MApplicationDelegate
 *
 * @return int @todo
 */
function MMain(array $argv) : int {
	try {
		$arguments = new MMutableArray();
		foreach ($argv as $arg) {
			$arguments->addObject(S($arg));
		}

		$application = null;
	
		if (MFile::fileExists("resources/manifest.xml")) {
			$xmlManifest = simplexml_load_file("resources/manifest.xml");
			MExec($xmlManifest['delegate'], $delegateClass ==> {
				try {
					$delegate = MObject::newInstanceOfClass($delegateClass);
					$defaultNamespace = MApplicationNamespace::parseFromXMLElement($xmlManifest, S("application"));
					$application = new MApplication($delegate, $defaultNamespace, $arguments);
					
					MExec($xmlManifest['errorClass'], $errorViewControllerClass ==> {
						$application->setErrorViewControllerClass(S($errorViewControllerClass));
					});
				} catch (Exception $e) {
					throw new MParseErrorException(S("resources/manifest.xml"), null, null, $e);
				}
			}, () ==> {
				throw new MParseErrorException(S("resources/manifest.xml"), null, S("No application delegate class defined"));
			});
		} else {
			throw new MFileNotFoundException(S('resources/manifest.xml'));
		}
	
		if ($application !== null) {
			return $application->run();
		} else {
			throw new MException(S("Failed to initialise application!"));	
		}
	} catch (Exception $e) {
		logException($e);

		$viewController = new MErrorViewController(MHTTPResponseCode::InternalServerError, I(MHTTPResponseCode::InternalServerError), S("Internal Server Error"), S("Sorry but the page you are looking for could not be loaded due to an internal server error"));
		$response = new MHTTPViewControllerResponse($viewController);
		MSendResponse($response);

		return 1;
	}
}

/**
 * Initializes an object in case it is null using the function passed in
 * the `$initializer` parameter and returns the initialized object.
 *
 * This function is useful when you need an object to be initialized
 * before it is returned. It checks if the object is already initialized
 * and returns it, and if it isn't it calls the initializer function
 * and returns it's results.
 * 
 * @param ?T $object The object to be initialized
 * @param MInitializerCallback<T> $callback The callback to be used to initialize
 * the object if necessary.
 *
 * @return The initialized object.
 */
function MInit<T>(?T $object, MInitializerCallback<T> $callback) : T {
	if ($object === null) {
		return $initializer();
	} else {
		return $object;
	}
}

/**
 * Initializes an object with another object if it's not null, and without it if
 * the object is null.
 *
 * This method is a convenience method which allows you to initialize an object
 * conditionally based on whether or not the passed object is null.
 *
 * @param ?Tw $object The object to pass to the `$withCallback`.
 * @param MInitWithCallback<Tw, To> $withCallback The callback that is called the
 * `$object` is not null.
 * @param MInitWithoutCallback<To> $withoutCallback The callback that is called
 * if `$object` is null.
 *
 * @return To The object resulting of the initialization.
 */
function MInitWith<Tw, To>(?Tw $object, MInitWithCallback<Tw, To> $withCallback, MInitWithoutCallback<To> $withoutCallback) : To {
	if ($object !== null) {
		return $withCallback($object);
	} else {
		return $withoutCallback();
	}
}	

/**
 * Ensures that an object is not null before executing a method.
 *
 * This is a utility function to safely execute methods on objects that can
 * be null.
 *
 * This function checks if the value of `$object` is different than `null`
 * and if it isn't, it calls the function specified in `$callback` with
 * the object as an argument.
 *
 * If the value of `$object` is null, `$callback` is never called.
 *
 * Using this function has the same effect as writing:
 *
 *  $object = someNullableMethod();
 *  if ($object !== null) {
 * 	   $object->method();
 *  } 
 *
 * In this case, you woud write:
 *
 *  MExec(someNullableMethod(), $object ==> {
 *  	$object->method();
 *  }, null);
 *
 * In cases where you need to perform some action when the object is null simply
 * pass in a `MNullCallback` as the third argument
 *
 *  MExec(someNullableMethod(), $object ==> {
 *     $object->method();
 *  }, () == > {
 *     throw new MException();
 *  });
 *
 * @param ?T $object An object to be passed to the callback if `$object` is not
 * null.
 * @param MExecuteCallback<T> $callback The callback method to call in case `$object`
 * is not null.
 * @param MNullCallback $callback The callback method to call in case `$object` is
 * null.
 *
 * @return void
 */
function MExec<T>(?T $object, MExecuteCallback<T> $callback, ?MNullCallback $nullCallback = null) : void {
	if ($object !== null) {
		$callback($object);
	} else {
		if ($nullCallback !== null) {
			$nullCallback($object);
		}
	}
}

