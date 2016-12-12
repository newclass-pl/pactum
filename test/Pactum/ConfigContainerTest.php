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

namespace Test\Pactum;


use Pactum\ConfigContainer;
use Pactum\ConfigException;

/**
 * Class ConfigBuilderValueTest
 * @package Test\Pactum
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class ConfigContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testConstruct()
    {
        $data=[];
        $data['obj1'] = $this->getObj(1);
        $data['obj2'] = $this->getObj(2);
        $data['arr1'] = $this->getArr(1);
        $data['arr2'] = $this->getArr(2);
        $data['val1'] = '1';
        $data['val2'] = 'two';

        $container = new ConfigContainer($data);

        $this->assertEquals('1', $container->getData('val1'));
        $this->assertEquals('two', $container->getData('val2'));

        $this->assertEquals([
            '11',
            '12',
        ], $container->getData('arr1'));
        $this->assertEquals([
            '21',
            '22',
        ], $container->getData('arr2'));

        /**
         * @var ConfigContainer $obj1
         */
        $obj1=$container->getData('obj1');
        $this->assertEquals('1', $obj1->getData('1o_val1'));
        $this->assertEquals('2', $obj1->getData('1o_val2'));

        /**
         * @var ConfigContainer $obj2
         */
        $obj2=$container->getData('obj2');
        $this->assertEquals('1', $obj2->getData('2o_val1'));
        $this->assertEquals('2', $obj2->getData('2o_val2'));

    }

    /**
     *
     */
    public function testGetDataConfigException()
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessageRegExp('/Value "unknown" not found./');

        $container = new ConfigContainer([]);
        $container->getData('unknown');

    }

    /**
     *
     */
    public function testSerialize()
    {
        $objects = [];
        $arrays = [];
        $values = [];
        $objects['obj1'] = $this->getObj(1);
        $objects['obj2'] = $this->getObj(2);
        $arrays['arr1'] = $this->getArr(1);
        $arrays['arr2'] = $this->getArr(2);
        $values['val1'] = '1';
        $values['val2'] = 'two';

        $container = new ConfigContainer($objects, $arrays, $values);

        $data=$container->serialize();
        $cloneObj=unserialize($data);
        $this->assertEquals($cloneObj,$container);

    }

    private function getObj($index)
    {
        $values = [
            $index . 'o_val1' => 1,
            $index . 'o_val2' => 2,
        ];
        return $values;
    }

    private function getArr($index)
    {
        $values = [
            $index . '1',
            $index . '2',
        ];
        return $values;
    }

}