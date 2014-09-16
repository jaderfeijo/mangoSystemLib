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
	
	import('mango.system.html.*');
	
	/**
	 * 
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 *
	 * @package mango.system
	 *
	 */
	class MViewController extends MObject {
		
		//
		// ************************************************************
		//
		
		protected $view;
		protected $applicationController;
		protected $responseCode;
		protected $responseHeaders;
		protected $restricted;
		protected $realm;
		protected $accessDeniedView;
		protected $invalidCredentialsView;
		protected $authenticatedUserName;
		protected $parameterValues;
		protected $fieldValues;
		protected $parentViewController;
		protected $childViewControllers;
		
		/**
		 * 
		 *
		 * @return MViewController
		 */
		public function __construct(MView $view = null) {
			parent::__construct();
			
			$this->view = $view;
			$this->applicationController = null;
			$this->responseCode = MHTTPResponse::RESPONSE_OK;
			$this->responseHeaders = new MMutableDictionary();
			$this->restricted = false;
			$this->realm = S("Restricted Area");
			$this->accessDeniedView = null;
			$this->invalidCredentialsView = null;
			$this->authenticatedUserName = null;
			$this->parameterValues = null;
			$this->fieldValues = null;
			$this->parentViewController = null;
			$this->childViewControllers = new MMutableArray();
		}
		
		/******************** Protected ********************/
		
		/**
		 * @internal
		 *
		 * @return MDictionary
		 */
		protected function _parseDigest($digest) {
			$data = new MMutableDictionary();
			$parts = explode(',', $digest);
			
			foreach($parts as $p) {
				$p = trim($p);
				$kvpair = explode('=', $p);
				$kvpair[1] = str_replace("\"", "", $kvpair[1]);
				$data->setObjectForKey(S($kvpair[0]), S($kvpair[1]));
			}
			
			return $data;
		}
		
		/**
		 * @internal
		 *
		 * @return 
		 */
		protected function _parseParameterValues() {
			if ($this->applicationController()) {
				foreach ($this->applicationController()->acceptedMethodForMethod(S(MHTTPRequest()->method()))->parameters()->toArray() as $parameter) {
					$value = MHTTPRequest()->inputParameters()->objectForKey($parameter->name());
					if ($value) {
						if (!$this->_setValueForParameter($parameter, $value)) {
							throw new MBadRequestException(Sf("Invalid data type for parameter [%s], expected [%s]", $parameter->name(), $parameter->expectedDataType()));
						}
					} else {
						if ($parameter->required()) {
							throw new MBadRequestException(Sf("Required parameter [%s] missing", $parameter->name()));
						}
					}
				}
			}
		}
		
		/**
		 * @internal
		 *
		 * @return void
		 */
		protected function _parseFieldValues() {
			if ($this->applicationController()) {
				foreach ($this->applicationController()->acceptedMethodForMethod(S(MHTTPRequest()->method()))->fields()->toArray() as $field) {
					$value = MHTTPRequest()->inputFields()->objectForKey($field->name());
					if ($value) {
						if (!$this->_setValueForField($field, $value)) {
							throw new MBadRequestException(Sf("Invalid data type for field [%s], expected [%s]", $field->name(), $field->expectedDataType()));
						}
					} else {
						if ($field->required()) {
							throw new MBadRequestException(Sf("Required field [%s] missing", $field->name()));
						}
					}
				}
			}
		}
		
		/**
		 * @internal
		 *
		 * @return bool
		 */
		public function _setValueForParameter(MApplicationControllerParameter $parameter, $value) {
			$object = null;
			
			try {
				if ($parameter->type() == MApplicationControllerParameter::StringType) {
					$object = S($value);
				} else if ($parameter->type() == MApplicationControllerParameter::IntegerType) {
					$object = MNumber::parseInt($value);
				} else if ($parameter->type() == MApplicationControllerParameter::FloatType) {
					$object = MNumber::parseFloat($value);
				} else if ($parameter->type() == MApplicationControllerParameter::BooleanType) {
					$object = MNumber::parseBool($value);
				} else if ($parameter->type() == MApplicationControllerParameter::DateType) {
					$object = MDate::parse($value);
				} else if ($parameter->type() == MApplicationControllerParameter::BinaryType) {
					$object = MData::parseBase64String(S($value));
				} else if ($parameter->type() == MApplicationControllerParameter::ArrayType) {
					$object = new MMutableArray();
					foreach ($value as $v) {
						$object->addObject(S($v));
					}
				} else if ($parameter->type() == MApplicationControllerParameter::DictionaryType) {
					$object = new MMutableDictionary();
					foreach ($value as $k => $v) {
						$object->setObjectForKey(S($k), S($v));
					}
				}
			} catch (Exception $e) {
				return false;
			}
			
			if (!$this->parameterValues) {
				$this->parameterValues = new MMutableDictionary();
			}
			
			if ($object) {
				$this->parameterValues->setObjectForKey($parameter, $object);
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 * @internal
		 *
		 * @return bool
		 */
		protected function _setValueForField(MApplicationControllerField $field, $value) {
			$object = null;
			
			try {
				if ($field->type() == MApplicationControllerField::StringType) {
					$object = S($value);
				} else if ($field->type() == MApplicationControllerField::IntegerType) {
					$object = MNumber::parseInt($value);
				} else if ($field->type() == MApplicationControllerField::FloatType) {
					$object = MNumber::parseFloat($value);
				} else if ($field->type() == MApplicationControllerField::BooleanType) {
					$object = MNumber::parseBool($value);
				} else if ($field->type() == MApplicationControllerField::DateType) {
					$object = MDate::parse($value);
				} else if ($field->type() == MApplicationControllerField::BinaryType) {
					$object = MData::parseBase64String(S($value));
				} else if ($field->type() == MApplicationControllerField::ArrayType) {
					$object = new MMutableArray();
					foreach ($value as $v) {
						$object->addObject(S($v));
					}
				} else if ($field->type() == MApplicationControllerField::DictionaryType) {
					$object = new MMutableDictionary();
					foreach ($value as $k => $v) {
						$object->setObjectForKey(S($k), S($v));
					}
				}
			} catch (Exception $e) {
				return false;
			}
			
			if (!$this->fieldValues) {
				$this->fieldValues = new MMutableDictionary();
			}
			
			if ($object) {
				$this->fieldValues->setObjectForKey($field, $object);
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 * @return MView
		 */
		protected function _view() {
			if (!$this->view) {
				$this->loadView();
			}
			return $this->view;
		}
		
		/******************** Methods ********************/
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function passwordForUserWithName(MString $username) {
			return null;
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function loadView() {
			$this->setView(new MView());
		}
		
		/**
		 * @return MObject
		 */
		public function valueForParameter(MApplicationControllerParameter $parameter) {
			if (!$this->parameterValues) {
				$this->_parseParameterValues();
			}
			if ($this->parameterValues) {
				return $this->parameterValues->objectForKey($parameter);
			} else {
				return null;
			}
		}
		
		/**
		 * @return MObject
		 */
		public function valueForField(MApplicationControllerField $field) {
			if (!$this->fieldValues) {
				$this->_parseFieldValues();
			}
			if ($this->fieldValues) {
				return $this->fieldValues->objectForKey($field);
			} else {
				return null;
			}
		}
		
		/**
		 * @return MObject
		 */
		public function valueForParameterNamed(MString $parameterName) {
			if ($this->applicationController()) {
				$parameter = $this->applicationController()->acceptedMethodForMethod(S(MHTTPRequest()->method()))->parameterWithName($parameterName);
				if (!$parameter) {
					throw new MParameterUndefinedException($parameterName);
				}
				return $this->valueForParameter($parameter);
			} else {
				return null;
			}
		}
		
		/**
		 * @return MObject
		 */
		public function valueForFieldNamed(MString $fieldName) {
			if ($this->applicationController()) {
				$field = $this->applicationController()->acceptedMethodForMethod(S(MHTTPRequest()->method()))->fieldWithName($fieldName);
				if (!$field) {
					throw new MFieldUndefinedException($fieldName);
				}
				return $this->valueForField($field);
			} else {
				return null;
			}
		}
		
		/******************** Properties ********************/
		
		/**
		 * @return MViewController
		 */
		public function parentViewController() {
			return $this->parentViewController;
		}
		
		/**
		 * @return void
		 */
		public function setParentViewController(MViewController $viewController = null) {
			$this->parentViewController = $viewController;
		}
		
		/**
		 * @return MArray
		 */
		public function childViewControllers() {
			return $this->childViewControllers;
		}
		
		/**
		 * @return void
		 */
		public function addChildViewController(MViewController $viewController) {
			$this->childViewControllers->addObject($viewController);
			$viewController->setParentViewController($this);
		}
		
		/**
		 * @return void
		 */
		public function removeChildViewController(MViewController $viewController) {
			$this->childViewControllers->removeObject($viewController);
			$viewController->setParentViewController(null);
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setView(MView $view) {
			$this->view = $view;
			$this->addResponseHeader(S("Content-Type"), $view->contentType());
		}
		
		/**
		 * 
		 *
		 * @return MView
		 */
		public function view() {
			if ($this->applicationController()) {
				if ($this->applicationController()->acceptsMethod(S(MHTTPRequest()->method()))) {
					if (MHTTPRequest()->contentType()) {
						$contentTypes = MHTTPRequest()->contentType()->componentsSeparatedByString(S(";"));
						foreach ($contentTypes->toArray() as $contentType) {
							$type = $contentType->stringByTrimmingEmptySpaces();
							if ($this->applicationController()->acceptedMethodForMethod(S(MHTTPRequest()->method()))->contentTypes()->count() > 0) {
								if (!$this->applicationController()->acceptedMethodForMethod(S(MHTTPRequest()->method()))->contentTypes()->containsObject($type)) {
									throw new MBadRequestException(Sf("The specified content-type (%s) is not supported", $type));
								}
							}
						}
					}
					
					if ($this->isRestricted()) {
						if (!$this->authenticatedUserName) {
							$authenticateHeader = Sf('Digest realm="%s", qop="auth", nonce="%s", opaque="%s"', $this->realm(), uniqid(), md5($this->realm()));
							
							if (empty(MHTTPRequest::request()->server()['PHP_AUTH_DIGEST'])) {
								$this->setResponseCode(MHTTPResponse::RESPONSE_UNAUTHORIZED);
								$this->addResponseHeader(S("WWW-Authenticate"), $authenticateHeader);
								return $this->accessDeniedView();
							} else {
								$data = $this->_parseDigest(MHTTPRequest::request()->server()['PHP_AUTH_DIGEST']);
								
								$username = $data->objectForKey(S("username"));
								if ($username) {
									$password = $this->passwordForUserWithName($username);
									if ($password) {
										$A1 = S(md5(Sf(
											"%s:%s:%s",
											$data->objectForKey(S("username")),
											$this->realm(),
											$password
										)));
										$A2 = S(md5(Sf(
											"%s:%s",
											MHTTPRequest::request()->server()['REQUEST_METHOD'],
											$data->objectForKey(S("uri"))
										)));
										$validResponse = S(md5(Sf(
											"%s:%s:%s:%s:%s:%s",
											$A1,
											$data->objectForKey("nonce"),
											$data->objectForKey("nc"),
											$data->objectForKey("cnonce"),
											$data->objectForKey("qop"),
											$A2
										)));
										
										if ($data->objectForKey("response")->equals($validResponse)) {
											$this->authenticatedUserName = $username;
											return $this->_view();
										} else {
											$this->setResponseCode(MHTTPResponse::RESPONSE_UNAUTHORIZED);
											$this->addResponseHeader(S("WWW-Authenticate"), $authenticateHeader);
											return $this->invalidCredentialsView();
										}
									} else {
										$this->setResponseCode(MHTTPResponse::RESPONSE_UNAUTHORIZED);
										$this->addResponseHeader(S("WWW-Authenticate"), $authenticateHeader);
										return $this->invalidCredentialsView();
									}
								} else {
									$this->setResponseCode(MHTTPResponse::RESPONSE_UNAUTHORIZED);
									$this->addResponseHeader(S("WWW-Authenticate"), $authenticateHeader);
									return $this->invalidCredentialsView();
								}
							}
						} else {
							return $this->_view();
						}
					} else {
						return $this->_view();
					}
				} else {
					throw new MBadRequestException(Sf("The specified request method (%s) is not supported", MHTTPRequest()->method()));
				}
			} else {
				return $this->_view();
			}
		}
		
		/**
		 * @return void
		 */
		public function setApplicationController(MApplicationController $applicationController) {
			$this->applicationController = $applicationController;
		}
		
		/**
		 * @return MApplicationController
		 */
		public function applicationController() {
			return $this->applicationController;
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setResponseCode($responseCode) {
			MAssertTypes('int', $responseCode);
			$this->responseCode = $responseCode;
		}
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function responseCode() {
			return $this->responseCode;
		}
		
		/**
		 * 
		 *
		 * @return MDictionary
		 */
		public function responseHeaders() {
			return $this->responseHeaders;
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function addResponseHeader(MString $header, MString $value) {
			$this->responseHeaders->setObjectForKey($header, $value);
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function removeReponseHeader(MString $header) {
			$this->responseHeaders->removeObjectForKey($header);
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function clearResponseHeaders() {
			$this->responseHeaders->removeAllObjects();
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setRestricted($restricted) {
			MAssertTypes('bool', $restricted);
			$this->restricted = $restricted;
		}
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function isRestricted() {
			return $this->restricted;
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setRealm(MString $realm) {
			$this->realm = $realm;
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function realm() {
			return $this->realm;
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setAccessDeniedView(MView $view = null) {
			$this->accessDeniedView = $view;
		}
		
		/**
		 * 
		 *
		 * @return MView
		 */
		public function accessDeniedView() {
			if (!$this->accessDeniedView) {
				$this->accessDeniedView = new MHTMLErrorDocumentView(N(MHTTPResponse::RESPONSE_FORBIDDEN), S("Forbidden"), S("The page you are trying to view is forbidden."));
			}
			return $this->accessDeniedView;
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setInvalidCredentialsView(MView $view = null) {
			$this->invalidCredentialsView = $view;
		}
		
		/**
		 * 
		 *
		 * @return MView
		 */
		public function invalidCredentialsView() {
			if (!$this->invalidCredentialsView) {
				$this->invalidCredentialsView = new MHTMLErrorDocumentView(N(MHTTPResponse::RESPONSE_FORBIDDEN), S("Access Denied"), S("Invalid Login Credentials"));
			}
			return $this->invalidCredentialsView;
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function authenticatedUserName() {
			return $this->authenticatedUserName;
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function address() {
			return MHTTPRequest()->url();
		}
		
	}

?>