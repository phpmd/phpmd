<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Licensed under BSD License
 * For full copyright and license information, please see the LICENSE file.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @link http://phpmd.org/
 */

class testReportContainsCouplingBetweenObjectsWarning
{
    /**
     * addParameter
     *
     * @param \ReflectionParameter $p
     * @return void
     * @throws \OutOfBoundsException
     */
    public function addParameter(\ReflectionParameter $p)
    {

    }

    /**
     * setClass
     *
     * @param \ReflectionClass $c
     * @return void
     * @throws \OutOfBoundsException
     */
    public function setClass(\ReflectionClass $c)
    {

    }

    /**
     * setParentClass
     *
     * @param \ReflectionClass $c
     * @return void
     * @throws \OutOfBoundsException
     */
    public function setParentClass(\ReflectionClass $c)
    {

    }

    /**
     * addProperty
     *
     * @param \ReflectionProperty $p
     * @return void
     * @throws \OutOfBoundsException
     */
    public function addProperty(\ReflectionProperty $p)
    {

    }

    /**
     * traverse
     *
     * @param DOMNode $node
     * @return void
     * @throws \InvalidArgumentException
     */
    public function traverse(\DOMNode $node = null)
    {

    }

    /**
     * setStorage
     *
     * @param \SplObjectStorage $storage
     * @return void
     */
    public function setStorage(\SplObjectStorage $storage)
    {

    }

    /**
     * getIterator
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator([]);
    }

    /**
     * getElement
     *
     * @return DOMElement
     */
    public function getElement()
    {
    }

    /**
     * getOwnerDocument
     *
     * @return DOMDocument
     */
    public function getOwnerDocument()
    {
    }

    /**
     * find
     *
     * @param DOMXPath $xpath
     * @return DOMNodeList
     */
    public function find(DOMXPath $xpath)
    {
        return $xpath->query('');
    }

    public function fail()
    {
        throw new \ErrorException();
    }
}
