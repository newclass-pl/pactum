<?php
/**
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Michal Tomczak <michal.tomczak@newaxis.pl>
 *
 * @copyright     Copyright (c) Newaxis (http://newaxis.pl)
 * @link          https://cogitary-polisy.aria.pl
 * @license       http://www.binpress.com/license/view/l/b0e782df3e50d424a32d613af2c4937b
 */


namespace Pactum;


class InvalidValueException extends ConfigException
{

    /**
     * InvalidValueException constructor.
     * @param mixed $value
     * @param string $type
     */
    public function __construct($value, $type)
    {
        parent::__construct('Value "'.$value.'" is not '.$type.'.');
    }
}