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
	
	import('mango.system.exceptions');
	
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
	class MHTTPRequest extends MObject {
		
		const REQUEST_METHOD_OPTIONS = "OPTIONS";
		const REQUEST_METHOD_GET = "GET";
		const REQUEST_METHOD_HEAD = "HEAD";
		const REQUEST_METHOD_POST = "POST";
		const REQUEST_METHOD_PUT = "PUT";
		const REQUEST_METHOD_DELETE = "DELETE";
		const REQUEST_METHOD_TRACE = "TRACE";
		const REQUEST_METHOD_CONNECT = "CONNECT";
		
		protected static $request;
		
		/**
		 * 
		 *
		 * @return MHTTPRequest
		 */
		public static function request() {
			if (!MHTTPRequest::$request) {
				if (isRunningInSimulatedRequestMode()) {
					MHTTPRequest::$request = new MHTTPRequest(simulatedRequestFileName(), simulatedRequestName());
				} else {
					MHTTPRequest::$request = new MHTTPRequest();
				}
			}
			return MHTTPRequest::$request;
		}
		
		//
		// ************************************************************
		//
		
		protected $method;
		protected $contentType;
		protected $contents;
		protected $inputParameters;
		protected $inputFields;
		protected $arguments;
		protected $baseAddress;
		protected $relativeAddress;
		protected $baseUrl;
		protected $url;
		protected $server;
		protected $get;
		protected $post;
		
		protected $contentsFile;
		
		/**
		 * @internal
		 *
		 * @return MHTTPRequest
		 */
		public function __construct($simulatedRequestFile = null, $simulatedRequestName = null) {
			parent::__construct();
			
			$this->method = null;
			$this->contentType = null;
			$this->contents = null;
			$this->inputParameters = null;
			$this->inputFields = null;
			$this->arguments = null;
			$this->baseAddress = null;
			$this->relativeAddress = null;
			$this->baseUrl = null;
			$this->url = null;
			
			if ($simulatedRequestFile) {
				if (file_exists($simulatedRequestFile)) {
					$json = json_decode(file_get_contents($simulatedRequestFile), true);
					if (!empty($simulatedRequestName)) {
						$request = $json[$simulatedRequestName];
						if (!empty($request)) {
							$this->server = array_merge($_SERVER, $json[$simulatedRequestName]['server']);
							$this->get = array_merge($_GET, $json[$simulatedRequestName]['get']);
							$this->post = array_merge($_POST, $json[$simulatedRequestName]['post']);
							$this->contentsFile = $json[$simulatedRequestName]['contents-file'];
						} else {
							throw new Exception(Sf("Could not find request named '%s' inside '%s'", $simulatedRequestName, $simulatedRequestFile));
						}
					} else {
						throw new MException(S("You must specify a 'request_name'. Usage: hhvm -m index.php --simulated-request [json_request_file] [request_name]"));
					}
				} else {
					throw new MFileNotFoundException(S($simulatedRequestFile));
				}
			} else {
				$this->server = $_SERVER;
				$this->get = $_GET;
				$this->post = $_POST;
				$this->contentsFile = "php://input";
			}
		}
		
		/******************** Properties ********************/
		
		/**
		 * @return array
		 */
		public function server() {
			return $this->server;
		}
		
		/**
		 * @return array
		 */
		public function get() {
			return $this->get;
		}
		
		/**
		 * @return array
		 */
		public function post() {
			return $this->post;
		}
		
		/**
		 * 
		 *
		 * @return string
		 */
		public function method() {
			if (!$this->method) {
				$this->method = $this->server()['REQUEST_METHOD'];
			}
			return $this->method;
		}
		
		/**
		 * @return MString
		 */
		public function contentType() {
			if (!$this->contentType) {
				if (isset($this->server()['CONTENT_TYPE'])) {
					$this->contentType = S($this->server()['CONTENT_TYPE']);
				}
			}
			return $this->contentType;
		}
		
		/**
		 * @return MData
		 */
		public function contents() {
			if (!$this->contents) {
				$this->contents = new MData(file_get_contents($this->contentsFile));
			}
			return $this->contents;
		}
		
		/**
		 * @return MDictionary
		 */
		public function inputParameters() {
			if (!$this->inputParameters) {
				$this->inputParameters = new MMutableDictionary();
				$data = $this->get();
				foreach ($data as $key => $value) {
					$this->inputParameters->setObjectForKey(S($key), $value);
				}
			}
			return $this->inputParameters;
		}
		
		/**
		 * 
		 *
		 * @return MDictionary
		 */
		public function inputFields() {
			if (!$this->inputFields) {
				$this->inputFields = new MMutableDictionary();
				$data = $this->post();
				if (!$data) {
					$data = array();
					parse_str($this->contents()->getBytes(), $data);
				}
				foreach ($data as $key => $value) {
					$this->inputFields->setObjectForKey(S($key), $value);
				}
			}
			return $this->inputFields;
		}
		
		/**
		 * 
		 *
		 * @return MArray
		 */
		public function arguments() {
			if (!$this->arguments) {
				if (!$this->relativeAddress()->isEmpty()) {
					$argsString = $this->relativeAddress()->componentsSeparatedByString(S("?"), false)->objectAtIndex(0);
					$this->arguments = $argsString->componentsSeparatedByString(S("/"), false);
				} else {
					$this->arguments = new MArray();
				}
			}
			return $this->arguments;
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function baseAddress() {
			if (!$this->baseAddress) {
				$this->baseAddress = S($this->server()['SCRIPT_NAME'])->stringByReplacingOccurrencesOfString(S("index.php"), S(""))->stringByTrimmingCharactersInSet(S("/"));
			}
			return $this->baseAddress;
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function relativeAddress() {
			if (!$this->relativeAddress) {
				$this->relativeAddress = S($this->server()['REQUEST_URI'])->stringByReplacingOccurrencesOfString($this->baseAddress(), S(""))->stringByTrimmingCharactersInSet(S("/"));
			}
			return $this->relativeAddress;
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function baseUrl() {
			if (!$this->baseUrl) {
				$url = new MMutableString();
				if (!empty($this->server()['HTTPS'])) {
					$url->appendString(S("https://"));
				} else {
					$url->appendString(S("http://"));
				}
				$url->appendString(S($this->server()['HTTP_HOST']));
				$this->baseUrl = $url;
			}
			return $this->baseUrl;
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function url() {
			if (!$this->url) {
				$this->url = $this->baseUrl()->stringByAppendingPathComponent($this->baseAddress())->stringByAppendingPathComponent($this->relativeAddress());
			}
			return $this->url;
		}
		
	}

?>