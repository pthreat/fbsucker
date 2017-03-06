<?php

	/**
	 * For now on, this class is just about file caching 
	 *
	 * @TODO build a cache-adapter pattern in order to be able to build a cache adapter through a factory
	 *
	 * abstract Cache.class.php
	 * cache/Adapter
	 * cache/Factory 
	 * cache/adapter/File extends CacheAdapter
	 * cache/adapter/MemCache extends CacheAdapter
	 * cache/adapter/PDO extends CacheAdapter
	 *
	 */

	namespace stange\fbsucker\cache{

		use \stange\fbsucker\cache\Adapter	as	AbstractCacheAdapter;

		class File extends AbstractCacheAdapter{

			private	$dir			=	NULL;

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

			public function load($name){

				$file	=	"{$this->dir}/{$name}";

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

			public function save($name,$contents){

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
