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
class MMutableArray<T> extends MArray<T> {
	
	public function __construct(?Traversable<T> $t = null) {
		parent::__construct($t);
	}
	
	/******************** Methods ********************/
	
	public function addObject(T $object) : void {
		$this->_vector->add($object);
	}
	
	public function addObjects(MArray<T> $objects) : void {
		foreach ($objects->traversable() as $object) {
			$this->addObject($object);
		}
	}

	public function removeObject(T $object) : bool {
		$index = $this->indexOfObject($object);
		if ($index != MArray::ObjectNotFound) {
			$this->removeObjectAtIndex($index);
			return true;
		} else {
			return false;
		}
	}

	public function removeObjects(MArray<T> $objects) : void {
		foreach ($objects->traversable() as $object) {
			$this->removeObject($object);
		}
	}

	public function removeObjectAtIndex(int $index) : void {
		if ($index < $this->lowerBound() || $index > $this->upperBound()) {
			throw new MIndexOutOfBoundsException($index, $this->lowerBound(), $this->upperBound());
		}
		$this->_vector->removeKey($index);
	}
	
	public function removeAllObjects() : void {
		$this->_vector->clear();
	}

}

