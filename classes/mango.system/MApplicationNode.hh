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
 * 
 *
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */
class MApplicationNode extends MObject {
	
	protected MString $_name;
	protected ?MApplicationNode $_parentNode;
	protected MMutableArray $_childNodes;
	
	/**
	 * 
	 * @return MApplicationNode
	 */
	public function __construct(?MString $name = null) {
		parent::__construct();
		
		$this->_name = ($name !== null ? $name : S(""));
		$this->_parentNode = null;
		$this->_childNodes = new MMutableArray();
	}
	
	/******************** Properties ********************/
	
	/**
	 * @return MString
	 */
	public function name() : MString {
		return $this->_name;
	}
	
	/**
	 * @return MApplicationNode
	 */
	public function parentNode() : MApplicationNode {
		return $this->_parentNode;
	}
	
	/******************** Protected ********************/
	
	/**
	 * @return void
	 */
	protected function setParentNode(?MApplicationNode $parentNode = null) : void {
		$this->_parentNode = $parentNode;
	}
	
	/******************** Properties ********************/
	
	/**
	 * @return void
	 */
	public function addChildNode(MApplicationNode $node) : void {
		$this->_childNodes->addObject($node);
		$node->setParentNode($this);
	}
	
	/**
	 * @return void
	 */
	public function removeChildNode(MApplicationNode $node) : void {
		if ($this->_childNodes->removeObject($node)) {
			$node->setParentNode(null);
		}
	}
	
	/**
	 * @return void
	 */
	public function removeAllChildNodes() : void {
		$this->_childNodes->removeAllObjects();
	}
	
	/**
	 * @return MArray
	 */
	public function childNodes() : MArray {
		return (MArray)$this->_childNodes;
	}
	
	/**
	 * @return MApplicationNode
	 */
	public function childNodeWithName(MString $name) : ?MApplicationNode {
		foreach ($this->childNodes()->toArray() as $node) {
			if ($node->name()->equals($name)) {
				return $node;
			}
		}
		return null;
	}
	
	/**
	 * @return bool
	 */
	public function hasChildNodes() : bool {
		return ($this->childNodes()->count() > 0);
	}
	
	/**
	 * @return MViewController
	 */
	public function viewControllerForPath(MArray $path) : ?MViewController {
		return null;
	}
	
}

