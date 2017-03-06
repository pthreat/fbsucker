<?php

	/**
	 * File Cache Adapter
	 */

	namespace stange\fbsucker\cache\adapter{

		use \stange\fbsucker\cache\Adapter	as	AbstractCacheAdapter;

		class File extends AbstractCacheAdapter{

			public function __construct(Array $args=Array()){
		
				parent::__construct($args);

			}

			protected function __load($name){

				$file	=	"{$this->getEntryPoint()}/$name";

				if(!file_exists($file)){

					parent::setError("Cache file \"$file\" does not exists");
					return NULL;

				}

				if(!is_readable($file)){

					$msg	=	"Cache file \"$file\" is not readable, check file permissions";
					parent::setError($msg);

					return FALSE;

				}

				return file_get_contents($file);

			}

			private function createDirectory(){

				$ep	=	$this->getEntryPoint();

				if(!is_dir($ep)){

					return mkdir($ep,0777,TRUE);

				}

				return TRUE;

			}

			public function __save($name,$value){

				if(!$this->createDirectory()){

					throw new \RuntimeException("Could not create cache directory {$this->dir}");

				}

				$file	=	"{$this->getEntryPoint()}/$name";
				$fp	=	fopen($file,'w');
				fwrite($fp,$value);
				fclose($fp);

				return $this;

			}

		}

	}
