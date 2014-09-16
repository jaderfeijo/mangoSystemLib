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
	class MHTTPResponse extends MObject {
		
		const RESPONSE_CONTINUE = 100;
		const RESPONSE_SWITCHING_PROTOCOLS = 101;
		const RESPONSE_PROCESSING = 102;
		const RESPONSE_OK = 200;
		const RESPONSE_CREATED = 201;
		const RESPONSE_ACCEPTED = 202;
		const RESPONSE_NON_AUTHORITATIVE_INFORMATION = 203;
		const RESPONSE_NO_CONTENT = 204;
		const RESPONSE_RESET_CONTENT = 205;
		const RESPONSE_PARTIAL_CONTENT = 206;
		const RESPONSE_MULTI_STATUS = 207;
		const RESPONSE_ALREADY_REPORTED = 208;
		const RESPONSE_IM_USED = 226;
		const RESPONSE_MULTIPLE_CHOICES = 300;
		const RESPONSE_MOVED_PERMANENTLY = 301;
		const RESPONSE_FOUND = 302;
		const RESPONSE_SEE_OTHER = 303;
		const RESPONSE_NOT_MODIFIED = 304;
		const RESPONSE_USE_PROXY = 305;
		const RESPONSE_SWITCH_PROXY = 306;
		const RESPONSE_TEMPORARY_REDIRECT = 307;
		const RESPONSE_PERMANENT_REDIRECT = 308;
		const RESPONSE_BAD_REQUEST = 400;
		const RESPONSE_UNAUTHORIZED = 401;
		const RESPONSE_PAYMENT_REQUIRED = 402;
		const RESPONSE_FORBIDDEN = 403;
		const RESPONSE_NOT_FOUND = 404;
		const RESPONSE_METHOD_NOT_ALLOWED = 405;
		const RESPONSE_NOT_ACCEPTABLE = 406;
		const RESPONSE_PROXY_AUTHENTICATION_REQUIRED = 407;
		const RESPONSE_REQUEST_TIMEOUT = 408;
		const RESPONSE_CONFLICT = 409;
		const RESPONSE_GONE = 410;
		const RESPONSE_LENGTH_REQUIRED = 411;
		const RESPONSE_PRECONDITION_FAILED = 412;
		const RESPONSE_REQUEST_ENTITY_TOO_LARGE = 413;
		const RESPONSE_REQUEST_URI_TOO_LONG = 414;
		const RESPONSE_UNSUPPORTED_MEDIA_TYPE = 415;
		const RESPONSE_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
		const RESPONSE_EXPECTATION_FAILED = 417;
		const RESPONSE_IM_A_TEAPOT = 418;
		const RESPONSE_ENHANCE_YOUR_CALM = 420;
		const RESPONSE_UNPROCESSABLE_ENTITY = 422;
		const RESPONSE_LOCKED = 423;
		const RESPONSE_FAILED_DEPENDENCY = 424;
		const RESPONSE_UNORDERED_COLLECTION = 425;
		const RESPONSE_UPGRADE_REQUIRED = 426;
		const RESPONSE_PRECONDITION_REQUIRED = 428;
		const RESPONSE_TOO_MANY_REQUESTS = 429;
		const RESPONSE_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
		const RESPONSE_NO_RESPONSE = 444;
		const RESPONSE_RETRY_WITH = 449;
		const RESPONSE_BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS = 450;
		const RESPONSE_CLIENT_CLOSED_REQUEST = 499;
		const RESPONSE_INTERNAL_SERVER_ERROR = 500;
		const RESPONSE_NOT_IMPLEMENTED = 501;
		const RESPONSE_BAD_GATEWAY = 502;
		const RESPONSE_SERVICE_UNAVAILABLE = 503;
		const RESPONSE_GATEWAY_TIMEOUT = 504;
		const RESPONSE_HTTP_VERSION_NOT_SUPPORTED = 505;
		const RESPONSE_VARIANT_ALSO_NEGOTIATES = 506;
		const RESPONSE_INSUFFICIENT_STORAGE = 507;
		const RESPONSE_LOOP_DETECTED = 508;
		const RESPONSE_BANDWIDTH_LIMIT_EXCEEDED = 509;
		const RESPONSE_NOT_EXTENDED = 510;
		const RESPONSE_NETWORK_AUTHENTICATION_REQUIRED = 511;
		const RESPONSE_NETWORK_READ_TIMEOUT_ERROR = 598;
		const RESPONSE_NETWORK_CONNECT_TIMEOUT_ERROR = 599;
		
		protected static $responses;
		
		/**
		 * 
		 *
		 * @return MString
		 */
		protected static function responseStringForCode($code) {
			MAssertTypes('int', $code);
			if (!MHTTPResponse::$responses) {
				MHTTPResponse::$responses = array(
					MHTTPResponse::RESPONSE_CONTINUE => S("Continue"),
					MHTTPResponse::RESPONSE_SWITCHING_PROTOCOLS => S("Switching Protocols"),
					MHTTPResponse::RESPONSE_PROCESSING => S("Processing"),
					MHTTPResponse::RESPONSE_OK => S("OK"),
					MHTTPResponse::RESPONSE_CREATED => S("Created"),
					MHTTPResponse::RESPONSE_ACCEPTED => S("Accepted"),
					MHTTPResponse::RESPONSE_NON_AUTHORITATIVE_INFORMATION => S("Non-Authoritative Information"),
					MHTTPResponse::RESPONSE_NO_CONTENT => S("No Content"),
					MHTTPResponse::RESPONSE_RESET_CONTENT => S("Reset Content"),
					MHTTPResponse::RESPONSE_PARTIAL_CONTENT => S("Partial Content"),
					MHTTPResponse::RESPONSE_MULTI_STATUS => S("Multi-Status"),
					MHTTPResponse::RESPONSE_ALREADY_REPORTED => S("Already Reported"),
					MHTTPResponse::RESPONSE_IM_USED => S("IM Used"),
					MHTTPResponse::RESPONSE_MULTIPLE_CHOICES => S("Multiple Choices"),
					MHTTPResponse::RESPONSE_MOVED_PERMANENTLY => S("Moved Permanently"),
					MHTTPResponse::RESPONSE_FOUND => S("Found"),
					MHTTPResponse::RESPONSE_SEE_OTHER => S("See Other"),
					MHTTPResponse::RESPONSE_NOT_MODIFIED => S("Not Modified"),
					MHTTPResponse::RESPONSE_USE_PROXY => S("Use Proxy"),
					MHTTPResponse::RESPONSE_SWITCH_PROXY => S("Switch Proxy"),
					MHTTPResponse::RESPONSE_TEMPORARY_REDIRECT => S("Temporary Redirect"),
					MHTTPResponse::RESPONSE_PERMANENT_REDIRECT => S("Permanent Redirect"),
					MHTTPResponse::RESPONSE_BAD_REQUEST => S("Bad Request"),
					MHTTPResponse::RESPONSE_UNAUTHORIZED => S("Unauthorized"),
					MHTTPResponse::RESPONSE_PAYMENT_REQUIRED => S("Payment Required"),
					MHTTPResponse::RESPONSE_FORBIDDEN => S("Forbidden"),
					MHTTPResponse::RESPONSE_NOT_FOUND => S("Not Found"),
					MHTTPResponse::RESPONSE_METHOD_NOT_ALLOWED => S("Method Not Allowed"),
					MHTTPResponse::RESPONSE_NOT_ACCEPTABLE => S("Not Acceptable"),
					MHTTPResponse::RESPONSE_PROXY_AUTHENTICATION_REQUIRED => S("Proxy Authentication Required"),
					MHTTPResponse::RESPONSE_REQUEST_TIMEOUT => S("Request Timeout"),
					MHTTPResponse::RESPONSE_CONFLICT => S("Conflict"),
					MHTTPResponse::RESPONSE_GONE => S("Gone"),
					MHTTPResponse::RESPONSE_LENGTH_REQUIRED => S("Length Required"),
					MHTTPResponse::RESPONSE_PRECONDITION_FAILED => S("Precondition Failed"),
					MHTTPResponse::RESPONSE_REQUEST_ENTITY_TOO_LARGE => S("Request Entity Too Large"),
					MHTTPResponse::RESPONSE_REQUEST_URI_TOO_LONG => S("Request-URI Too Long"),
					MHTTPResponse::RESPONSE_UNSUPPORTED_MEDIA_TYPE => S("Unsupported Media Type"),
					MHTTPResponse::RESPONSE_REQUESTED_RANGE_NOT_SATISFIABLE => S("Requested Range Not Satisfiable"),
					MHTTPResponse::RESPONSE_EXPECTATION_FAILED => S("Expectation Failed"),
					MHTTPResponse::RESPONSE_IM_A_TEAPOT => S("I'm a teapot"),
					MHTTPResponse::RESPONSE_ENHANCE_YOUR_CALM => S("Enhance Your Calm"),
					MHTTPResponse::RESPONSE_UNPROCESSABLE_ENTITY => S("Unprocessable Entity"),
					MHTTPResponse::RESPONSE_LOCKED => S("Locked"),
					MHTTPResponse::RESPONSE_FAILED_DEPENDENCY => S("Failed Dependency"),
					MHTTPResponse::RESPONSE_UNORDERED_COLLECTION => S("Unordered Collection"),
					MHTTPResponse::RESPONSE_UPGRADE_REQUIRED => S("Upgrade Required"),
					MHTTPResponse::RESPONSE_PRECONDITION_REQUIRED => S("Precondition Required"),
					MHTTPResponse::RESPONSE_TOO_MANY_REQUESTS => S("Too Many Requests"),
					MHTTPResponse::RESPONSE_REQUEST_HEADER_FIELDS_TOO_LARGE => S("Request Header Fields Too Large"),
					MHTTPResponse::RESPONSE_NO_RESPONSE => S("No Response"),
					MHTTPResponse::RESPONSE_RETRY_WITH => S("Retry With"),
					MHTTPResponse::RESPONSE_BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS => S("Blocked by Windows Parental Controls"),
					MHTTPResponse::RESPONSE_CLIENT_CLOSED_REQUEST => S("Client Closed Request"),
					MHTTPResponse::RESPONSE_INTERNAL_SERVER_ERROR => S("Internal Server Error"),
					MHTTPResponse::RESPONSE_NOT_IMPLEMENTED => S("Not Implemented"),
					MHTTPResponse::RESPONSE_BAD_GATEWAY => S("Bad Gateway"),
					MHTTPResponse::RESPONSE_SERVICE_UNAVAILABLE => S("Service Unavailable"),
					MHTTPResponse::RESPONSE_GATEWAY_TIMEOUT => S("Gateway Timeout"),
					MHTTPResponse::RESPONSE_HTTP_VERSION_NOT_SUPPORTED => S("HTTP Version Not Supported"),
					MHTTPResponse::RESPONSE_VARIANT_ALSO_NEGOTIATES => S("Variant Also Negotiates"),
					MHTTPResponse::RESPONSE_INSUFFICIENT_STORAGE => S("Insufficient Storage"),
					MHTTPResponse::RESPONSE_LOOP_DETECTED => S("Loop Detected"),
					MHTTPResponse::RESPONSE_BANDWIDTH_LIMIT_EXCEEDED => S("Bandwidth Limit Exceeded"),
					MHTTPResponse::RESPONSE_NOT_EXTENDED => S("Not Extended"),
					MHTTPResponse::RESPONSE_NETWORK_AUTHENTICATION_REQUIRED => S("Network Authentication Required"),
					MHTTPResponse::RESPONSE_NETWORK_READ_TIMEOUT_ERROR => S("Network read timeout error"),
					MHTTPResponse::RESPONSE_NETWORK_CONNECT_TIMEOUT_ERROR => S("Network connect timeout error")
				);
			}
			return MHTTPResponse::$responses[$code];
		}
		
		//
		// ************************************************************
		//
		
		protected $code;
		protected $body;
		protected $headers;
		
		/**
		 * 
		 *
		 * @return MHTTPResponse
		 */
		public function __construct($code = MHTTPResponse::RESPONSE_OK, MString $body = null) {
			MAssertTypes('int', $code);
			
			parent::__construct();
			
			$this->code = $code;
			$this->body = $body;
			$this->headers = new MMutableDictionary();
		}
		
		/******************** Properties ********************/
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setCode($code) {
			MAssertTypes('int', $code);
			$this->code = $code;
		}
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function code() {
			return $this->code;
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setBody(MString $body) {
			$this->body = $body;
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function body() {
			return $this->body;
		}
		
		/**
		 * 
		 *
		 * @return MDictionary
		 */
		public function headers() {
			return $this->headers;
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function addHeader(MString $header, MString $value) {
			$this->headers->setObjectForKey($header, $value);
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function removeHeader(MString $header) {
			$this->headers->removeObjectForKey($header);
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function clearAllHeaders() {
			$this->headers->removeAllObjects();
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function responseString() {
			return MHTTPResponse::responseStringForCode($this->code());
		}
		
	}

?>