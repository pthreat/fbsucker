<?php 

	require __DIR__.'/../autoload.php';

	use \stange\fbsucker\cache\adapter\File	as	FileCache;

	try{

		$cache	=	new FileCache(['entryPoint'=>'cache']);
		$cache->save('test','cache');

	}catch(\Exception $e){

		echo $e->getMessage()."\n";

	}
