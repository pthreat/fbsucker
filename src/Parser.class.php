<?php

	namespace stange\fbsucker{

		abstract class Parser{

			private	$data			=	NULL;
			private	$isParsed	=	FALSE;

			public function __construct($data){

				$this->setData($data);

			}

			public function setData($data){

				if(empty($data)){

					throw new \InvalidArgumentException("Data to be parsed can not be empty");

				}

				$this->data	=	$data;

				return $this;

			}

			public function getData(){

				return $this->data;

			}

			public function parse(){

				$result	=	$this->__parse();
				$this->isParsed	=	TRUE;
				return $result;

			}

			abstract protected function __parse();

		}

	}
