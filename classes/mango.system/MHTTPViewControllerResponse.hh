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
class MHTTPViewControllerResponse extends MHTTPResponse {
	
	//
	// ************************************************************
	//
	
	protected ?MViewController $_viewController;
	
	public function __construct(?MViewController $viewController = null) {
		parent::__construct();
		
		$this->_viewController = $viewController;
	}
	
	/******************** Properties ********************/
	
	public function setViewController(?MViewController $viewController) : void {
		$this->_viewController = $viewController;
	}
	
	public function viewController() : ?MViewController {
		return $this->_viewController;
	}
	
	/******************** MHTTPResponse ********************/
	
	public function setCode(MHTTPResponseCode $code) : void {
		$this->viewController()->setResponseCode($code);
	}
	
	public function code() : MHTTPResponseCode {
		return $this->viewController()->responseCode();
	}
	
	public function setBody(MString $body) : void {
		throw new MMethodNotSupportedException(S("setBody"));
	}
	
	public function body() : MString {
		return $this->viewController()->view()->toString();
	}
	
	public function headers() : MDictionary {
		return $this->viewController()->responseHeaders();
	}
	
	public function addHeader(MString $header, MString $value) : void {
		$this->viewController()->addResponseHeader($header, $value);
	}
	
	public function removeHeader(MString $header) : void {
		$this->viewController()->removeResponseHeader($header);
	}
	
	public function clearAllHeaders() : void {
		$this->viewController()->clearResponseHeaders();
	}
	
}

