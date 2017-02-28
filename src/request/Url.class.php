<?php

	namespace stange\fbsucker\request{

		use \stange\fbsucker\request\helper\Url	as	UrlHelper;
		use \stange\fbsucker\request\url\Query		as	UrlQuery;
		use \stange\fbsucker\request\url\Path		as	UrlPath;

		class Url{
	
			private	$url		=	NULL;

			public function __construct($url){

				if(!filter_var($url,FILTER_VALIDATE_URL)){

					throw new \InvalidArgumentException("Invalid URL $url");

				}

				$struct	=	Array(
										'scheme'		=>	'',
										'user'		=>	'',
										'pass'		=>	'',
										'host'		=>	'',
										'port'		=>	'',
										'path'		=>	'',
										'query'		=>	'',
										'fragment'	=>	'',
				);

				$url	=	array_merge(
											$struct,
											parse_url($url)
				);

				$urlVars	=	Array();

				if(!empty($url['query'])){

					parse_str($url['query'],$urlVars);

				}

				$url['query']	=	new UrlQuery($urlVars);
				$url['path']	=	new UrlPath($url['path']);

				$this->url	=	$url;

			}

			public function __clone(){

				$this->url['query']	=	clone($this->url['query']);
				$this->url['path']	=	clone($this->url['path']);

			}

			public function setScheme($scheme){

				$this->url['scheme']	=	$scheme;
				return $this;

			}

			public function getScheme(){

				return $this->url['scheme'];

			}

			public function setPath(UrlPath $path){

				$this->url['path']	=	$path;
				return $this;

			}

			public function getPath(){

				return $this->url['path'];

			}

			public function setQuery(UrlQuery $query){
				
				$this->url['query']	=	$query;
				return $this;

			}

			public function getQuery(){

				return $this->url['query'];

			}

			public function setHost($host){

				$this->url['host']	=	$host;
				return $this;

			}

			public function  getHost(){

				return $this->url['host'];

			}

			public function setPort($port){

				$this->url['port']	=	(int)$port;
				return $this;

			}

			public function getPort(){

				return $this->url['port'];

			}

			public function setFragment($fragment){

				$this->url['fragment']	=	$fragment;
				return $this;

			}

			public function getFragment(){

				return $this->url['fragment'];

			}

			public function getString(){

				return UrlHelper::parseUrlToString($this->url);

			}

			public function __toString(){
			
				try{

					return $this->getString();

				}catch(\Exception $e){

					return '';

				}

			}

		}

	}
