<?php
/**
 * Pactum: Config manager
 * Copyright (c) NewClass (http://newclass.pl)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the file LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) NewClass (http://newclass.pl)
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */


namespace Pactum;

/**
 * Interface ArrayReader
 * @package Pactum
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
interface ArrayReader extends ObjectReader
{


    /**
     * @return mixed[]
     */
    public function getValues();

    /**
     * @return ObjectReader[]
     */
    public function getObjects();

    /**
     * @return ArrayReader[]
     */
    public function getArrays();

}