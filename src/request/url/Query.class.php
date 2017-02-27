<?php

	namespace stange\fbsucker\request\url{

		use \stange\fbsucker\request\url\query\Param	as	QueryParam;

		class Query{

			private	$params		=	Array();
			private	$separator	=	'&';

			public function __construct(Array $params,$separator='&'){

				foreach($params as $name=>$value){

					$this->add($name,$value);

				}

			}

			public function setSeparator($separator){

				$this->separator	=	$separator;
				return $this;

			}

			public function getSeparator(){

				return $this->separator;

			}

			public function add($name,$value,$separator='='){


				$this->params[]	=	new QueryParam(
																$name,
																$value,
																$separator
				);

				return $this;

			}

			public function replace($name,$value,$separator='='){

				$find	=	$this->find($name);

				if(!$find){

					return $this->add($name,$value,$separator);

				}

				$fins->setSeparator($separator);
				$find->setValue($value);

				return $this;

			}

			public function find($name){

				foreach($this->params as $param){

					if($param->getName() == $name){

						return $param;

					}

				}

				return FALSE;

			}

			public function remove($name){

				foreach($this->params as $key=>$param){

					if($param->getName() == $name){

						unset($this->params[$key]);

					}

				}

			}

			public function getString(){

				$str	=	Array();

				foreach($this->params as $value){

					$str[]	=	sprintf('%s',$value);

				}

				return implode($this->separator,$str);

			}

			public function __toString(){

				return sprintf('%s',$this->getString());

			}	

		}

	}
