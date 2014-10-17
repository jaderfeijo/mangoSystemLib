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
 * This file contains the error handling functionality of mango
 *
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 *
 */

/******************** Internal Stuff ********************/

set_error_handler("mango_error_handler");
set_exception_handler("mango_exception_handler");

/**
 * @internal
 * 
 * Mango Framework error handler
 * 
 * The Mango Framework error handler. This function is called by PHP and
 * allows Mango to catch and handle every error that happens within the
 * Mango Framework domain.
 *
 * @param int	 $level The PHP error level
 * @param string $message The error message
 * @param string $file The file name where the error occoured
 * @param int	 $line The line number where the error has occoured
 * @param string $context A string defining the context in which the error
 * has happened
 *
 * @return bool This return value is read by PHP and determines wether or
 * not the error has been handled, if this function returns false, PHP
 * handles the error with it's default error handler
 */
function mango_error_handler($level, $message, $file, $line, $context) : bool {
	if ($level == E_ERROR || $level == E_PARSE || $level == E_CORE_ERROR || $level == E_COMPILE_ERROR || $level == E_USER_ERROR) {
		if (!MAppDelegate()->didRecoverFromError($level, $message, $file, $line, $context)) {
			logError($level, $message, $file, $line);
	
			if (!isRunningFromCommandLine() || isRunningInSimulatedRequestMode()) {
				$response = new MHTTPViewControllerResponse(new MErrorViewController(MHTTPResponse::RESPONSE_INTERNAL_SERVER_ERROR, N(MHTTPResponse::RESPONSE_INTERNAL_SERVER_ERROR), S("Internal Server Error"), S("Sorry but the page you are looking for could not be loaded due to an internal server error")));
				$response->setCode(MHTTPResponse::RESPONSE_INTERNAL_SERVER_ERROR);
				MDie($response);
			} else {
				MDie(null, 1);
			}
		}
	}
	return true;
}

/**
 * @internal
 *
 * Mango Framework uncaught exception handler
 *
 * Default Exception handler for mango. This gets called by the system
 * every time there is an uncaught exception thrown.
 *
 * @param Exception	$exception	The exception thrown
 *
 * @return void
 */
function mango_exception_handler($exception) : void {
	if (!MAppDelegate()->didRecoverFromUncaughtException($exception)) {
		logException($exception);

		if (!isRunningFromCommandLine() || isRunningInSimulatedRequestMode()) {
			$response = new MHTTPViewControllerResponse(new MErrorViewController(MHTTPResponse::RESPONSE_INTERNAL_SERVER_ERROR, N(MHTTPResponse::RESPONSE_INTERNAL_SERVER_ERROR), S("Internal Server Error"), S("Sorry but the page you are looking for could not be loaded due to an internal server error")));
			$response->setCode(MHTTPResponse::RESPONSE_INTERNAL_SERVER_ERROR);
			MDie($response);
		} else {
			MDie(null, 1);
		}
	}
}

/**
 * @internal
 *
 * @return array
 */
function backTrace($useBackTrace = null) : array {
	$backTrace = $useBackTrace;
	if (is_null($backTrace)) {
		$backTrace = debug_backtrace();
	}

	$backStrings = array();
	$skipNext = false;
	foreach ($backTrace as $currentBacktrace) {
		if ($currentBacktrace['file'] == __FILE__) {
			$skipNext = true;
		} else {
			if (!$skipNext) {
				$class = @$currentBacktrace['class'];
				$type = @$currentBacktrace['type'];
				$function = @$currentBacktrace['function'];
				$file = @$currentBacktrace['file'];
				$line = @$currentBacktrace['line'];
				array_unshift($backStrings, "{$class}{$type}{$function}() $file:$line");
			}
			$skipNext = false;
		}
	}

	for ($i = 0; $i < count($backStrings); $i++) {
		$currentBackString = $backStrings[$i];
		$index = $i + 1;
		$indexStr = addLeadingSpaces("$index.", 4);
		$backStrings[$i] = "Mango $indexStr $currentBackString";
	}

	return $backStrings;
}

/**
 * @internal
 *
 * @return string
 */
function backTraceString($backTrace = null) : string {
	return implode("\n", backTrace($backTrace));
}

/**
 * @internal
 *
 * @return void
 */
function logBackTrace($backTrace = null) : void {
	$backTrace = backTrace($backTrace);
	error_log("Mango Stack trace:");
	foreach ($backTrace as $backString) {
		error_log($backString);
	}
}

/**
 * @internal
 *
 * @return void
 */
function logError($level, $message, $file = null, $line = null, $backTrace = null) : void {
	$errType = "Unknown Error:";
	if ($level == E_ERROR) {
		$errType = "Error:";
	} else if ($level == E_WARNING) {
		$errType = "Warning:";
	} else if ($level == E_PARSE) {
		$errType = "Parse Error:";
	} else if ($level == E_NOTICE) {
		$errType = "Notice:";
	} else if ($level == E_CORE_ERROR) {
		$errType = "Core Error:";
	} else if ($level == E_CORE_WARNING) {
		$errType = "Core Warning:";
	} else if ($level == E_COMPILE_ERROR) {
		$errType = "Compile Error:";
	} else if ($level == E_COMPILE_WARNING) {
		$errType = "Compile Warning:";
	} else if ($level == E_USER_ERROR) {
		$errType = "Mango Error:";
	} else if ($level == E_USER_WARNING) {
		$errType = "Mango Warning:";
	} else if ($level == E_USER_NOTICE) {
		$errType = "Mango Notice:";
	}

	$logString = "$errType $message";

	if (!is_null($file)) {
		$logString .= " in $file";
	}

	if (!is_null($line)) {
		$logString .= " on line $line";
	}

	error_log($logString);
	logBackTrace($backTrace);
}

/**
 * @internal
 *
 * Issues a PHP warning
 *
 * @param string $message The warning message
 *
 * @return void
 */
function issueWarning($message) : void {
	logError(E_USER_WARNING, $message);
}

/**
 * @internal
 *
 * Logs a PHP exception into the default error log
 *
 * @param Exception $e The exception to log
 *
 * @return void
 */
function logException(Exception $e) : void {
	$message = $e->getMessage();
	$file = $e->getFile();
	$line = $e->getLine();
	$backTrace = $e->getTrace();
	if ($e instanceof MException) {
		$message = $e->message();
	}
	logError(E_USER_ERROR, $message, $file, $line, $backTrace);
}

