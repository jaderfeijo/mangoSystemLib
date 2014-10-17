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
class MView extends MObject {
	
	//
	// ************************************************************
	//
	
	protected MMutableArray $_subviews;
	protected ?MView $_superview;
	protected MString $_contentType;
	
	/**
	 * 
	 *
	 * @return MView
	 */
	public function __construct() {
		parent::__construct();
		$this->_subviews = new MMutableArray();
		$this->_superview = null;
		$this->_contentType = S("text/plain");
	}
	
	/******************** Properties ********************/
	
	public function subviews() : MArray {
		return $this->_subviews;
	}
	
	public function setSuperview(?MView $superview = null) : void {
		$this->_superview = $superview;
	}
	
	public function superview() : MView {
		return $this->_superview;
	}
	
	public function setContentType(MString $contentType) : void {
		$this->_contentType = $contentType;
	}
	
	public function contentType() : MString {
		return $this->_contentType;
	}
	
	/******************** Methods ********************/
	
	public function addSubview(MView $view) : void {
		$this->_subviews->addObject($view);
		$view->setSuperview($this);
	}
	
	public function removeSubview(MView $view) : void {
		$this->_subviews->removeObject($view);
		$view->setSuperview(null);
	}
	
	public function removeAllSubviews() : void {
		$this->_subviews->removeAllObjects();
	}
			
	/******************** MObject Methods ********************/
	
	public function toString() : MString {
		$str = new MMutableString();
		foreach ($this->subviews()->toArray() as $view) {
			if (!$this->subviews()->isLastObject($view)) {
				$str->appendLine($view->toString());
			} else {
				$str->appendString($view->toString());
			}
		}
		return (MString)$str;
	}
	
}

