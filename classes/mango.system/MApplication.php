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
	
	package('mango.system');
	
	import('mango.system.io.*');
	import('mango.system.html.*');
	import('mango.system.exceptions.*');
	
	/**
	 * This class holds a representation of the currently running Mango
	 * Application. It controls the execution and allows you to configure
	 * several different aspects of your Mango Application
	 *
	 * You should only ever create one instance of this class, this is usually done
	 * in the index.php file. To access the MApplication singleton instance that
	 * represents the currently running Application use
	 * MApplication::sharedApplication()
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 *
	 * @package mango.system
	 *
	 */
	class MApplication extends MObject {
		
		protected static $_application;
		
		/**
		 * Returns the MApplication instance that represents the currently
		 * running application
		 *
		 * @return MApplication The currently running Application instance
		 */
		public static function sharedApplication() {
			if (!MApplication::$_application) {
				MApplication::$_application = new MApplication();
			}
			return MApplication::$_application;
		}
		
		//
		// ************************************************************
		//
		
		protected $_delegate;
		protected $_errorViewControllerClass;
		protected $_defaultNamespace;
		protected $_rootViewController;
		protected $_commandName;
		protected $_commandLineArguments;
		
		/**
		 * Creates a new MApplication instance with the specified delegate class
		 * If no delegate class is specified the system looks for the 'manifest.xml'
		 * file inside the 'resources' folder and parses it
		 *
		 * @param MString $delegateClass A string containing the fully qualified class
		 * name for this application's delegate, or null.
		 *
		 * @return MApplication The MApplication instance which has just been created
		 */
		public function __construct(MString $delegateClass = null) {
			parent::__construct();
			
			$this->_delegate = null;
			$this->_errorViewControllerClass = null;
			$this->_defaultNamespace = null;
			$this->_rootViewController = null;
			$this->_commandName = null;
			$this->_commandLineArguments = null;
			
			if (!$this->isRoutingEnabled()) {
				$this->enableRouting();
				
				$redirect = new MHTTPResponse(MHTTPResponse::RESPONSE_FOUND);
				$redirect->addHeader(S("Location"), MHTTPRequest()->url());
				
				MDie($redirect);
			}
			
			if ($delegateClass) {
				$this->_delegate = MObject::newInstanceOfClass($delegateClass);
			} else if (MFile::fileExists("resources/manifest.xml")) {
				$xmlManifest = simplexml_load_file("resources/manifest.xml");
				$this->_delegate = MObject::newInstanceOfClassWithParameters(S($xmlManifest['delegate']), A($this));
				$this->_errorViewControllerClass = S($xmlManifest['errorClass']);
				try {
					$this->_defaultNamespace = MApplicationNamespace::parseFromXMLElement($xmlManifest, S("application"));
				} catch (Exception $e) {
					throw new MParseErrorException(S("resources/manifest.xml"), null, null, $e);
				}
			} else {
				$this->_delegate = new MApplicationDelegate($this);
			}
			
			MApplication::$_application = $this;
		}
		
		/******************** Protected ********************/
		
		/**
		 * @internal
		 *
		 * @return bool
		 */
		protected function isRoutingEnabled() {
			// TODO: In the future maybe this check could be more sophisticated than this
			// and actually look inside the configuration file to check if everything is
			// setup properly
			return MFile::fileExists('.htaccess');
		}
		
		/**
		 * @internal
		 *
		 * @return void
		 */
		protected function enableRouting() {
			MLog("[EnableRouting]: Creating '.htaccess' file for URL routing…");
		
			$fileStream = new MFileOutputStream(new MFile(S(".htaccess")));
			$writer = new MStreamWriter($fileStream);
			$writer->writeLine(S("# Mango URL Routing Code"));
			$writer->writeLine(S("RewriteEngine On"));
			$writer->writeLine(S("RewriteRule . index.php"));
			$writer->close();
			
			MLog("[EnableRouting]: File created");
		}
		
		/**
		 * @internal
		 *
		 * @return void
		 */
		protected function parseCommandLineArguments() {
			global $argv;
			if ($this->isRunningFromCommandLine()) {
				$command = null;
				$arguments = new MMutableArray();
				foreach ($argv as $argument) {
					if (is_null($command)) {
						$command = S($argument);
					} else {
						$arguments->addObject(S($argument));
					}
				}
				$this->_commandName = $command;
				$this->_commandLineArguments = $arguments;
			}
		}
		
		/******************** Properties ********************/
		
		/**
		 * Returns the Application Delegate instance used by this application
		 *
		 * @return MApplicationDelegate The Application Delegate instance for this
		 * application
		 */
		public function delegate() {
			return $this->_delegate;
		}
		
		/**
		 * Sets the MErrorViewController subclass this application should use for handling
		 * errors
		 *
		 * This should be the fully qualified class name
		 *
		 * If this value is set to null or an empty string, the application will use the
		 * system's default error view controller
		 *
		 * @param MString $errorViewControllerClass The fully qualified class name to use
		 * for error handling
		 *
		 * @return void
		 */
		public function setErrorViewControllerClass(MString $errorViewControllerClass = null) {
			$this->_errorViewControllerClass = $errorViewControllerClass;
		}
		
		/**
		 * Returns the MErrorViewController subclass this application will use for handling
		 * errors
		 *
		 * @return MString A string containing the fully qualified error view controller
		 * class name
		 */
		public function errorViewControllerClass() {
			if ($this->_errorViewControllerClass) {
				if (!$this->_errorViewControllerClass->isEmpty()) {
					return $this->_errorViewControllerClass;
				}
			}
			return S("mango.system.MErrorViewController");
		}
		
		/**
		 * Returns the default namespace for this application.
		 *
		 * @return MApplicationNamespace The default namespace used by this application
		 */
		public function defaultNamespace() {
			if (!$this->_defaultNamespace) {
				$this->_defaultNamespace = new MApplicationNamespace(S(""));
			}
			return $this->_defaultNamespace;
		}
		
		/**
		 * Returns the root view controller for this current instance of the application.
		 * The root view controller is returned depending on which parameters are called.
		 * The Application class parses the request URL and breaks down the different elements
		 * into controller url, and parameters. This determines which view controller should
		 * be instanced and called. The appropriate view controller is instanced according to
		 * those parameters and returned by this method.
		 *
		 * @return MViewController The root view controller for this instance of the application
		 */
		protected function rootViewController() {
			if (!$this->_rootViewController) {
				$this->_rootViewController = $this->defaultNamespace()->viewControllerForPath(MHTTPRequest()->arguments());
			}
			return $this->_rootViewController;
		}
		
		/******************** Methods ********************/
		
		/**
		 * Returns a boolean which indicates whether or not this application was called from
		 * the command line, or whether it is being run as a result of a request to the
		 * server.
		 *
		 * @return bool Returns true if the application is being run from the command line.
		 * false otherwise.
		 */
		public function isRunningFromCommandLine() {
			if (isRunningFromCommandLine() && !isRunningInSimulatedRequestMode()) {
				return true;
			}
			return false;
		}
		
		/**
		 * Returns a string containing the command name that was used to invoke this
		 * application from the command line.
		 *
		 * @return MString A string containing the command name used to invoke this
		 * application from the command line.
		 */
		public function commandName() {
			if (!$this->_commandName) {
				$this->parseCommandLineArguments();
			}
			return $this->_commandName;
		}
		
		/**
		 * Returns an array containing all the arguments that were passed to this mango
		 * application when it was invoked from the command line.
		 *
		 * @return MArray An array containing the arguments that were passed in from the
		 * command line.
		 */
		public function commandLineArguments() {
			if (!$this->_commandLineArguments) {
				$this->parseCommandLineArguments();
			}
			return $this->_commandLineArguments;
		}
		
		/**
		 * This function needs to be called after creating your instance of MApplication.
		 * This is the entry point for your application's execution
		 *
		 * When you call this function, the Mango environment parses all the information
		 * it needs, sets itself up and boots up its classes
		 *
		 * This is also where routing occours. The system finds the controller class for
		 * the specified URL and loads it
		 *
		 * The system takes care of handling top-level errors that may occur. For example,
		 * if the URL requested by the user has no registered controllers, the system returns
		 * a 404 View to the user and responds with the appropriate HTTP code.
		 *
		 * The same thing happens if an exception is thrown and not caught or if another error
		 * occurs in the execution of your code. The system catches the error, outputs the
		 * appropriate error information to the error log and returns an 500 Internal Server
		 * Error view to the client
		 *
		 * @return void
		 */
		public function run() {
			$response = null;
			$returnCode = 0;
			
			if ($this->isRunningFromCommandLine()) {
				$returnCode = $this->delegate()->didFinishLaunchingFromCommandLineWithArguments($this->commandLineArguments());
			} else {
				$viewController = null;
				
				try {
					$this->delegate()->didFinishLaunching();
					$viewController = $this->rootViewController();
				} catch (MBadRequestException $e) {
					logException($e);
					$viewController = MObject::newInstanceOfClassWithParameters($this->errorViewControllerClass(), A(
						MHTTPResponse::RESPONSE_BAD_REQUEST, N(MHTTPResponse::RESPONSE_BAD_REQUEST), S("Bad Request"), $e->description()
					));
				} catch (MException $e) {
					logException($e);
					$viewController = MObject::newInstanceOfClassWithParameters($this->errorViewControllerClass(), A(
						MHTTPResponse::RESPONSE_INTERNAL_SERVER_ERROR, N(MHTTPResponse::RESPONSE_INTERNAL_SERVER_ERROR), S("Internal Server Error"), S("Sorry but the page you are looking for could not be loaded due to an internal server error")
					));
				}
				
				if (!$viewController) {
					$viewController = MObject::newInstanceOfClassWithParameters($this->errorViewControllerClass(), A(
						MHTTPResponse::RESPONSE_NOT_FOUND, N(MHTTPResponse::RESPONSE_NOT_FOUND), S("Not Found"), S("Sorry but the page you are looking for could not be found")
					));
				}
				
				$response = new MHTTPViewControllerResponse($viewController);
			}
			
			MDie($response, $returnCode);
		}
	
	}

?>
