<?php

	namespace stange\fbsucker\http\adapter{

		use stange\fbsucker\http\iface\Adapter	as	AdapterInterface;

		class Basic implements AdapterInterface{

			public function request($path,$method=NULL){

				$method	=	$method===NULL ? 'get' : $method;

				if(strtolower($method)!=='get'){

					throw new \RuntimeException("Method $method not implemented yet");

				}

				return file_get_contents($path);

			}

		}

	}
