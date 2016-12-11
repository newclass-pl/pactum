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

        $this->assertEquals('1', $container->getValue('val1'));
        $this->assertEquals('two', $container->getValue('val2'));

        $this->assertEquals([
            '11',
            '12',
        ], $container->getArray('arr1'));
        $this->assertEquals([
            '21',
            '22',
        ], $container->getArray('arr2'));

        $obj1=$container->getObject('obj1');
        $this->assertEquals('1', $obj1->getValue('1o_val1'));
        $this->assertEquals('2', $obj1->getValue('1o_val2'));

        $obj2=$container->getObject('obj2');
        $this->assertEquals('1', $obj2->getValue('2o_val1'));
        $this->assertEquals('2', $obj2->getValue('2o_val2'));

    }

    /**
     *
     */
    public function testGetValueConfigException()
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessageRegExp('/Value "unknown" not found./');

        $container = new ConfigContainer([],[],[]);
        $container->getValue('unknown');

    }

    public function testGetObjectConfigException()
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessageRegExp('/Object "unknown" not found./');

        $container = new ConfigContainer([],[],[]);
        $container->getObject('unknown');

    }

    public function testGetArrayConfigException()
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessageRegExp('/Array "unknown" not found./');

        $container = new ConfigContainer([],[],[]);
        $container->getArray('unknown');

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

    /**
     *
     */
    public function testGetConfig()
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
        $values['val part'] = 'part';

        $container = new ConfigContainer($objects, $arrays, $values);

        $data=$container->getConfig();
        $this->assertEquals('1',$data->getVal1());
        $this->assertEquals('two',$data->getVal2());
        $this->assertEquals('part',$data->getValPart());
        $obj1=$data->getObj1();
        $this->assertEquals('1',$obj1->get1oVal1());
        $this->assertEquals('2',$obj1->get1oVal2());
        $this->assertEquals(['11','12'],$data->getArr1());

    }

    private function getObj($index)
    {
        $values = [
            $index . 'o_val1' => 1,
            $index . 'o_val2' => 2,
        ];
        $container = new ConfigContainer([], [], $values);
        return $container;
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