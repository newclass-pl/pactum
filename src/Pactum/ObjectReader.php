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
 * Source reader.
 *
 * @author Michal Tomczak (michal.tomczak@itephp.com)
 */
interface ObjectReader{

    /**
     * @param string $name
     * @return ArrayReader
     */
	public function getArray($name);

    /**
     * @param string $name
     * @return ObjectReader
     */
    public function getObject($name);

    /**
     * @param string $name
     * @return mixed
     */
    public function getValue($name);

}