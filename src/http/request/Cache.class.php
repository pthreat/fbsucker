<?php

	namespace stange\fbsucker\http\request{
	
		class Cache{

			private	$dir			=	NULL;
			private	$fileName	=	NULL;

			public function __construct($dir,$fileName=NULL){

				$this->setDir($dir);

				if(!is_null($fileName)){

					$this->setFilename($fileName);

				}

			}

			public function setDir($dir){

				$this->dir	=	$dir;
				return $this;

			}

			public function getDir(){

				return $this->dir;

			}

			public function setFilename($name){

				$this->fileName	=	$name;
				return $this;

			}

			public function getFilename(){

				return $this->fileName;

			}

			public function getContents(){

				$file	=	"{$this->dir}/{$this->fileName}";

				if(!file_exists($file)){

					throw new \Exception("Cache file \"$file\" does not exists");

				}

				if(!is_readable($file)){

					throw new \LogicException("Cache file \"$file\" is not readable, check file permissions");

				}

				return file_get_contents($file);

			}

			private function createDirectory($profile){

				if(!is_dir($this->dir)){

					return mkdir($this->dir,0777,TRUE);

				}

				return TRUE;

			}

			public function save($contents){

				if(!$this->createDirectory()){

					throw new \RuntimeException("Could not create cache directory {$this->dir}");

				}

				$file	=	"{$this->dir}/{$this->fileName}";
				$fp	=	fopen($file,'w');
				fwrite($fp);
				fclose($fp);

				return $this;

			}

			public function __toString(){

				try{

					return $this->getContents();

				}catch(\Exception $e){

					return '';

				}

			}

		}

	}
