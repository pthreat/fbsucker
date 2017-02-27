<?php

	namespace stange\fbsucker{

		class Social{
	
			private	$email	=	NULL;
			private	$urls		=	Array();

			public function setEmail($email){

				if(!filter_var($email,\FILTER_VALIDATE_EMAIL)){

					throw new \InvalidArgumentException("Invalid email address");

				}

				$this->email	=	$email;

				return $this;

			}

			public function getEmail(){

				return $this->email;

			}

			public function addUrl($url){
		
				if(in_array($url,$this->urls)){

					return $this;	

				}

				$this->urls[] = $url;

				return $this;

			}

			public function hasNetwork($regexp){

				$regexp	=	preg_quote($regexp);

				foreach($this->urls as $url){

					if(preg_match("/$regexp/",$url)){

						return $url;

					}

				}

			}

		}

	}
