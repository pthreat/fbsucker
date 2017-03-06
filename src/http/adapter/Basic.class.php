<?php

	namespace stange\fbsucker\http\adapter{

		use stange\fbsucker\http\Adapter			as	BaseHttpAdapter;

		class Basic extends BaseHttpAdapter{

			public function __request($path,$method=NULL){

				$method	=	$method===NULL ? 'get' : $method;

				if(strtolower($method)!=='get'){

					throw new \RuntimeException("Method $method not implemented yet");

				}

				$context = stream_context_create([
																'http' => [
																				'ignore_errors' => true
																],
				]);

				return file_get_contents($path,FALSE,$context);

			}

		}

	}
