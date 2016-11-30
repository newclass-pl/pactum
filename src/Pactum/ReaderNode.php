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
 * File reader.
 *
 * @author Michal Tomczak (michal.tomczak@itephp.com)
 */
interface ReaderNode extends Reader{

	/**
	 *
	 * @param string $name
	 * @return string
	 * @throws ConfigException
	 */
	public function getAttribute($name);
	
}