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
class MApplicationNamespace extends MApplicationNode {

	/**
	 * @param $namespaceElement
	 *
	 * @return MApplicationNamespace
	 */
	public static function parseFromXMLElement(SimpleXMLElement $namespaceElement) : MApplicationNamespace {
		$namespace = MInitWith($namespaceElement['name'], $namespaceName ==> {
			return new MApplicationNamespace(S($namespaceName));
		}, () ==> {
			return new MApplicationNamespace(S(""));
		});
		
		foreach ($namespaceElement as $element) {
			if ($element->getName() == "controller") {
				$namespace->addChildNode(MApplicationController::parseFromXMLElement($element));
			} else if ($element->getName() == "namespace") {
				$namespace->addChildNode(MApplicationNamespace::parseFromXMLElement($element));
			} else {
				throw new MParseErrorException(null, null, Sf("Unknown element '%s'", $element->getName()));
			}
		}
		
		return $namespace;
	}
	
	//
	// ************************************************************
	//
	
	/**
	 * @return MApplicationNamespace
	 */
	public function __construct(MString $name) {
		parent::__construct($name);
	}
	
	/******************** MApplicationNode ********************/
	
	/**
	 * @return MViewController
	 */
	public function viewControllerForPath(MArray<MString> $path) : ?MViewController {
		$viewController = null;
		
		$name = S("");
		if ($path->count() > 0) {
			$name = $path->objectAtIndex(0);
		}
		
		$subpath = new MArray();
		if ($path->count() > 1) {
			$subpath = $path->subarrayFromIndex(1);
		}
		
		$viewController = MInitWith($this->childNodeWithName($name), $node ==> {
			return $node->viewControllerForPath($subpath);
		}, () ==> {
			return null;
		});

		if ($viewController !== null) {
			return $viewController;
		} else {
			return parent::viewControllerForPath($path);
		}
	}
	
}

