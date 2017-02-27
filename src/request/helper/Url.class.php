<?php

	namespace stange\fbsucker\request\helper{

		class Url{

			public static function parseUrlToString(Array $url){

				if(!isset($url['host'])){

					throw new \InvalidArgumentException("Invalid URL array, host key missing");

				}

				if(isset($url['query']) && is_array($url['query'])){

					$query	=	Array();

					foreach($url['query'] as $var=>$value){

						$query[]	=	"$var=$value";

					}

					$url['query']	=	implode('&',$query);
					unset($query);

				}

	
				$query	=	sprintf('%s',$url['query']);

				$scheme		=	isset($url['scheme'])	? $url['scheme']		: 'http';
				$fragment	=	isset($url['fragment']) ? $url['fragment']	: '';
				$query		=	empty($query)				? ''						: "?$query";
				$path			=	isset($url['path'])		? $url['path']			: '';

				$userPass	=	'';
				$port			=	'';

				$hasUser	=	!empty($url['user']);
				$hasPass	=	!empty($url['pass']);
				$hasPort	=	isset($url['port']) && is_numeric($url['port']) && $url['port']>=0;

				if($hasUser&&$hasPass){

					$userPass="$url[user]:$url[pass]@";

				}elseif($hasUser){

					$userPass="$url[user]@";

				}

				if($hasPort){

					$port	=	(int)$url["port"];
					$port	=	":$port";

				}

				return sprintf(
									'%s://%s%s%s%s%s%s',
									$scheme,
									$userPass,
									$url['host'],
									$port,
									$path,
									$query,
									$fragment
				);

			}

		}

	}
