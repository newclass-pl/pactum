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

use Pactum\ConfigBuilder;
use Pactum\ConfigBuilderObject;
use Pactum\ConfigBuilderValue;
use Pactum\ConfigContainer;
use Pactum\ElementNotFoundException;
use Pactum\InvalidNumberElementException;
use Pactum\ParserProcess;
use Pactum\Reader\JSONReader;
use Pactum\Reader\XMLReader;

/**
 * Class ConfigBuilderTest
 * @package Test\Pactum
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class ConfigBuilderTest extends \PHPUnit_Framework_TestCase{

    /**
     * @var ConfigBuilder
     */
    private $config;

    /**
     *
     */
    public function setUp()
    {
        $this->config=new ConfigBuilder();
        $this->config->addBoolean("booleanTrue")
            ->addBoolean("booleanFalse")
            ->addNumber("number1")
            ->addNumber("number2")
            ->addString("text")
            ->addString("other")
            ->addMixed("v_mixed1")
            ->addMixed("v_mixed2")
            ->addArray("d_array",new ConfigBuilderObject(),1,4)
            ->getValue()->addString("test");

        $this->config->addArray("v_array",new ConfigBuilderValue('number'),4);
        $this->config->addObject("v_object")
            ->addString("v_text")
            ->addNumber("number",1233);
//        $this->config->addArray("k_array",new ConfigBuilderArray(new ConfigBuilderObject()))
//            ->getValue()->getValue()->addString('k_array_var');

        $this->config->addArray("n_array",new ConfigBuilderValue('number'));

        $this->config->addObject("not_required",false)
            ->addNumber("num");

    }

    /**
     *
     */
    public function testParseXML(){

        $path=realpath(__DIR__.'/..');

        $xmlReader=new XMLReader($path.'/Asset/example1.xml');
        $this->config->addReader($xmlReader);

        $xmlReader=new XMLReader($path.'/Asset/example2.xml');
        $this->config->addReader($xmlReader);


        $container=$this->config->getContainer();
        $this->checkAssert($container);

    }

    public function testParseXMLInvalidNumberElementException(){

        $path=realpath(__DIR__.'/..');
        $this->config=new ConfigBuilder();
        $this->config->addArray('list',new ConfigBuilderValue('number'),3);

        $xmlReader=new XMLReader($path.'/Asset/example3.xml');
        $this->config->addReader($xmlReader);
        $exception=$this->getException($this->config);
        $this->assertNotNull($exception);
        $this->assertEquals(InvalidNumberElementException::class,get_class($exception));
        $this->assertEquals('Invalid number element "root->list". Required 3, received 2.',$exception->getMessage());

        $this->config=new ConfigBuilder();
        $this->config->addArray('list',new ConfigBuilderValue('number'),0,1);

        $xmlReader=new XMLReader($path.'/Asset/example3.xml');
        $this->config->addReader($xmlReader);
        $exception=$this->getException($this->config);
        $this->assertNotNull($exception);
        $this->assertEquals(InvalidNumberElementException::class,get_class($exception));
        $this->assertEquals('Invalid number element "root->list". Required 1, received 2.',$exception->getMessage());

    }

    public function testParseJSONInvalidNumberElementException(){

        $path=realpath(__DIR__.'/..');
        $this->config=new ConfigBuilder();
        $this->config->addArray('list',new ConfigBuilderValue('number'),3);

        $xmlReader=new JSONReader($path.'/Asset/example3.json');
        $this->config->addReader($xmlReader);
        $exception=$this->getException($this->config);
        $this->assertNotNull($exception);
        $this->assertEquals(InvalidNumberElementException::class,get_class($exception));
        $this->assertEquals('Invalid number element "root->list". Required 3, received 2.',$exception->getMessage());

        $this->config=new ConfigBuilder();
        $this->config->addArray('list',new ConfigBuilderValue('number'),0,1);

        $xmlReader=new XMLReader($path.'/Asset/example3.xml');
        $this->config->addReader($xmlReader);
        $exception=$this->getException($this->config);
        $this->assertNotNull($exception);
        $this->assertEquals(InvalidNumberElementException::class,get_class($exception));
        $this->assertEquals('Invalid number element "root->list". Required 1, received 2.',$exception->getMessage());

    }

    public function testParseJSONElementNotFoundException(){

        $path=realpath(__DIR__.'/..');
        $this->config=new ConfigBuilder();
        $this->config->addObject('element');

        $xmlReader=new JSONReader($path.'/Asset/example3.json');
        $this->config->addReader($xmlReader);
        $exception=$this->getException($this->config);
        $this->assertNotNull($exception);
        $this->assertInstanceOf(ElementNotFoundException::class,$exception);
        $this->assertEquals('Element "element" not found in "root".',$exception->getMessage());


    }


    /**
     * @param ConfigContainer $container
     */
    private function checkAssert(ConfigContainer $container){
        $this->assertEquals(true,$container->getData('booleanTrue'));
        $this->assertEquals(false,$container->getData('booleanFalse'));
        $this->assertEquals(1,$container->getData('number1'));
        $this->assertEquals(0.65,$container->getData('number2'));
        $this->assertEquals('value text',$container->getData('text'));
        $this->assertEquals('test',$container->getData('v_mixed1'));
        $this->assertEquals(true,$container->getData('v_mixed2'));
        $array=$container->getData("d_array");
        $this->assertCount(2,$array);
        /**
         * @var ConfigContainer $obj;
         */
        $obj=$array[0];
        $this->assertEquals('wdwd',$obj->getData('test'));
        $obj=$array[1];
        $this->assertEquals('next',$obj->getData('test'));

        $array=$container->getData('v_array');
        $this->assertCount(6,$array);
        $this->assertEquals([1,2,3,4,5,6],$array);

        /**
         * @var ConfigContainer $object
         */
        $object=$container->getData('v_object');
        $this->assertEquals('data',$object->getData('v_text'));
        $this->assertEquals(1233,$object->getData('number'));
        $this->assertEquals([],$container->getData('n_array'));

        $this->assertNull($container->getData('not_required'));
        //        $array=$container->getArray("k_array");
//        $this->assertCount(2,$array);
//        $this->assertEquals('set',$array[0][0]->getValue('k_array_var'));
//        $this->assertEquals('next',$array[1][0]->getValue('k_array_var'));

    }

    /**
     *
     */
    public function testParseJSON(){

        $path=realpath(__DIR__.'/..');

        $xmlReader=new JSONReader($path.'/Asset/example1.json');
        $this->config->addReader($xmlReader);
        $xmlReader=new JSONReader($path.'/Asset/example2.json');
        $this->config->addReader($xmlReader);

        $container=$this->config->getContainer();

        $this->checkAssert($container);

    }

    /**
     *
     */
    public function testFilterVariable(){

        $path=realpath(__DIR__.'/..');
        $config=new ConfigBuilder();
        $variables=[];
        $config->addFilter(function (ParserProcess $parser) use (&$variables){

            if($parser->getPath()==='root->variable'){
                /**
                 * @var ConfigBuilderObject $value
                 */
                foreach($parser->getValue() as $value){
                    $variables[$value['name']]=$value['value'];
                }
                return;
            }
        });

        $config->addFilter(function (ParserProcess $parser) use(&$variables){
            if(in_array($parser->getType(),['string','boolean','mixed','number']) && is_string($parser->getValue())){

                $value=$parser->getValue();
                preg_match_all('/@\{(.+?)\}/',$parser->getValue(),$matches);
                for($i=0; $i<count($matches[0]); $i++){
                    if(is_string($variables[$matches[1][$i]]) && $value!==$matches[0][$i]){
                        $value=str_replace($matches[0][$i],$variables[$matches[1][$i]],$value);
                        continue;
                    }
                    $value=$variables[$matches[1][$i]];

                }
                $parser->setValue($value);

            }
        });

        $config
            ->addArray("variable",new ConfigBuilderObject())->getValue()
            ->addString('name')
            ->addMixed('value');

        $config
            ->addArray("data",new ConfigBuilderValue('number'));


        $config->addString('var1')
            ->addBoolean('var2');

        $xmlReader=new JSONReader($path.'/Asset/example4.json');
        $config->addReader($xmlReader);

        $container=$config->getContainer();
        $this->assertEquals('string data',$container->getData('var1'));
        $this->assertEquals(true,$container->getData('var2'));
        $this->assertEquals([1234,3],$container->getData('data'));

    }

    /**
     *
     */
    public function testFilterImport(){

        $path=realpath(__DIR__.'/..');
        $config=new ConfigBuilder();
        $config->addFilter(function (ParserProcess $parser) use ($path){

            if($parser->getType()==='string' && $parser->getPath()==='root->import'){
                $record=$parser->getValue();
                $importReader=new JSONReader($path.'/Asset/'.$record);
                $parser->addReader($importReader);

                return;
            }
        });

        $config
            ->addArray("import",new ConfigBuilderValue("string"));

        $config
            ->addArray("data",new ConfigBuilderValue('number'));


        $config->addString('var1')
            ->addString('var2');
        $config->addString('var3');

        $xmlReader=new JSONReader($path.'/Asset/example5.json');
        $config->addReader($xmlReader);

        $container=$config->getContainer();
        $this->assertEquals('1',$container->getData('var1'));
        $this->assertEquals('2',$container->getData('var2'));
        $this->assertEquals('3',$container->getData('var3'));

    }

    /**
     *
     */
    public function testGetClass(){

        $path=realpath(__DIR__.'/..');

        $xmlReader=new XMLReader($path.'/Asset/example1.xml');
        $this->config->addReader($xmlReader);

        $xmlReader=new XMLReader($path.'/Asset/example2.xml');
        $this->config->addReader($xmlReader);

        $obj=$this->config->getClass(sys_get_temp_dir().'/Tmp','Tmp');
        $this->assertEquals(true ,$obj->isBooleanTrue());
        $this->assertEquals(false ,$obj->isBooleanFalse());
        $this->assertEquals(1 ,$obj->getNumber1());
        $this->assertEquals(0.65 ,$obj->getNumber2());
        $this->assertEquals('value text' ,$obj->getText());
        $this->assertEquals([1,2,3,4,5,6] ,$obj->getVArray());
        /**
         * @var object[] $dArray
         */
        $dArray=$obj->getDArray();
        $this->assertCount(2,$dArray);
        $this->assertEquals('wdwd' ,$dArray[0]->getTest());
        $this->assertEquals('next' ,$dArray[1]->getTest());

        $this->assertEquals('data' ,$obj->getVObject()->getVText());
        $this->assertEquals('file' ,$obj->getOther());
        $this->assertEquals('test' ,$obj->getVMixed1());
        $this->assertEquals(true ,$obj->getVMixed2());


    }
    /**
     * @param ConfigBuilder $config
     * @return \Exception|null
     */
    private function getException($config){
        $exception=null;

        try{
            $config->getContainer();
        }
        catch(\Exception $e){
            $exception=$e;
        }

        return $exception;
    }

}