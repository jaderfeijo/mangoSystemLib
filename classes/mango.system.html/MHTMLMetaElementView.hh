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
class MHTMLMetaElementView extends MHTMLElementView {
	
	public function __construct(?MString $name = null, ?MString $content = null) {
		parent::__construct(S("meta"));
		$this->setName($name);
		$this->setContent($content);
	}
	
	/******************** Properties ********************/
	
	public function name() : ?MString {
		return $this->valueForProperty(S("name"));
	}
	
	public function setName(?MString $name) : void {
		$this->setValueForProperty(S("name"), $name);
	}
	
	public function property() : ?MString {
		return $this->valueForProperty(S("property"));
	}
	
	public function setProperty(?MString $property) : void {
		$this->setValueForProperty(S("property"), $property);
	}
	
	public function content() : ?MString {
		return $this->valueForProperty(S("content"));
	}
	
	public function setContent(?MString $content) : void {
		$this->setValueForProperty(S("content"), $content);
	}
	
	public function charset() : ?MString {
		return $this->valueForProperty(S("charset"));
	}
	
	public function setCharset(?MString $charset) : void {
		$this->setValueForProperty(S("charset"), $charset);
	}
	
	public function httpEquiv() : ?MString {
		return $this->valueForProperty(S("http-equiv"));
	}
	
	public function setHttpEquiv(?MString $httpEquiv) : void {
		$this->setValueForProperty(S("http-equiv"), $httpEquiv);
	}
	
	public function scheme() : ?MString {
		return $this->valueForProperty(S("scheme"));
	}
	
	public function setScheme(?MString $scheme) : void {
		$this->setValueForProperty(S("scheme"), $scheme);
	}
	
}

