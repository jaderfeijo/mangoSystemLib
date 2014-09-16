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
	
	import('mango.system.*');
	
	/**
	 * Represents an Application Controller object which describes the interface of a given
	 * view controller.
	 *
	 * An application controller object is a wrapper object around a view controller. It
	 * provides information on how to interface with a given view controller, this
	 * includes information such as accepted paratemers, request methods, etc
	 *
	 * This class is usually created by the system when parsing the application's
	 * manifest file, however, it can also be created manually
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @package mango.system
	 *
	 */
	class MApplicationController extends MApplicationNode {
		
		/**
		 *
		 * @param $controllerElement
		 *
		 * @return MApplicationController
		 */
		public function parseFromXMLElement($controllerElement) {
			$controller = new MApplicationController(S($controllerElement['class']), S($controllerElement['name']));
			
			foreach ($controllerElement as $attributeElement) {
				if ($attributeElement->getName() == "controller") {
					$controller->addChildNode(MApplicationController::parseFromXMLElement($attributeElement));
				} else if ($attributeElement->getName() == "namespace") {
					$controller->addChildNode(MApplicationController::parseFromXMLElement($attributeElement));
				} else if ($attributeElement->getName() == "parameter") {
					$required = true;
					if (isset($attributeElement['required'])) {
						$required = MNumber::parseBool((string)$attributeElement['required'])->boolValue();
					}
					
					if ($attributeElement['type'] == "String") {
						$controller->addParameter(new MApplicationControllerParameter(S($parameterElement['name']), MApplicationControllerParameter::StringType, $required));
					} else if ($attributeElement['type'] == "Integer") {
						$controller->addParameter(new MApplicationControllerParameter(S($parameterElement['name']), MApplicationControllerParameter::IntegerType, $required));
					} else if ($attributeElement['type'] == "Float") {
						$controller->addParameter(new MApplicationControllerParameter(S($parameterElement['name']), MApplicationControllerParameter::FloatType, $required));
					} else if ($attributeElement['type'] == "Boolean") {
						$controller->addParameter(new MApplicationControllerParameter(S($parameterElement['name']), MApplicationControllerParameter::BooleanType, $required));
					} else if ($attributeElement['type'] == "Date") {
						$controller->addParameter(new MApplicationControllerParameter(S($parameterElement['name']), MApplicationControllerParameter::DateType, $required));
					} else if ($attributeElement['type'] == "Binary") {
						$controller->addParameter(new MApplicationControllerParameter(S($parameterElement['name']), MApplicationControllerParameter::Binary, $required));
					} else if ($attributeElement['type'] == "Array") {
						$controller->addParameter(new MApplicationControllerParameter(S($parameterElement['name']), MApplicationControllerParameter::ArrayType, $required));
					} else if ($attributeElement['type'] == "Dictionary") {
						$controller->addParameter(new MApplicationControllerParameter(S($parameterElement['name']), MApplicationControllerParameter::DictionaryType, $required));
					} else {
						throw new MParseErrorException(null, null, Sf("Unknown type '%s'", $parameterElement['type']));
					}
				} else if ($attributeElement->getName() == "accept") {
					$acceptedMethod = new MApplicationControllerAcceptedMethod(S($attributeElement['method']));
					foreach ($attributeElement as $acceptElement) {
						if ($acceptElement->getName() == "content-types") {
							foreach ($acceptElement as $contentTypeElement) {
								$acceptedMethod->addContentType(S($contentTypeElement));
							}
						} else if ($acceptElement->getName() == "fields") {
							foreach ($acceptElement as $fieldElement) {
								$required = true;
								if (isset($fieldElement['required'])) {
									$required = MNumber::parseBool((string)$fieldElement['required'])->boolValue();
								}
								
								if ($fieldElement['type'] == "String") {
									$acceptedMethod->addField(new MApplicationControllerField(S($fieldElement['name']), MApplicationControllerField::StringType, $required));
								} else if ($fieldElement['type'] == "Integer") {
									$acceptedMethod->addField(new MApplicationControllerField(S($fieldElement['name']), MApplicationControllerField::IntegerType, $required));
								} else if ($fieldElement['type'] == "Float") {
									$acceptedMethod->addField(new MApplicationControllerField(S($fieldElement['name']), MApplicationControllerField::FloatType, $required));
								} else if ($fieldElement['type'] == "Boolean") {
									$acceptedMethod->addField(new MApplicationControllerField(S($fieldElement['name']), MApplicationControllerField::BooleanType, $required));
								} else if ($fieldElement['type'] == "Date") {
									$acceptedMethod->addField(new MApplicationControllerField(S($fieldElement['name']), MApplicationControllerField::DateType, $required));
								} else if ($fieldElement['type'] == "Binary") {
									$acceptedMethod->addField(new MApplicationControllerField(S($fieldElement['name']), MApplicationControllerField::BinaryType, $required));
								} else if ($fieldElement['type'] == "Array") {
									$acceptedMethod->addField(new MApplicationControllerField(S($fieldElement['name']), MApplicationControllerField::ArrayType, $required));
								} else if ($fieldElement['type'] == "Dictionary") {
									$acceptedMethod->addField(new MApplicationControllerField(S($fieldElement['name']), MApplicationControllerField::DictionaryType, $required));
								} else {
									throw new MParseErrorException(null, null, Sf("Unknown type '%s'", $fieldElement['type']));
								}
							}
						} else {
							throw new MParseErrorException(null, null, Sf("Unknown element '%s'", $acceptElement->getName()));
						}
					}
					$controller->addAcceptedMethod($acceptedMethod);
				} else {
					throw new MParseErrorException(null, null, Sf("Unknown element '%s'", $attributeElement->getName()));
				}
			}
			
			if ($controller->acceptedMethods()->count() <= 0) {
				$controller->addAcceptedMethod(new MApplicationControllerAcceptedMethod(S("GET")));
			}
			
			return $controller;
		}
		
		//
		// ************************************************************
		//
		
		protected $controllerClassName;
		protected $parameters;
		protected $acceptedMethods;
		protected $parameterValues;
		
		/**
		 * Creates a new application controller instance with the specified view controller class name
		 * and the specified name
		 *
		 * @param MString $controllerClassName The fully qualified class name for the view controller
		 * this application controller represents
		 * @param MString $name The name for this application controller
		 *
		 * @return MApplicationController The newly created MApplicationController instance
		 */
		public function __construct(MString $controllerClassName, MString $name = null) {
			parent::__construct($name);
			
			$this->controllerClassName = $controllerClassName;
			$this->parameters = new MMutableArray();
			$this->acceptedMethods = new MMutableArray();
			$this->parameterValues = new MMutableArray();
		}
		
		/******************** Properties ********************/
		
		/**
		 * Retruns the class name of the view controller this application controller represents
		 *
		 * @return MString The class name for the view controller this application controller
		 * represents
		 */
		public function controllerClassName() {
			return $this->controllerClassName;
		}
		
		/**
		 * Returns an array containing a series of MApplicationControllerParameter objects
		 * which are accepted by this application controller
		 *
		 * @return MArray An array containing all accepted parameter objects for this
		 * controller
		 */
		public function parameters() {
			return $this->parameters;
		}
		
		/**
		 * Returns an array containing all the MApplicationControllerParameter objects
		 * which are required by this application controller
		 *
		 * @return MArray An array containing all the required parameters for this
		 * controller
		 */
		public function requiredParameters() {
			$requiredParameters = new MMutableArray();
			foreach ($this->parameters()->toArray() as $parameter) {
				if ($parameter->required()) {
					$requiredParameters->addObject($parameter);
				}
			}
			return $requiredParameters;
		}
		
		/**
		 * Returns an array containg a series of MApplicationControllerAcceptedMethod objects
		 * which this application controller accepts
		 *
		 * @return MArray An array containing all accepted methods for this controller
		 */
		public function acceptedMethods() {
			return $this->acceptedMethods;
		}
		
		/**
		 * Returns an array of the consumed parameter values for this view controller.
		 * The values are passed in via the consumeParameterValues() method.
		 *
		 * @return MArray An array contining all the parameter values which
		 * were consumed by this controller
		 */
		public function parameterValues() {
			return $this->parameterValues;
		}
		
		/******************** Methods ********************/
		
		/**
		 * Adds the specified MApplicationControllerParameter instance to the list
		 * of accepted parameters for this controller
		 *
		 * @param MApplicationControllerParameter $parameter The application controller
		 * parameter instance to add
		 *
		 * @return void
		 */
		public function addParameter(MApplicationControllerParameter $parameter) {
			$this->parameters->addObject($parameter);
		}
		
		/**
		 * Removes the specified MApplicationControllerParameter instance from the
		 * list of accepted parameters for this controller
		 *
		 * @param MApplicationControllerParameter $parameter The application controller parameter
		 * instance to remove
		 *
		 * @return void
		 */
		public function removeParameter(MApplicationControllerParameter $parameter) {
			$this->parameters->removeObject($parameter);
		}
		
		/**
		 * Removes all accepted parameters from this controller
		 *
		 * @return void
		 */
		public function removeAllParameters() {
			$this->parameters->removeAllObjects();
		}
		
		/**
		 * Returns a MApplicationControllerParamter instance matching the specified
		 * name. If no matching parameter is found, this method returns null
		 *
		 * @param MString $name The name of the application parameter you wish to
		 * retrieve
		 *
		 * @return MApplicationControllerParameter The parameter instance matching
		 * the specified name, or null if no matching parameter is found
		 */
		public function parameterWithName(MString $name) {
			foreach ($this->parameters()->toArray() as $parameter) {
				if ($parameter->name()->equals($name)) {
					return $parameter;
				}
			}
			return null;
		}
		
		/**
		 * Adds an accepted method instance to the list of methods accepted by this
		 * application controller
		 *
		 * @param MApplicationControllerAcceptedMethod The accepted method instance
		 * you wish to add
		 *
		 * @return void
		 */
		public function addAcceptedMethod(MApplicationControllerAcceptedMethod $method) {
			$this->acceptedMethods->addObject($method);
		}
		
		/**
		 * Removes the specified accepted methods instance from the lsit of methods
		 * accepted by this application controller
		 *
		 * MApplicationControllerAcceptedMethod $method The accepted method instance
		 * you wish to remove
		 *
		 * @return void
		 */
		public function removeAcceptedMethod(MApplicationControllerAcceptedMethod $method) {
			$this->acceptedMethods->removeObject($method);
		}
		
		/**
		 * Removes all accepted methods from this application controller
		 *
		 * @return void
		 */
		public function removeAllAcceptedMethods() {
			$this->acceptedMethods->removeAllObjects();
		}
		
		/**
		 * Returns a boolean which indicates wether this application controller accepts
		 * the specified method
		 *
		 * This method iterates through all of the MApplicationControllerAcceptedMethod
		 * instances accepted by this controller and looks for one that matches the
		 * specified method string. If an instance matching the specified method is found,
		 * this method returns true otherwise it returns false to indicate that this
		 * controller does not accept the specified method
		 *
		 * @param MString A string containing the method name
		 *
		 * @return bool A boolean value indicing whether or not this controller
		 * accepts the specified method
		 */
		public function acceptsMethod(MString $method) {
			foreach ($this->acceptedMethods()->toArray() as $acceptedMethod) {
				if ($acceptedMethod->method()->equals($method)) {
					return true;
				}
			}
			return false;
		}
		
		/**
		 * Returns the MApplicationControllerAcceptedMethod instance that matches
		 * the specified method
		 *
		 * This method iterates through all of the MApplicationControllerAcceptedMethod
		 * instances accepted by this controller and looks for one that matches the
		 * specified method string. If an instance matching the specified method is found,
		 * it is returned, otherwise this method returns null
		 *
		 * @param MString A string containing the method name
		 *
		 * @return MApplicationControllerAcceptedMethod The MApplicationControllerAcceptedMethod
		 * instance matching the specified method string, or null if none is found
		 */
		public function acceptedMethodForMethod(MString $method) {
			foreach ($this->acceptedMethods()->toArray() as $acceptedMethod) {
				if ($acceptedMethod->method()->equals($method)) {
					return $acceptedMethod;
				}
			}
			return null;
		}
		
		/**
		 * This method returns a new instance of the MViewController subclass this MApplicationController
		 * represents, initialised with the specified parameters.
		 *
		 * @param MArray $parameters An array containing the parameters with which to initialise the
		 * new MViewController instance
		 *
		 * @return MViewController returns an instance of the MViewController this Application
		 * controller represents with the specified parameters
		 */
		public function newViewControllerInstanceWithParameters(MArray $parameters) {
			$reflectionClass = MObject::reflectionClass($this->controllerClassName());
			$reflectionConstructor = $reflectionClass->getMethod("__construct");
			
			// validate number of parameters
			if ($this->parameters()->count() != $reflectionConstructor->getNumberOfParameters()) {
				throw new MException(Sf("The number of parameters in the manifest doesn't match the number of parameters declared in the constructor for the '%s' class", $this->controllerClassName()));
			}
			if ($this->requiredParameters()->count() != $reflectionConstructor->getNumberOfRequiredParameters()) {
				throw new MException(Sf("The number of required parameters in the manifest doesn't match the number of required parameters declared in the constructor for the '%s' class", $this->controllerClassName()));
			}
			if ($parameters->count() < $reflectionConstructor->getNumberOfRequiredParameters()) {
				throw new MBadRequestException();
			}
			
			
			if ($parameters->count() > 0) {
				$formattedParameters = new MMutableArray();
				for ($i = 0; $i < $parameters->count(); $i++) {
					$controllerParameter = $this->parameters()->objectAtIndex($i);
					$parameterValue = $parameters->objectAtIndex($i);
					$parameterObject = null;
					if ($controllerParameter->type() == MApplicationControllerParameter::StringType) {
						$parameterObject = $parameterValue;
					} else if ($controllerParameter->type() == MApplicationControllerParameter::IntegerType) {
						$parameterObject = MNumber::parseInt($parameterValue);
					} else if ($controllerParameter->type() == MApplicationControllerParameter::FloatType) {
						$parameterObject = MNumber::parseFloat($parameterValue);
					} else if ($controllerParameter->type() == MApplicationControllerParameter::BooleanType) {
						$parameterObject = MNumber::parseBool($parameterValue);
					} else if ($controllerParameter->type() == MApplicationControllerParameter::DateType) {
						$parameterObject = MDate::parseString($parameterValue);
					} else if ($controllerParameter->type() == MApplicationControllerParameter::BinaryType) {
						$parameterObject = MData::parseBase64String($parameterValue);
					} else if ($controllerParameter->type() == MApplicationControllerParameter::ArrayType) {
						$parameterObject = $parameterValue->componentsSeparatedByString(S("|"));
					} else if ($controllerParameter->type() == MApplicationControllerParameter::DictionaryType) {
						$parameterObject = MDictionary::parseString($parameterValue);
					}
					
					if ($parameterObject) {
						$formattedParameters->addObject($parameterObject);
					} else {
						throw new MException(Sf("Unknown error while parsing parameter named '%s'", $controllerParameter->name()));
					}
				}
				return $reflectionClass->newInstanceArgs($formattedParameters->toArray());
			} else {
				return $reflectionClass->newInstance();
			}
		}
		
		/******************** MApplicationNode ********************/
		
		/**
		 *
		 *
		 * @return MViewController
		 */
		public function viewControllerForPath(MArray $path) {
			$viewController = null;
			$childViewController = null;
		
			if ($this->hasChildNodes()) {
				if ($path->count() > 0) {
					$numberOfRequiredParameters = $this->requiredParameters()->count();
					if ($path->count() >= $numberOfRequiredParameters) {
						if ($path->count() > $numberOfRequiredParameters) {
							$nodePath = $path->subarrayFromIndex($numberOfRequiredParameters);
							$node = $this->childNodeWithName($nodePath->objectAtIndex(0));
							if ($node) {
								$subpath = new MArray();
								if ($nodePath->count() > 1) {
									$subpath = $nodePath->subarrayFromIndex(1);
								}
								$childViewController = $node->viewControllerForPath($subpath);
							}
						}
					
						if ($childViewController) {
							$requiredParameters = $path->subarrayToIndex($numberOfRequiredParameters - 1);
							$viewController = $this->newViewControllerInstanceWithParameters($requiredParameters);
						} else {
							$viewController = $this->newViewControllerInstanceWithParameters($path);
						}
					} else {
						throw new MBadRequestException();
					}
				} else {
					$viewController = $this->newViewControllerInstanceWithParameters($path);
				}
			} else {
				$viewController = $this->newViewControllerInstanceWithParameters($path);
			}
			
			if ($viewController) {
				$viewController->setApplicationController($this);
				if ($childViewController) {
					$viewController->addChildViewController($childViewController);
				}
				return $viewController;
			} else {
				return parent::viewControllerForPath($path);
			}
		}
		
	}

?>