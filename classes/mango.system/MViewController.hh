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
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
class MViewController extends MObject {
	
	//
	// ************************************************************
	//
	
	protected MView $_view;
	protected ?MApplicationController $_applicationController;
	protected MHTTPResponseCode $_responseCode;
	protected MMutableDictionary $_responseHeaders;
	protected bool $_restricted;
	protected MString $_realm;
	protected ?MView $_accessDeniedView;
	protected ?MView $_invalidCredentialsView;
	protected MString $_authenticatedUserName;
	protected ?MMutableDictionary $_parameterValues;
	protected ?MMutableDictionary $_fieldValues;
	protected ?MViewController $_parentViewController;
	protected MMutableArray $_childViewControllers;
	
	public function __construct(?MView $view = null) {
		parent::__construct();
		$this->_view = $view;
		$this->_applicationController = null;
		$this->_responseCode = MHTTPResponseCode::OK;
		$this->_responseHeaders = new MMutableDictionary();
		$this->_restricted = false;
		$this->_realm = S("Restricted Area");
		$this->_accessDeniedView = null;
		$this->_invalidCredentialsView = null;
		$this->_authenticatedUserName = null;
		$this->_parameterValues = null;
		$this->_fieldValues = null;
		$this->_parentViewController = null;
		$this->_childViewControllers = new MMutableArray();
	}
	
	/******************** Protected ********************/
	
	protected function _parseDigest(string $digest) : MDictionary {
		$data = new MMutableDictionary();
		$parts = explode(',', $digest);
		
		foreach($parts as $p) {
			$p = trim($p);
			$kvpair = explode('=', $p);
			$kvpair[1] = str_replace("\"", "", $kvpair[1]);
			$data->setObjectForKey(S($kvpair[0]), S($kvpair[1]));
		}
		
		return (MDictionary)$data;
	}
	
