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
	abstract class MMarkupElementView extends MView {
		
		//
		// ************************************************************
		//
		
		protected $element;
		protected $text;
		protected $properties;
		protected $indentsCode;
		protected $indentLevel;
		protected $shouldAppendEmptyLine;
		
		/**
		 * 
		 *
		 * @return MMarkupElementView
		 */
		public function __construct() {
			parent::__construct();
			
			$this->element = null;
			$this->text = null;
			$this->properties = new MMutableDictionary();
			$this->indentsCode = true;
			$this->indentLevel = 0;
			$this->shouldAppendEmptyLine = false;
		}
		
		/******************** Properties ********************/
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setElement(MString $element) {
			$this->element = $element;
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function element() {
			return $this->element;
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setText(MString $text = null) {
			$this->text = $text;
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function text() {
			return $this->text;
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setValueForProperty(MString $name, MString $value = null) {
			if ($value) {
				$this->properties->setObjectForKey($name, $value);
			} else {
				$this->properties->removeObjectForKey($name);
			}
		}
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function valueForProperty(MString $name) {
			return $this->properties->objectForKey($name);
		}
		
		/**
		 * 
		 *
		 * @return MDictionary
		 */
		public function properties() {
			return $this->properties;
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function setIndentsCode($indentsCode) {
			MAssertTypes('bool', $indentsCode);
			$this->indentsCode = $indentsCode;
		}
		
		/**
		 * 
		 *
		 * @return bool
		 */
		public function indentsCode() {
			return $this->indentsCode;
		}
		
		/**
		 * 
		 *
		 * @return void
		 */
		public function indentLevel() {
			if ($this->superview()) {
				return $this->superview()->indentLevel() + ($this->superview()->indentsCode() ? 1 : 0);
			} else {
				return $this->indentLevel;
			}
		}
		
		/**
		 * @return bool
		 */
		public function shouldAppendEmptyLine() {
			return $this->shouldAppendEmptyLine;
		}
		
		/**
		 * @return void
		 */
		public function setShouldAppendEmptyLine($shouldAppendEmptyLine) {
			MAssertTypes('bool', $shouldAppendEmptyLine);
			$this->shouldAppendEmptyLine = $shouldAppendEmptyLine;
		}
		
		/******************** Methods ********************/
		
		/**
		 * @return MArray
		 */
		public function subviewsWithPropertyAndValue(MString $property, MString $value) {
			$views = new MMutableArray();
			
			foreach ($this->subviews()->toArray() as $subview) {
				if ($subview instanceof MMarkupElementView) {
					if ($subview->valueForProperty($property)->equals($value)) {
						$views->addObject($subview);
					}
				}
			}
			return $views;
		}
		
		/******************** MObject Methods ********************/
		
		/**
		 * 
		 *
		 * @return MString
		 */
		public function toString() {
			if ($this->element()) {
				$markup = new MMutableString();
				
				$indentString = MString::stringWithRepeatingString(S(" "), $this->indentLevel());
				
				$properties = new MMutableArray();
				foreach ($this->properties()->allKeys()->toArray() as $name) {
					$value = $this->properties()->objectForKey($name);
					$properties->addObject(Sf("%s=\"%s\"", $name, $value));
				}
				
				if ($properties->count() > 0) {
					$markup->appendFormat("%s<%s %s", $indentString, $this->element(), $properties->componentsJoinedByString(S(" ")));
				} else {
					$markup->appendFormat("%s<%s", $indentString, $this->element());
				}
				
				if ($this->text()) {
					$markup->appendFormat(">%s</%s>", $this->text()->stringByEncodingHTMLEntities(), $this->element());
				} else if ($this->subviews()->count() > 0) {
					$markup->appendLine(S(">"));
					$markup->appendLine(parent::toString());
					$markup->appendString(Sf("%s</%s>", $indentString, $this->element()));
				} else {
					$markup->appendString(S("/>"));
				}
				
				if ($this->shouldAppendEmptyLine()) {
					$markup->appendLine();
				}
				
				return $markup;
			} else {
				return parent::toString();
			}
		}
		
	}

?>