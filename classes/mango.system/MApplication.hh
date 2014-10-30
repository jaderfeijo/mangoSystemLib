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
 * This class holds a representation of the currently running Mango
 * Application. It controls the execution and allows you to configure
 * several different aspects of your Mango Application
 *
 * You should only ever create one instance of this class, this is usually done
 * in the `index.hh` file. To access the MApplication singleton instance that
 * represents the currently running Application use
 * MApplication::sharedApplication()
 *
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
class MApplication extends MObject {

	const string DefaultErrorViewControllerClass = "MErrorViewController";

	protected static ?MApplication $_sharedApplication = null;
	
	/**
	 * Returns the MApplication instance that represents the currently
	 * running application
	 *
	 * @return MApplication The currently running Application instance
	 */
	public static function sharedApplication() : MApplication {
		if (MApplication::$_sharedApplication === null) {
			throw new MException(S("No Application instance was created!"));
		}
		return MApplication::$_sharedApplication;
	}
	
	//
	// ************************************************************
	//

	protected MApplicationDelegate $_delegate;
	protected MApplicationNamespace $_defaultNamespace;
	protected MArray<MString> $_arguments;

	protected MString $_errorViewControllerClass;
	protected ?MViewController $_rootViewController;
	protected ?MFile $_simulatedRequestFile;

	/**
	 * Creates a new MApplication instance with the specified delegate class
	 * If no delegate class is specified the system looks for the 'manifest.xml'
	 * file inside the 'resources' folder and parses it
	 *
	 * @todo Describe parameters
	 *
	 * @return MApplication The MApplication instance which has just been created
	 */
	public function __construct(MApplicationDelegate $delegate, MApplicationNamespace $defaultNamespace, MArray<MString> $arguments) {
		parent::__construct();
		
		$this->_delegate = $delegate; 
		$this->_defaultNamespace = $defaultNamespace;
		$this->_arguments = $arguments;
		
		$this->_errorViewControllerClass = S(MApplication::DefaultErrorViewControllerClass);
		$this->_rootViewController = null;
		$this->_simulatedRequestFile = null;
		
		if (MApplication::$_sharedApplication === null) {
			MApplication::$_sharedApplication = $this;
		} else {
			throw new MApplicationInstanceAlreadyCreatedException();
		}
		
		if (!$this->isRoutingEnabled()) {
			$this->enableRouting();
			if (!$this->isRunningFromCommandLine()) {
				$redirect = new MHTTPResponse(MHTTPResponseCode::Found);
				$redirect->addHeader(S("Location"), MHTTPRequest()->url());
				MDie($redirect);
			}
		}
	}

	/******************** Protected ********************/
	