	protected function _parseParameterValues() : void {
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
	
	protected function _parseFieldValues() : void {
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
	
	public function _setValueForParameter(MApplicationControllerParameter $parameter, string $value) : bool {
		$object = null;
		
		try {
			if ($parameter->type() == MApplicationControllerParameter::StringType) {
				$object = S($value);
			} else if ($parameter->type() == MApplicationControllerParameter::IntegerType) {
				$object = I(intval($value));
			} else if ($parameter->type() == MApplicationControllerParameter::FloatType) {
				$object = F(floatval($value));
			} else if ($parameter->type() == MApplicationControllerParameter::BooleanType) {
				$object = B(boolval($value));
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
		
		if (!$this->_parameterValues) {
			$this->_parameterValues = new MMutableDictionary();
		}
		
		if ($object !== null) {
			$this->_parameterValues->setObjectForKey($parameter, $object);
			return true;
		} else {
			return false;
		}
	}
	
	protected function _setValueForField(MApplicationControllerField $field, string $value) : bool {
		$object = null;
		
		try {
			if ($field->type() == MApplicationControllerField::StringType) {
				$object = S($value);
			} else if ($field->type() == MApplicationControllerField::IntegerType) {
				$object = I(intval($value));
			} else if ($field->type() == MApplicationControllerField::FloatType) {
				$object = F(floatval($value));
			} else if ($field->type() == MApplicationControllerField::BooleanType) {
				$object = B(boolval($value));
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
		
		if (!$this->_fieldValues) {
			$this->_fieldValues = new MMutableDictionary();
		}
		
		if ($object !== null) {
			$this->_fieldValues->setObjectForKey($field, $object);
			return true;
		} else {
			return false;
		}
	}
	
	protected function _view() : MView {
		if (!$this->_view) {
			$this->loadView();
		}
		return $this->_view;
	}
	
	/******************** Methods ********************/
	
	public function passwordForUserWithName(MString $username) : ?MString {
		return null;
	}
	
	public function loadView() : void {
		$this->setView(new MView());
	}
	
	public function valueForParameter(MApplicationControllerParameter $parameter) : ?MObject {
		if (!$this->_parameterValues) {
			$this->_parseParameterValues();
		}
		if ($this->_parameterValues) {
			return $this->_parameterValues->objectForKey($parameter);
		} else {
			return null;
		}
	}
	
	public function valueForField(MApplicationControllerField $field) : ?MObject {
		if (!$this->_fieldValues) {
			$this->_parseFieldValues();
		}
		if ($this->_fieldValues) {
			return $this->_fieldValues->objectForKey($field);
		} else {
			return null;
		}
	}
	
	public function valueForParameterNamed(MString $parameterName) : ?MObject {
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
	
	public function valueForFieldNamed(MString $fieldName) : ?MObject {
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
	
	public function parentViewController() : ?MViewController {
		return $this->_parentViewController;
	}
	
	public function setParentViewController(?MViewController $viewController = null) : void {
		$this->_parentViewController = $viewController;
	}
	
	public function childViewControllers() : MArray {
		return (MArray)$this->_childViewControllers;
	}
	
	public function addChildViewController(MViewController $viewController) : void {
		$this->_childViewControllers->addObject($viewController);
		$viewController->setParentViewController($this);
	}
	
	public function removeChildViewController(MViewController $viewController) : void {
		$this->_childViewControllers->removeObject($viewController);
		$viewController->setParentViewController(null);
	}
	
	public function setView(MView $view) : void {
		$this->_view = $view;
		$this->addResponseHeader(S("Content-Type"), $view->contentType());
	}
	
	public function view() : MView {
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
	
	public function setApplicationController(MApplicationController $applicationController) : void {
		$this->_applicationController = $applicationController;
	}
	
	public function applicationController() : MApplicationController {
		return $this->_applicationController;
	}
	
	public function setResponseCode(MHTTPResponseCode $responseCode) : void {
		$this->_responseCode = $responseCode;
	}
	
	public function responseCode() : MHTTPResponseCode {
		return $this->_responseCode;
	}
	
	public function responseHeaders() : MDictionary {
		return (MDictionary)$this->_responseHeaders;
	}

	public function addResponseHeader(MString $header, MString $value) : void {
		$this->_responseHeaders->setObjectForKey($header, $value);
	}
	
	public function removeReponseHeader(MString $header) : void {
		$this->_responseHeaders->removeObjectForKey($header);
	}
	
	public function clearResponseHeaders() : void {
		$this->_responseHeaders->removeAllObjects();
	}
	
	public function setRestricted(bool $restricted) : void {
		$this->_restricted = $restricted;
	}
	
	public function isRestricted() : bool {
		return $this->_restricted;
	}
	
	public function setRealm(MString $realm) : void {
		$this->_realm = $realm;
	}
	
	public function realm() : void {
		return $this->_realm;
	}
	
	public function setAccessDeniedView(?MView $view = null) : void {
		$this->_accessDeniedView = $view;
	}
	
	public function accessDeniedView() : MView {
		if ($this->_accessDeniedView === null) {
			$this->_accessDeniedView = new MHTMLErrorDocumentView(N(MHTTPResponse::RESPONSE_FORBIDDEN), S("Forbidden"), S("The page you are trying to view is forbidden."));
		}
		return $this->_accessDeniedView;
	}
	
	public function setInvalidCredentialsView(?MView $view = null) : void {
		$this->_invalidCredentialsView = $view;
	}
	
	public function invalidCredentialsView() : MView {
		if (!$this->_invalidCredentialsView) {
			$this->_invalidCredentialsView = new MHTMLErrorDocumentView(N(MHTTPResponse::RESPONSE_FORBIDDEN), S("Access Denied"), S("Invalid Login Credentials"));
		}
		return $this->_invalidCredentialsView;
	}
	
	public function authenticatedUserName() : MString {
		return $this->_authenticatedUserName;
	}
	
	public function address() : MString {
		return MHTTPRequest()->url();
	}
	
}

