<?php 

	echo realpath( __DIR__.'/../../../autoload.php');
	die();

	use \stange\fbsucker\cache\adapter\File	as	FileCache;
	use \stange\logging\Slog;

	$a	=	new Slog();

	die();

	try{

		$cache	=	new FileCache([
												'entryPoint'=>	'cache',
												'log'			=>	new Slog()
		]);

		$cache->sload('test','cache');

	}catch(\Exception $e){

		echo $e->getMessage()."\n";

	}