	/**
	 * @internal
	 *
	 * @return bool
	 */
	protected function isRoutingEnabled() : bool {
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
	protected function enableRouting() : void {
		MLog("[EnableRouting]: Creating '.htaccess' file for URL routing…");
		
		$fileStream = new MFileOutputStream(new MFile(S(".htaccess")));
		$writer = new MStreamWriter($fileStream);
		$writer->writeLine(S("# Mango URL Routing Code"));
		$writer->writeLine(S("RewriteEngine On"));
		$writer->writeLine(S("RewriteRule . index.hh));
		$writer->close();
		
		MLog("[EnableRouting]: File created");
	}
	
	/******************** Properties ********************/
	
	/**
	 * Returns the Application Delegate instance used by this application
	 *
	 * @return MApplicationDelegate The Application Delegate instance for this
	 * application
	 */
	public function delegate() : MApplicationDelegate {
		return $this->_delegate;
	}
	
	/**
	 * Returns the default namespace for this application.
	 *
	 * @return MApplicationNamespace The default namespace used by this application
	 */
	public function defaultNamespace() : MApplicationNamespace {
		if (!$this->_defaultNamespace) {
			$this->_defaultNamespace = new MApplicationNamespace(S(""));
		}
		return $this->_defaultNamespace;
	}

	/**
	 * Returns an array containing all the arguments that were passed to this mango
	 * application when it was invoked from the command line.
	 *
	 * @return MArray<MString> An array containing the arguments that were passed in from the
	 * command line.
	 */
	public function arguments() : MArray<MString> {
		return $this->_arguments;
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
	 * @param MString $errorViewControllerClass The class name of the view controller
	 * to use for error handling
	 *
	 * @return void
	 */
	public function setErrorViewControllerClass(MString $errorViewControllerClass) : void {
		$this->_errorViewControllerClass = $errorViewControllerClass;
	}
	
	/**
	 * Returns the MErrorViewController subclass this application will use for handling
	 * errors
	 *
	 * @return MString A string containing the fully qualified error view controller
	 * class name
	 */
	public function errorViewControllerClass() : MString {
		return $this->_errorViewControllerClass;
	}
	
	/**
	 * Returns the root view controller for this current instance of the application.
	 * The root view controller is returned depending on which parameters are called.
	 * The Application class parses the request URL and breaks down the different elements
	 * into controller url, and parameters. This determines which view controller should
	 * be instanced and called. The appropriate view controller is instanced according to
	 * those parameters and returned by this method.
	 *
	 * @return ?MViewController The root view controller for this instance of the application
	 */
	public function rootViewController() : ?MViewController {
		if ($this->_rootViewController === null) {
			$this->_rootViewController = $this->defaultNamespace()->viewControllerForPath(MHTTPRequest()->arguments());
		}
		return $this->_rootViewController;
	}

	/**
	 * Returns a boolean which indicates whether or not this application was called from
	 * the command line, or whether it is being run as a result of a request to the
	 * server.
	 *
	 * @return bool Returns true if the application is being run from the command line.
	 * false otherwise.
	 */
	public function isRunningFromCommandLine() : bool {
		if (PHP_SAPI == 'cli') {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Returns whether or not this application is running in simulated request
	 * mode. Simulated request mode allows the application to be called from
	 * within the command line with a set of parameters which make it behave
	 * as if it had received a normal HTTP request. This allows the application
	 * to be debugged from the CLI without the need of having a HTTP server
	 * in between.
	 *
	 * Simulated request mode can be triggered using by passing the following
	 * command when invoking the application's `index.hh` file.
	 *
	 * $ hhvm index.hh --simulate-request request.json
	 *
	 * This will look for a file named request.json, parse it's contents
	 * and use it as a HTTP request which is passed to the application
	 * and executed.
	 *
	 * You can also invoke the application with a given request in debug mode
	 * as follows:
	 *
	 * $ hhvm -m debug index.hh --simulate-request request.json
	 *
	 * This will do the same as before, but will fire the HHVM debugger, allowing
	 * you to debug the request, all done locally without the need for going through a
	 * web server like Apache or Nginx.
	 *
	 * @return bool Whether or not this application is running in simulated request
	 * mode.
	 */
	public function isRunningInSimulatedRequestMode() : bool {
		return ($this->simulatedRequestFile() !== null);
	}

	/**
	 * Returns the path for the file containing the simulated request data
	 * when running in simulated request mode, or null otherwise.
	 *
	 * @return ?MFile Returns the simulated request file if provided,
	 * or null otherwise.
	 */
	public function simulatedRequestFile() : ?MFile {
		if ($this->_simulatedRequestFile === null) {
			if ($this->isRunningFromCommandLine()) {
				$args = getopt('', 'simulate-request::');
				$file = $args['simulate-request'];
				if ($file !== null) {
					$this->_simulatedRequestFile = new MFile(S($file));
				}
			}
		}
		return $this->_simulatedRequestFile;
	}

	/******************** Methods ********************/
	
	/**
	 * @todo update
	 *
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
	 * @return int
	 */
	public function run() : int {
		$returnCode = 0;
		
		if ($this->isRunningFromCommandLine() && !$this->isRunningInSimulatedRequestMode()) {
			$returnCode = $this->delegate()->didFinishLaunchingFromCommandLineWithArguments($this->arguments());
		} else {
			$viewController = null;
			
			try {
				$this->delegate()->didFinishLaunching();
				$viewController = $this->rootViewController();
			} catch (MBadRequestException $e) {
				logException($e);
				$viewController = MObject::newInstanceOfClassWithParameters($this->errorViewControllerClass(), A(
					MHTTPResponseCode::BadRequest, I((int)MHTTPResponseCode::BadRequest), S("Bad Request"), $e->description()
				));
			} catch (MException $e) {
				logException($e);
				$viewController = MObject::newInstanceOfClassWithParameters($this->errorViewControllerClass(), A(
					MHTTPResponseCode::InternalServerError, I((int)MHTTPResponseCode::InternalServerError), S("Internal Server Error"), S("Sorry but the page you are looking for could not be loaded due to an internal server error")
				));
			}
			
			if ($viewController === null) {
				$viewController = MObject::newInstanceOfClassWithParameters($this->errorViewControllerClass(), A(
					MHTTPResponseCode::NotFound, I((int)MHTTPResponseCode::NotFound), S("Not Found"), S("Sorry but the page you are looking for could not be found")
				));
			}
			
			$response = new MHTTPViewControllerResponse($viewController);
			MSendResponse($response);
		}

		return $returnCode;
	}

}

