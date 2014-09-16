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
	
	package('mango.system.html');
	
	import('mango.system.*');
	
	/**
	 * 
	 *
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @license MIT
	 *
	 * @package mango.system.html
	 *
	 */
	class MHTMLErrorDocumentView extends MHTMLDocumentView {
		
		//
		// ************************************************************
		//
		
		protected $errorCode;
		protected $errorName;
		protected $errorMessage;
		
		protected $errorCodeHeaderView;
		protected $errorDescriptionParagraphView;
		
		/**
		 * 
		 *
		 * @return MHTMLErrorDocumentView
		 */
		public function __construct(MNumber $errorCode = null, MString $errorName = null, MString $errorMessage = null) {
			parent::__construct();
			
			if (!$errorCode) $errorCode = N(0);
			if (!$errorName) $errorName = S("Unknown Error");
			if (!$errorMessage) $errorMessage = S("An Unknown Error Occoured!");
			
			$this->errorCode = $errorCode;
			$this->errorName = $errorName;
			$this->errorMessage = $errorMessage;
			
			$this->errorCodeHeaderView = new MHTMLHeaderElementView();
			$this->addSubview($this->errorCodeHeaderView);
			
			$this->errorDescriptionParagraphView = new MHTMLParagraphView();
			$this->addSubview($this->errorDescriptionParagraphView);
			
			$this->updateView();
		}
		
		/******************** Protected ********************/
		
		/**
		 * 
		 *
		 * @return void
		 */
		protected function updateView() {
			$this->title()->setText(Sf("%s - %s", $this->errorCode()->toString(), $this->errorName()));
			$this->errorCodeHeaderView->setText($this->errorCode()->toString());
			$this->errorDescriptionParagraphView->setText($this->errorMessage());
		}
		
		/******************** Properties ********************/
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setErrorCode(MNumber $errorCode) {
			$this->errorCode = $errorCode;
			$this->updateView();
		}
		
		/**
		 * 
		 *
		 * @return MNumber
		 */
		public function errorCode() {
			return $this->errorCode;
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setErrorName(MString $errorName) {
			$this->errorName = $errorName;
			$this->updateView();
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function errorName() {
			return $this->errorName;
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setErrorMessage(MString $errorMessage) {
			$this->errorMessage = $errorMessage;
			$this->updateView();
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function errorMessage() {
			return $this->errorMessage;
		}
		
	}

?>