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
	class MView extends MObject {
		
		//
		// ************************************************************
		//
		
		protected $subviews;
		protected $superview;
		protected $contentType;
		
		/**
		 * 
		 *
		 * @return MView
		 */
		public function __construct() {
			parent::__construct();
			
			$this->subviews = new MMutableArray();
			$this->superview = null;
			$this->contentType = S("text/plain");
		}
		
		/******************** Properties ********************/
		
		/**
		 * 
		 *
		 * @return MArray
		 */
		public function subviews() {
			return $this->subviews;
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setSuperview(MView $superview = null) {
			$this->superview = $superview;
		}
		
		/**
		 * 
		 *
		 * @return MView
		 */
		public function superview() {
			return $this->superview;
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setContentType(MString $contentType) {
			$this->contentType = $contentType;
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function contentType() {
			return $this->contentType;
		}
		
		/******************** Methods ********************/
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function addSubview(MView $view) {
			$this->subviews->addObject($view);
			$view->setSuperview($this);
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function removeSubview(MView $view) {
			$this->subviews->removeObject($view);
			$view->setSuperview(null);
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function removeAllSubviews() {
			$this->subviews->removeAllObjects();
		}
				
		/******************** MObject Methods ********************/
		
		/**
		 * 
		 */
		public function toString() {
			$str = new MMutableString();
			foreach ($this->subviews()->toArray() as $view) {
				if (!$this->subviews()->isLastObject($view)) {
					$str->appendLine($view->toString());
				} else {
					$str->appendString($view->toString());
				}
			}
			return $str;
		}
		
	}

?>