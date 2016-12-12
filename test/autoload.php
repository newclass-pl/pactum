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


$vendors=[
    'Pactum'=>realpath(__DIR__.'/..').'/src',
    'Tmp'=>sys_get_temp_dir(),
];

spl_autoload_register(function ($className) use ($vendors){
    $classPath=str_replace('\\', '/', $className);
    if(strpos($classPath, 'Test')===0){
        $classPath=realpath(__DIR__.'/..').'/test/'.substr($classPath, 5);
    }
    else{
        foreach($vendors as $kVendor=>$vendor){
            if(strpos($classPath, $kVendor)===0){
                $classPath=$vendor.'/'.$classPath;
                break;
            }

        }
    }
    $classPath=$classPath.'.php';
    if(!file_exists($classPath)){
        return;
    }
    /** @noinspection PhpIncludeInspection */
    require_once $classPath;
});