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
class MHTTPRequest extends MObject {
	
	protected static MHTTPRequest $_request;
	
	public static function request() : MHTTPRequest {
		if (MHTTPRequest::$_request === null) {
			if (MApp()->isRunningInSimulatedRequestMode()) {
				MHTTPRequest::$_request = new MHTTPRequest(MApp()->simulatedRequestFile());
			} else {
				MHTTPRequest::$_request = new MHTTPRequest();
			}
		}
		return MHTTPRequest::$_request;
	}
	
	//
	// ************************************************************
	//
	
	protected ?MHTTPRequestMethod $_method;
	protected ?MString $_contentType;
	protected ?MData $_contents;
	protected ?MMutableDictionary $_inputParameters;
	protected ?MMutableDictionary $_inputFields;
	protected ?MArray $_arguments;
	protected ?MString $_baseAddress;
	protected ?MString $_relativeAddress;
	protected ?MString $_baseUrl;
	protected ?MString $_url;
	protected array $_server;
	protected array $_get;
	protected array $_post;
	
	protected string $_contentsFile;
	
	public function __construct(?MFile $simulatedRequestFile = null) {
		parent::__construct();
		
		$this->_method = null;
		$this->_contentType = null;
		$this->_contents = null;
		$this->_inputParameters = null;
		$this->_inputFields = null;
		$this->_arguments = null;
		$this->_baseAddress = null;
		$this->_relativeAddress = null;
		$this->_baseUrl = null;
		$this->_url = null;
		
		if ($simulatedRequestFile !== null) {
			if ($simulatedRequestFile->exists()) {
				$json = json_decode($simulatedRequestFile->contents());
				$this->_server = array_merge($_SERVER, $json['server']);
				$this->_get = array_merge($_GET, $json['get']);
				$this->_post = array_merge($_POST, $json['post']);
				$this->_contentsFile = $json['contents-file'];
			} else {
				throw new MFileNotFoundException($simulatedRequestFile->path());
			}
		} else {
			$this->_server = $_SERVER;
			$this->_get = $_GET;
			$this->_post = $_POST;
			$this->_contentsFile = "php://input";
		}
	}
	
	/******************** Properties ********************/
	
	public function server() : array {
		return $this->_server;
	}
	
	public function get() : array {
		return $this->_get;
	}
	
	public function post() : array {
		return $this->_post;
	}
	
	public function method() : string {
		if ($this->_method === null) {
			$this->_method = $this->server()['REQUEST_METHOD'];
		}
		return $this->_method;
	}
	
	public function contentType() : MString {
		if ($this->_contentType === null) {
			if (isset($this->server()['CONTENT_TYPE'])) {
				$this->_contentType = S($this->server()['CONTENT_TYPE']);
			}
		}
		return $this->_contentType;
	}
	
	public function contents() : MData {
		if ($this->_contents === null) {
			$this->_contents = new MData(file_get_contents($this->_contentsFile));
		}
		return $this->_contents;
	}
	
	public function inputParameters() : MDictionary {
		if ($this->_inputParameters === null) {
			$this->_inputParameters = new MMutableDictionary();
			$data = $this->get();
			foreach ($data as $key => $value) {
				$this->_inputParameters->setObjectForKey(S($key), $value);
			}
		}
		return $this->_inputParameters;
	}
	
	public function inputFields() : MDictionary {
		if ($this->_inputFields === null) {
			$this->_inputFields = new MMutableDictionary();
			$data = $this->post();
			if (!$data) {
				$data = array();
				parse_str($this->contents()->getBytes(), $data);
			}
			foreach ($data as $key => $value) {
				$this->_inputFields->setObjectForKey(S($key), $value);
			}
		}
		return $this->_inputFields;
	}
	
	public function arguments() : MArray {
		if ($this->_arguments === null) {
			if (!$this->relativeAddress()->isEmpty()) {
				$argsString = $this->relativeAddress()->componentsSeparatedByString(S("?"), false)->objectAtIndex(0);
				$this->_arguments = $argsString->componentsSeparatedByString(S("/"), false);
			} else {
				$this->_arguments = new MArray();
			}
		}
		return $this->_arguments;
	}
	
	public function baseAddress() : MString {
		if ($this->_baseAddress === null) {
			$this->_baseAddress = S($this->server()['SCRIPT_NAME'])->stringByReplacingOccurrencesOfString(S("index.php"), S(""))->stringByTrimmingCharactersInSet(S("/"));
		}
		return $this->_baseAddress;
	}
	
	public function relativeAddress() : MString {
		if ($this->_relativeAddress === null) {
			$this->_relativeAddress = S($this->server()['REQUEST_URI'])->stringByReplacingOccurrencesOfString($this->baseAddress(), S(""))->stringByTrimmingCharactersInSet(S("/"));
		}
		return $this->_relativeAddress;
	}
	
	public function baseUrl() : MString {
		if ($this->_baseUrl === null) {
			$url = new MMutableString();
			if (!empty($this->server()['HTTPS'])) {
				$url->appendString(S("https://"));
			} else {
				$url->appendString(S("http://"));
			}
			$url->appendString(S($this->server()['HTTP_HOST']));
			$this->_baseUrl = $url;
		}
		return $this->_baseUrl;
	}
	
	public function url() : MString {
		if (!$this->_url) {
			$this->_url = $this->baseUrl()->stringByAppendingPathComponent($this->baseAddress())->stringByAppendingPathComponent($this->relativeAddress());
		}
		return $this->_url;
	}
	
}

