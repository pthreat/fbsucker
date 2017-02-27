<?php

	namespace stange\fbsucker\parser{

		use \stange\fbsucker\Social	as	SocialMedia;

		class Social{

			public function __construct($data){

				$this->setData($data);

			}

			public function setData($data){

				$this->data	=	$data;
				return $this;

			}

			public function getData(){

				return $this->data;

			}

			public function parse(){

				$social	=	new SocialMedia();

				$data		=	explode(" ",$this->data);

				foreach($data as $piece){

					$data	=	trim($piece);

					if(empty($data)){

						continue;

					}

					$data	=	explode("\n",$data);
					$data	=	$data[0];

					if(filter_var($data,\FILTER_VALIDATE_EMAIL)){

						$social->setEmail($data);
						continue;

					}

					if(preg_match('/^www\..*/',$data)){

						$data	=	"http://$data";

					}

					if(!\filter_var($data,\FILTER_VALIDATE_URL)){

						continue;

					}

					$social->addUrl($data);

				}

				return $social;

			}

		}

	}
