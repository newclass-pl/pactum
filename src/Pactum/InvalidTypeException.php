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
 * Class InvalidTypeException
 * @package Pactum
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class InvalidTypeException extends ConfigException
{

    /**
     * InvalidTypeException constructor.
     * @param $type
     */
    public function __construct($type)
    {
        parent::__construct('Invalid type "'.$type.'".');
    }
}