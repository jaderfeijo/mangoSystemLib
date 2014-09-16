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
	class MErrorViewController extends MViewController {
		
		//
		// ************************************************************
		//
		
		protected $errorView;
		
		/**
		 * 
		 *
		 * @return MErrorViewController
		 */
		public function __construct($responseCode, MNumber $errorCode = null, MString $errorName = null, MString $errorDescription = null) {
			MAssertTypes('int', $responseCode);
			parent::__construct();
			
			$this->setResponseCode($responseCode);
			
			$this->errorView = new MHTMLErrorDocumentView($errorCode, $errorName, $errorDescription);
			$this->setView($this->errorView);
		}
		
		/******************** Methods ********************/
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setErrorCode(MNumber $errorCode) {
			$this->errorView->setErrorCode($errorCode);
		}
		
		/**
		 * 
		 *
		 * @return MNumber
		 */
		public function errorCode() {
			return $this->errorView->errorCode();
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setErrorName(MString $errorName) {
			$this->errorView->setErrorName($errorName);
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function errorName() {
			return $this->errorView->errorName();
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setErrorDescription(MString $errorDescription) {
			$this->errorView->setErrorDescription($errorDescription);
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function errorDescription() {
			return $this->errorView->errorDescription();
		}
		
	}

?>