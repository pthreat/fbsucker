<?php

	namespace stange\fbsucker\cache{

		use \stange\fbsucker\iface\Cache						as	CacheInterface;
		use \stange\fbsucker\cache\exception\NotFound	as	NotFoundException;
		use \stange\fbsucker\cache\exception\Read			as	ReadException;
		use \stange\fbsucker\cache\exception\Save			as	WriteException;

		abstract class Adapter implements CacheInterface{

			private	$error		=	NULL;
			private	$entryPoint	=	NULL;
			private	$log			=	NULL;

			/** Provide logging capabilities to this class **/

			use \stange\fbsucker\traits\Loggable;

			public function __construct(Array $params=Array()){

				$this->setEntryPoint(
											isset($params['entry']) ? 
											$params['entry'] : NULL
				);

				if(isset($params['log'])){

					$this->setLog($params['log']);

				}

			}

			/**
			 * Every cache adapter has an entry point. But what do we mean by entry point?
			 * In the case of a File Adapter cache class this would be a directory
			 * In the case of a PDO Adapter cache class this would be a table on a database
			 * In the case of a Memcache Adapter this would be the parameters 
			 * for being able to connect to the memcached server.
			 */

			public function setEntryPoint($entry){

				$_entry	=	trim($entry);

				if(empty($_entry)){

					throw new \InvalidArgumentException("Cache entry point must not be empty");

				}

				$this->entryPoint	=	$entry;

				$this->log("Set cache entry point to: $entry");

				return $this;

			}

			public function getEntryPoint(){

				return $this->entryPoint;

			}

			protected function setError($error){

				$this->error	=	$error;
				return $this;

			}

			protected function getError(){

				return $this->error;

			}

			abstract protected function __load($name);
			abstract protected function __save($name,$value);

			/**
			 * The load method attempts to load a cache key named $name.
			 *
			 * @throws NotFoundException in case the cache key has not been found in cache
			 * @throws ReadException in case the cache key could not be read.
			 * @return mixed cache contents
			 */

			public function load($name){

				$content	=	$this->__load($name);

				if($content === NULL){

					$this->log("$name was not found in cache","warning");
					throw new NotFoundException("Cache \"$name\" not found");

				}

				if($content === FALSE){

					$msg	=	$this->error	?	$this->error	: "Could not read cache \"$name\"";
					throw new ReadException($msg);

				}

				$this->log("Fetching \"$name\" from cache","success");

				return $content;

			}

			/**
			 * The save method saves a value in cache idenified by name ($name)
			 * @throws WriteException if the derived __save function in the child adapter 
			 * returns NULL.
			 */

			public function save($name,$value){

				$this->log("Saving $name in cache",'debug');

				$save	=	$this->__save($name,$value);

				if($save == NULL){

					$msg	=	$this->error	?	$this->error	: "Could not write cache \"$name\"";
					throw new WriteException($msg);

				}

				return TRUE;

			}

			/**
			 * The sload method is a combination between the load and the save method.
			 * it works by trying to load a certain cache key (name) first. If said cache key
			 * does not exists, it will save said cache with name $name and value $value
			 */

			public function sload($name,$value){

				try{

					return $this->load($name);

				}catch(\Exception $e){

					$this->save($name,$value);
					return $this->load($name);

				}

			}

		}

	}
