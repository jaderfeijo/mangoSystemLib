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
	 * @author Jader Feijo <jader@movinpixel.com>
	 *
	 * @package mango.system.html
	 *
	 */
	class MHTMLMetaElementView extends MHTMLElementView {
		
		public function __construct(MString $name = null, MString $content = null) {
			parent::__construct(S("meta"));
			
			$this->setName($name);
			$this->setContent($content);
		}
		
		/******************** Properties ********************/
		
		/**
		 *
		 *
		 * @return MString
		 */
		public function name() {
			return $this->valueForProperty(S("name"));
		}
		
		/**
		 *
		 *
		 * @return void
		 */
		public function setName(MString $name = null) {
			$this->setValueForProperty(S("name"), $name);
		}
		
		/**
		 *
		 *
		 * @return MString
		 */
		public function property() {
			return $this->valueForProperty(S("property"));
		}
		
		/**
		 *
		 *
		 * @return void
		 */
		public function setProperty(MString $property = null) {
			$this->setValueForProperty(S("property"), $property);
		}
		
		/**
		 *
		 *
		 * @return MString
		 */
		public function content() {
			return $this->valueForProperty(S("content"));
		}
		
		/**
		 *
		 *
		 * @return void
		 */
		public function setContent(MString $content = null) {
			$this->setValueForProperty(S("content"), $content);
		}
		
		/**
		 *
		 *
		 * @return MString
		 */
		public function charset() {
			return $this->valueForProperty(S("charset"));
		}
		
		/**
		 *
		 *
		 * @return void
		 */
		public function setCharset(MString $charset = null) {
			$this->setValueForProperty(S("charset"), $charset);
		}
		
		/**
		 *
		 *
		 * @return MString
		 */
		public function httpEquiv() {
			return $this->valueForProperty(S("http-equiv"));
		}
		
		/**
		 *
		 *
		 * @return void
		 */
		public function setHttpEquiv(MString $httpEquiv = null) {
			$this->setValueForProperty(S("http-equiv"));
		}
		
		/**
		 *
		 *
		 * @return MString
		 */
		public function scheme() {
			return $this->valueForProperty(S("scheme"));
		}
		
		/**
		 *
		 *
		 * @return void
		 */
		public function setScheme(MString $scheme = null) {
			$this->setValueForProperty(S("scheme"), $scheme);
		}
		
	}

?>