<?php

	namespace stange\fbsucker\graph{

		use \stange\fbsucker\http\Request;
		use \stange\fbsucker\request\Url;

		class Data{

			private	$data		=	NULL;
			private	$request	=	NULL;

			public function __construct(Request $request,$data=NULL){

				if(!is_null($data)){

					$this->set($data);

				}

				$this->request	=	$request;

			}

			public function getRequest(){

				return $this->request;

			}

			/**
			 * Loads data from a JSON for the request object
			 * Very useful for testing purposes.
			 */

			public function fromJSON($file){
				
				if(!file_exists($file)){

					throw new \InvalidArgumentException("File \"$file\" doesnt exists");

				}

				if(!is_readable($file)){

					throw new \InvalidArgumentException("File \"$file\" is not readable");

				}

				$this->set(file_get_contents($file));

				return $this;

			}

			public function add($node,$value){

				$this->data->$node	=	$value;

				return $this;

			}

			private function checkDataError($data){

				if(!isset($data->error)){

					return;

				}

				throw new \LogicException(
													$data->error->message,
													$data->error->code
				);

			}

			public function set($data){

				if($data instanceof \stdClass){

					$this->checkDataError($data);
					$this->data	=	$data;
					return $this;

				}

				if(empty($data)){

					throw new \RuntimeException("Empty graph data");

				}

				if(!is_string($data)){

					throw new \InvalidArgumentException("Graph data object expects a JSON string");

				}

				$data	=	json_decode($data);

				if(!$data){

					if(function_exists("json_last_error_msg")){

						throw new \Exception(json_last_error_msg());

					}

					throw new \Exception("Could not decode JSON string");

				}

				$this->checkDataError($data);

				$this->data		=	$data;

				return $this;

			}

			public function get($node=NULL,$throw=FALSE){

				if($node){

					if(isset($this->data->$node)){

						return $this->data->$node;

					}

					if($throw){

						throw new\InvalidArgumentException("Graph node \"$node\" not found in graph data");

					}

					return '';

				}

				return $this->data;

			}

			public function __get($name){

				return $this->get($name);

			}

			public function dump(){

				return var_export($this->data,TRUE);

			}

			public function save($fileName,$overwrite=FALSE){

				if(empty($this->data)){

					$msg = "There's no graph data available :(";
					throw new \RuntimeException($msg);

				}

				$fileName	=	trim($fileName);

				if(empty($fileName)){

					throw new \InvalidArgumentException("Must specify a filename");

				}

				if(!$overwrite && file_exists($fileName)){

					throw new \RuntimeException("File $fileName already exists");

				}

				return file_put_contents($fileName,json_encode($this->data));
				
			}

		}

	}
