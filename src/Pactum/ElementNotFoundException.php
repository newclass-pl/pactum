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


class ElementNotFoundException extends ConfigException
{

    /**
     * ElementNotFoundException constructor.
     * @param $containerPath
     * @param string $name
     */
    public function __construct($containerPath, $name)
    {
        parent::__construct('Element "'.$name.'" not found in "'.$containerPath.'"');
    }
}