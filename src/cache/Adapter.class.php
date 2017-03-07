<?php

	namespace stange\fbsucker\cache{

		use \stange\fbsucker\iface\Cache						as	CacheInterface;
		use \stange\fbsucker\iface\Log						as	LoggingInterface;
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
											isset($params['entryPoint']) ? 
											$params['entryPoint'] : NULL
				);

				if(isset($params['log'])){

					$this->setLog($params['log']);

				}

			}

			public function setEntryPoint($entry){

				$_entry	=	trim($entry);

				if(empty($_entry)){

					throw new \InvalidArgumentException("Cache entry point must not be empty");

				}

				$this->entryPoint	=	$entry;

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

			public function load($name){

				$content	=	$this->__load($name);

				if($content === NULL){

					throw new NotFoundException("Cache \"$name\" not found");

				}

				if($content === FALSE){

					$msg	=	$this->error	?	$this->error	: "Could not read cache \"$name\"";
					throw new ReadException($msg);

				}

				return $content;

			}

			public function save($name,$value){

				$save	=	$this->__save($name,$value);

				if($save == NULL){

					$msg	=	$this->error	?	$this->error	: "Could not write cache \"$name\"";
					throw new WriteException($msg);

				}

				return TRUE;

			}

			public function sload($name,$value){

				$this->save($name,$value);
				return $this->load($name);

			}

		}

	}
