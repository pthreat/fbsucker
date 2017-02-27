<?php

	namespace stange\fbsucker\request\url{

		class Path{

			private	$pieces		=	Array();
			private	$separator	=	'/';

			public function __construct($pieces=NULL,$separator='/'){

				$this->setPieces($pieces ? $pieces : Array(),$separator);
				$this->setSeparator($separator);

			}

			public function setSeparator($separator){

				$this->separator	=	$separator;
				return $this;

			}

			public function getSeparator(){

				return $this->separator;

			}

			public function setPieces($pieces,$separator=NULL){
	
				$separator	=	$separator===NULL ? $this->separator : $separator;

				if(is_string($pieces)){
					
					$this->pieces	=	explode($separator,$pieces);

					return $this;

				}

				if(is_array($pieces)){

					$this->pieces = $pieces;

				}

				return $this;

			}

			public function getPieces(){

				return $this->pieces;

			}

			public function add($path){

				$this->pieces[]	=	$path;
				return $this;

			}

			public function has($path){

				foreach($this->pieces as $piece){

					if($piece==$path){

						return TRUE;

					}

				}

				return FALSE;

			}

			public function getString(){

				return sprintf("%s%s",$this->separator,implode($this->separator,$this->pieces));

			}

			public function __toString(){

				return $this->getString();

			}

		}

	}
