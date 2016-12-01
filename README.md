README
======

![license](https://img.shields.io/packagist/l/bafs/via.svg?style=flat-square)
![PHP 5.4+](https://img.shields.io/badge/PHP-5.4+-brightgreen.svg?style=flat-square)

What is Pactum?
-----------------

Pactum is a PHP config manager. Support multi file types.

Installation
------------

The best way to install is to use the composer by command:

composer require newclass/pactum

composer install

Use example
-------------
    use Pactum\ConfigBuilder;
    use Pactum\ConfigBuilderObject;
    use Pactum\Reader\JSONReader;

    $config=new ConfigBuilder();

    //set config structure
    $config->addBoolean("booleanTrue")
        ->addNumber("number1")
        ->addString("text")
        ->addString("other","default")
        ->addArray("d_array",new ConfigBuilderObject())
        ->getValue()->addString("test");

    //add json reader
    $xmlReader=new JSONReader('{"booleanTrue":true,"number1":1,"text":"value text","d_array":[{"test":"wdwd"}]}','s');
    $config->addReader($xmlReader);
    //parse data and generate container
    $container=$config->parse();
    
    //get value from config data
    $valueBoolean=$container->getValue('booleanTrue');
    $valueArray=$container->getArray("d_array");
    