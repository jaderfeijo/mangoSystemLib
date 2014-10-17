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
abstract class MMarkupElementView extends MView {
	
	//
	// ************************************************************
	//
	
	protected ?MString $_element;
	protected ?MString $_text;
	protected MMutableDictionary $_properties;
	protected bool $_indentsCode;
	protected int $_indentLevel;
	protected bool $_shouldAppendEmptyLine;
	
	public function __construct() {
		parent::__construct();
		
		$this->_element = null;
		$this->_text = null;
		$this->_properties = new MMutableDictionary();
		$this->_indentsCode = true;
		$this->_indentLevel = 0;
		$this->_shouldAppendEmptyLine = false;
	}
	
	/******************** Properties ********************/
	
	public function setElement(MString $element) : void {
		$this->_element = $element;
	}
	
	public function element() : MString {
		return $this->_element;
	}
	
	public function setText(?MString $text = null) : void {
		$this->_text = $text;
	}
	
	public function text() : ?MString {
		return $this->_text;
	}
	
	public function setValueForProperty(MString $name, ?MString $value = null) : void {
		if ($value !== null) {
			$this->_properties->setObjectForKey($name, $value);
		} else {
			$this->_properties->removeObjectForKey($name);
		}
	}
	
	public function valueForProperty(MString $name) : MString {
		return $this->_properties->objectForKey($name);
	}
	
	public function properties() : MDictionary {
		return $this->_properties;
	}
	
	public function setIndentsCode(bool $indentsCode) : void {
		$this->_indentsCode = $indentsCode;
	}
	
	public function indentsCode() : bool {
		return $this->_indentsCode;
	}
	
	public function indentLevel() : void {
		if ($this->superview()) {
			return $this->superview()->indentLevel() + ($this->superview()->indentsCode() ? 1 : 0);
		} else {
			return $this->_indentLevel;
		}
	}
	
	public function shouldAppendEmptyLine() : bool {
		return $this->_shouldAppendEmptyLine;
	}
	
	public function setShouldAppendEmptyLine(bool $shouldAppendEmptyLine) : void {
		$this->_shouldAppendEmptyLine = $shouldAppendEmptyLine;
	}
	
	/******************** Methods ********************/
	
	public function subviewsWithPropertyAndValue(MString $property, MString $value) : MArray {
		$views = new MMutableArray();
		
		foreach ($this->subviews()->toArray() as $subview) {
			if ($subview instanceof MMarkupElementView) {
				if ($subview->valueForProperty($property)->equals($value)) {
					$views->addObject($subview);
				}
			}
		}
		return (MArray)$views;
	}
	
	/******************** MObject Methods ********************/
	
	public function toString() : MString {
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
			
			return (MString)$markup;
		} else {
			return parent::toString();
		}
	}
	
}

