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
class MHTMLErrorDocumentView extends MHTMLDocumentView {
	
	protected MInteger $_errorCode;
	protected MString $_errorName;
	protected MString $_errorMessage;
	protected MHTMLHeaderElementView $_errorCodeHeaderView;
	protected MHTMLParagraphView $_errorDescriptionParagraphView;
	
	public function __construct(MInteger $errorCode, MString $errorName, MString $errorMessage) {
		parent::__construct();

		$this->_errorCode = $errorCode;
		$this->_errorName = $errorName;
		$this->_errorMessage = $errorMessage;
		$this->_errorCodeHeaderView = new MHTMLHeaderElementView();
		$this->_errorDescriptionParagraphView = new MHTMLParagraphView();

		$this->addSubview($this->errorCodeHeaderView());
		$this->addSubview($this->errorDescriptionParagraphView());
		
		$this->updateView();
	}
	
	/******************** Protected ********************/
	
	protected function errorCodeHeaderView() : MHTMLHeaderElementView {
		return $this->_errorCodeHeaderView;
	}

	protected function errorDescriptionParagraphView() : MHTMLParagraphView {
		return $this->_errorDescriptionParagraphView;
	}

	protected function updateView() : void {
		$this->title()->setText(Sf("%s - %s", $this->errorCode()->toString(), $this->errorName()));
		$this->errorCodeHeaderView()->setText($this->errorCode()->toString());
		$this->errorDescriptionParagraphView()->setText($this->errorMessage());
	}
	
	/******************** Properties ********************/
	
	public function setErrorCode(MInteger $errorCode) : void {
		$this->_errorCode = $errorCode;
		$this->updateView();
	}
	
	public function errorCode() : MInteger {
		return $this->_errorCode;
	}
	
	public function setErrorName(MString $errorName) : void {
		$this->_errorName = $errorName;
		$this->updateView();
	}
	
	public function errorName() : MString {
		return $this->_errorName;
	}
	
	public function setErrorMessage(MString $errorMessage) : void {
		$this->_errorMessage = $errorMessage;
		$this->updateView();
	}
	
	public function errorMessage() : MString {
		return $this->_errorMessage;
	}
	
}

