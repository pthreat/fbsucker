<?php 

	$autoload	=	realpath( __DIR__.'/../../../autoload.php');
	$composer	=	realpath( __DIR__.'/../../../vendor/autoload.php');
	require $autoload;
	require $composer;

	use \stange\fbsucker\cache\adapter\File	as	FileCache;
	use \stange\logging\Slog;

	try{

		$cache	=	new FileCache([
												'entry'	=>	'cache',
												'log'		=>	new Slog([
																				'echo'	=>	TRUE,
																				'level'	=>	1
												])
		]);

		$cache->sload('test','cache');

	}catch(\Exception $e){

		echo $e->getMessage()."\n";

	}
