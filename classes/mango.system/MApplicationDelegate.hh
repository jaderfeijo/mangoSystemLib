<?hh // strict

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
 * Interface that defines the basic structure of an Application Delegate
 *
 * All Application Delegates should implement this interface
 *
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 *
 */
interface MApplicationDelegate {
	
	/**
	 * Called when the application is being run from the command
	 * line
	 *
	 * This method is called instead of didFinishLoading when the
	 * application is run from the command line
	 *
	 * The return code should indicate whether the execution was
	 * successful or not. Return 0 for successful and anything
	 * else otherwise.
	 *
	 * This function should serve a similar function as the main()
	 * function does in a standard C/C++ application
	 *
	 * @param MArray $arguments An array containing the arguments
	 * passed in from the command line
	 *
	 * @return int This function should return a code which indicates
	 * whether or not the execution was successful
	 */
	public function didFinishLaunchingFromCommandLineWithArguments(MArray<MString> $arguments) : int;

	/**
	 * Called once the application has just finished launching,
	 * before the control is passed on to the view controller
	 *
	 * This method gives you the first opportunity to configure your
	 * application and initialize whatever application wide resources
	 * and classes you need across your application
	 *
	 * @return void
	 */
	public function didFinishLaunching() : void;
	
	/**
	 * Called right after the view controller instance for the current
	 * request is created
	 *
	 * This method offers you the opportunity to perform further customization
	 * to your view controller on an application level
	 *
	 * @param MViewController $viewController The view controller instance
	 * that has been created
	 *
	 * @return void
	 */
	public function didCreateViewController(MViewController $viewController) : void;
	
	/**
	 * @todo update method description
	 *
	 * Called just before the application is about to return it's results to the client
	 * (or return code when running from the CLI) and be terminated.
	 *
	 * @param MHTTPResponse $response The response object that will be retunred to the
	 * client. This value is null when running from the command line
	 * @param int $returnCode The return code that will be returned to the system, this
	 * value is usually used when running from the command line
	 *
	 * @return void
	 */
	public function willSendResponse(MHTTPResponse $response) : bool;

	/**
	 * @todo
	 */
	public function didSendResponse(MHTTPResponse $response) : void;

	/**
	 * @todo
	 */
	public function willTerminate() : void;
	
}

