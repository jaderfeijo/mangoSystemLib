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
	 * This file contains a set of functions which provide the packaging system
	 * functionality for Mango. This is the most basic portion of the Mango
	 * framework upon which every other bit of code depends and relies on.
	 *
	 * Including this file into your top-level Mango Framework script is required
	 * in order to use Mango Framework.
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 *
	 */
	
	require_once('errors.php');
	
	/**
	 * Defines which package the class or file belongs to
	 *
	 * This function simply makes sure that all other classes inside
	 * the specified package are imported
	 *
	 * @return void
	 */
	function package($package) {
		import("$package.*");
	}
	
	/**
	 * Loads a library into the project
	 *
	 * This function tells the system where to find library classes
	 * of a given version.
	 *
	 * If you try to specify a library with a given name with a
	 * different version more than once it will issue a warning.
	 *
	 * If no version is specified, the default 'latest' is used
	 *
	 * If the library and version specified cannot be found this
	 * function issues a warning.
	 *
	 * @example library('myCoolLibrary', '1.0');
	 * @example library('com.imagelib.ImageLib', 'latest');
	 * @example library('com.imagelib.ImageLib');
	 *
	 * @param string $name The name of the library to be imported
	 * @param string $version The version of the library to be
	 * imported
	 *
	 * @return boolean A boolean indicating whether or not the library
	 * was loaded successfully
	 */
	function library($name, $version = 'latest') {
		global $__cc_paths;
		
		$libraryPath = "library/$name";
		$fullLibraryPath = "$libraryPath/$version";
		
		foreach ($path in $__cc_paths) {
			if (strpos($path, $libraryPath) !== false) {
				if ($path != $fullLibraryPath) {
					issueWarning(sprintf(M_WRN_MSG_LIBRARY_ALREADY_DEFINED, $name));
					return false;
				} else {
					return true;
				}
			}
		}
		
		if (file_exists("$fullLibraryPath/library.php")) {
			$__cc_paths[] = $fullLibraryPath;
			require_once("$fullLibraryPath/library.php");
			return true;
		} else {
			issueWarning(sprintf(M_WRN_MSG_LIBRARY_NOT_FOUND, $name, $version));
			return false;
		}
	}
	
	/**
	 * Imports classes and whole packages
	 *
	 * This function schedules classes for importing inside of Mango's
	 * class loading queue. Once the classes are actually needed, the
	 * classes are loaded using PHP's dynamic class loading mechanism.
	 *
	 * @example import('mango.system.io.MFile');
	 * Imports the MFile class from inside the 'mango.system.io' package
	 * @example import('mango.system.io.*');
	 * Imports all classes inside the 'mango.system.io' package
	 *
	 * @param string $import The fully qualified class name or wildcard
	 * package name to be imported
	 *
	 * @return boolean A boolean indicating whether or not the class or
	 * package was imported
	 */
	function import($import) {
		global $__cc_imports;
		global $__cc_packages;
		global $__cc_paths;
		
		$class = MClassFromPackageString($import);
		$package = MPackageFromPackageString($import);
		
		if (isset($__cc_imports[$class]) || isset($__cc_packages[$package.'.*'])) return true;
		
		$packageFound = false;
		$classFound = false;
		
		foreach ($__cc_paths as $path) {
			$classPath = "$path/classes/$package";
			
			if (file_exists($classPath)) {
				$packageFound = true;
				
				if ($class == '*') {
					$classFound = true;
				
					$__cc_packages["$package.*"] = true;
					
					$dir = opendir($classPath);
					while (($file = readdir($dir)) !== false) {
						if (strrpos($file, '.php')) {
							$className = str_replace('.php', '', $file);
							if (!isset($__cc_imports[$className])) {
								$__cc_imports[$className] = "$classPath/$file";
							}
						}
					}
					
					break;
				} else {
					$classPath .= "/$class.php";
					if (file_exists($classPath)) {
						$classFound = true;
						
						if (!isset($__cc_imports[$class])) {
							$__cc_imports[$class] = $classPath;
						}
						
						break;
					}
				}
			}
		}
		
		if (!$packageFound) {
			issueWarning(sprintf(M_WRN_MSG_NO_SUCH_PACKAGE, $package));
			return false;
		} else if (!$classFound) {
			issueWarning(sprintf(M_WRN_MSG_NO_SUCH_CLASS, $class, $package));
			return false;
		} else {
			return true;
		}
	}
	
	/******************** Helper Functions ********************/
	
	/**
	 * Returns the class name portion of a fully qualified class name
	 *
	 * This function reads a fully qualified class name (package string)
	 * and returns only the Class name portion of it
	 *
	 * @see MPackageFromPackageString() to get the package name portion of
	 * a package string
	 *
	 * @param string $packageString The fully qualified class name
	 *
	 * @return string Returns the class name
	 */
	function MClassFromPackageString($packageString) {
		$lastDot = strrpos($packageString, '.');
		return ($lastDot ? substr($packageString, $lastDot + 1) : $packageString);
	}
	
	/**
	 * Returns the package portion of a fully qualified class name
	 *
	 * This function reads a fully qualified class name (package string)
	 * and returns only the Package name portion of it
	 *
	 * @see MClassFromPackageString() to get the class name portion of
	 * a package string
	 *
	 * @param string $packageString The fully qualified class name
	 *
	 * @return string Returns the package name
	 */
	function MPackageFromPackageString($packageString) {
		return substr($packageString, 0, strrpos($packageString, '.'));
	}
	
	/******************** Internal Stuff ********************/
	
	/**
	 * @internal
	 */
	define('M_WRN_MSG_NO_SUCH_PACKAGE', "There is no such package '%s'");

	/**
	 * @internal
	 */
	define('M_WRN_MSG_NO_SUCH_CLASS', "There is no such class '%s' in package '%s'");

	/**
	 * @internal
	 */
	define('M_WRN_MSG_LIBRARY_ALREADY_DEFINED', "A different version of the library named '%s' is already defined");
	
	/**
	 * @internal
	 */
	define('M_WRN_MSG_LIBRARY_NOT_FOUND', "The library named '%s' version '%s' could not be found");

	$__cc_imports = array();
	$__cc_packages = array();
	$__cc_paths = array();
	
	/**
	 * @internal
	 * 
	 * Mango Framework class auto loader
	 *
	 * This function gets called by the system every time a class
	 * that has not yet been loaded (included) is used. This allows
	 * Mango to dynamically load (include) only the classes that
	 * are actually used in every run of your code.
	 *
	 * @param string $className The name of the class that needs
	 * to be loaded
	 *
	 * @return void
	 */
	function __autoload($className) {
		global $__cc_imports;
		if (isset($__cc_imports[$className])) {
			include_once($__cc_imports[$className]);
		}
	}
	
?>