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
 * Class InvalidValueException
 * @package Pactum
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class InvalidValueException extends ConfigException
{

    /**
     * InvalidValueException constructor.
     * @param string $name
     * @param mixed $value
     * @param string $type
     */
    public function __construct($name,$value, $type)
    {
        parent::__construct('Value '.$name.'="'.$value.'"  is not '.$type.'.');
    }
}