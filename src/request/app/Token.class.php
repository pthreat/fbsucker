<?php

	namespace stange\fbsucker\request\app{

		use \stange\fbsucker\request\iface\Token	as	TokenInterface;

		class Token implements TokenInterface{

			private	$id		=	NULL;
			private	$secret	=	NULL;

			public function __construct(Array $args=Array()){
				
				$this->setId(isset($args['id']) ? $args['id'] : NULL);
				$this->setSecret(isset($args['secret']) ? $args['secret'] : NULL);

			}

			public function setId($id){

				$id	=	trim($id);

				if(empty($id)){

					throw new \InvalidArgumentException("Invalid App id");

				}

				$this->id	=	$id;
				return $this;

			}

			public function getId(){

				return $this->id;

			}

			public function setSecret($secret){

				$secret	=	trim($secret);

				if(empty($secret)){

					throw new \InvalidArgumentException("Invalid App secret");

				}

				$this->secret	=	$secret;

				return $this;

			}

			public function getSecret(){

				return $this->secret;

			}

			public function getToken(){

				return sprintf('%s|%s', $this->id,$this->secret);

			}

			public function __toString(){

				return $this->getToken();

			}

		}
			
	}

