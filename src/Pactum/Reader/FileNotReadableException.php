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


namespace Pactum\Reader;


use Pactum\ConfigException;

/**
 * Class FileNotReadableException
 * @package Pactum\Reader
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class FileNotReadableException extends ConfigException
{

    /**
     * FileNotReadableException constructor.
     * @param string $file
     */
    public function __construct($file)
    {
        parent::__construct('File "'.$file.'" not readable.');
    }
}