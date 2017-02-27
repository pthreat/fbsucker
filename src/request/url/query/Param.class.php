<?php

	namespace stange\fbsucker\request\url\query{

		class Param{

			private	$name				=	NULL;
			private	$value			=	NULL;
			private	$sign				=	NULL;

			public function __construct($name,$value,$sign='='){

				$this->setName($name);
				$this->setValue($value);
				$this->setSign($sign);

			}

			public function setName($name){
	
				$this->name	=	$name;
				return $this;

			}

			public function getName(){

				return $this->name;

			}

			public function setSign($sign){

				$this->sign	=	$sign;
				return $this;

			}

			public function getSign(){

				return $this->sign;

			}

			public function setValue($value){

				$this->value	=	$value;
				return $this;

			}

			public function getValue(){

				return $this->value;

			}

			public function getString(){

				return sprintf(
									'%s%s%s',
									$this->name,
									$this->sign,
									$this->value
				);

			}

			public function __toString(){

				return sprintf('%s',$this->getString());

			}

		}

	}
