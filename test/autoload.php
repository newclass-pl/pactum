<?php
spl_autoload_register(function ($className) {
    $path=realpath(__DIR__.'/..');
	$classPath=str_replace('\\', '/', $className);
	if(strpos($classPath, 'Test')===0){
		$classPath='test/'.substr($classPath, 4);
	}
	else{
		$classPath='src/'.$classPath;
	}

	$classPath=$path.'/'.$classPath.'.php';
    if(!file_exists($classPath)){
        return;
    }
    /** @noinspection PhpIncludeInspection */
    require_once $classPath;
});