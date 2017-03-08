<?php

spl_autoload_register(function ($class) {

	// project-specific namespace prefix
	$ns		= 'stange';
	$class	=	trim($class,'\\');
	$pos		=	strpos($class,'stange\\fbsucker');

	if($pos===FALSE){

		return;

	}

	$baseDir	=	sprintf('%s%s%s',__DIR__,DIRECTORY_SEPARATOR,'src');

	$class		=	substr($class,strpos($class,'\\')+1);
	$class		=	preg_replace('/\\\\/',DIRECTORY_SEPARATOR,$class);
	$class		=	substr($class,strpos($class,DIRECTORY_SEPARATOR)+1);

	$file		=	sprintf ('%s/%s.class.php',$baseDir,$class);

	if (!file_exists($file)){

		$msg	=	"Autoloader error: File $file was not found";

		throw new \RuntimeException($msg);

	}

	if(!is_readable($file)){

		$msg	=	"Autoloader error: File $file is not readable, check file  permissions";
		throw new \RuntimeException($msg);

	}

	require $file;

});
