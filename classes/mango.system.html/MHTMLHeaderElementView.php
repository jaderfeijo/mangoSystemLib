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
	class MHTMLHeaderElementView extends MHTMLElementView {
		
		const HTML_HEADER_TYPE_1 = 1;
		const HTML_HEADER_TYPE_2 = 2;
		const HTML_HEADER_TYPE_3 = 3;
		const HTML_HEADER_TYPE_4 = 4;
		const HTML_HEADER_TYPE_5 = 5;
		const HTML_HEADER_TYPE_6 = 6;
		
		//
		// ************************************************************
		//
		
		protected $headerType;
		
		/**
		 * 
		 *
		 * @return MHTMLHeaderElementView
		 */
		public function __construct(MString $text = null, $headerType = MHTMLHeaderElementView::HTML_HEADER_TYPE_1) {
			parent::__construct(S("h1"), $text);
			
			$this->headerType = $headerType;
		}
		
		/******************** Properties ********************/
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setHeaderType($headerType) {
			$this->headerType = $headerType;
		}
		
		/**
		 * 
		 *
		 * @return int
		 */
		public function headerType() {
			return $this->headerType;
		}
		
		/******************** MHTMLElementView ********************/
		
		/**
		 * 
		 */
		public function element() {
			return Sf("h%d", $this->headerType);
		}
		
	}

?>