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


namespace Pactum\Cache;

/**
 * Class PrimitiveBuilder
 * @package Pactum\Cache
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class PrimitiveCache extends AbstractCache
{

    /**
     * @return string
     */
    public function generateDefinition()
    {
        $key = $this->filterName($this->key);
        $fieldName = lcfirst($key);

        $template = "\$this->_" . $fieldName . " = " . $this->filterValue($this->value) . ";\n";
        return $template;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function filterValue($value)
    {
        if ($value === 'null') {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_string($value)) {
            return '\'' . str_replace([
                    '\\',
                    '\''
                ], [
                    '\\\\',
                    '\\\''
                ], $value) . '\'';
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getReturnType()
    {
        if ($this->value === null) {
            return 'null';
        }

        if (is_string($this->value)) {
            return 'string';
        }

        if (is_bool($this->value)) {
            return 'bool';
        }

        if (is_float($this->value)) {
            return 'float';
        }

        if (is_int($this->value)) {
            return 'int';
        }

        return 'mixed';
    }
}