<?php

	/**
	 * The request class creates the proper URL to be passed to an HTTP Adapter
	 * passed in the constructor, it uses said adapter in order to fetch the contents through http.
	 */

	namespace stange\fbsucker\http{

		use \stange\fbsucker\request\Url;
		use \stange\fbsucker\http\iface\Adapter	as HttpAdapterIface;
		use \stange\fbsucker\http\request\Cursor	as	RequestCursor;
		use \stange\fbsucker\request\iface\Token	as	TokenIface;
		use \stange\fbsucker\graph\Data				as	GraphData;

		class Request{

			private	$adapter		=	NULL;
			private	$url			=	NULL;
			private	$graphData	=	NULL;
			private	$token		=	NULL;
			private	$objectId	=	NULL;

			public function __construct(Array $args=Array()){

				$adapter	=	isset($args['adapter'])	? $args['adapter']	:	NULL;
				$url		=	isset($args['url'])		? $args['url']			:	'https://graph.facebook.com';
				$token	=	isset($args['token'])	? $args['token']		:	NULL;

				$this->setToken($token);
				$this->setUrl(new Url($url));
				$this->setAdapter($adapter);

				$this->graphData	=	new GraphData($this);

			}

			public function setObjectId($objectId){

				$this->objectId	=	$objectId;
				return $this;

			}

			public function getObjectId(){

				return $this->objectId;

			}

			public function setToken(TokenIface $token){

				$this->token	=	$token;
				return $this;

			}

			public function getToken(){

				return $this;

			}

			public function setGraphData(GraphData $data){

				$this->graphData	=	$data;
				return $this;

			}

			public function getGraphData(){

				return $this->graphData;

			}

			public function setAdapter(HttpAdapterIface $adapter){

				$this->adapter	=	$adapter;
				return $this;

			}

			public function getAdapter(){

				return $this->adapter;

			}

			public function setUrl(Url $url){

				$this->url	=	$url;
				return $this;

			}

			public function getUrl(){

				return $this->url;

			}

			public function request($objectId=NULL,$fields=NULL,$version='2.8'){

				$objectId	=	$objectId	?	$objectId	:	$this->objectId;

				if(empty($objectId)){

					throw new \InvalidArgumentException("No object id specified to be queried");

				}

				$url	=	clone($this->url);

				$url->getPath()
				->add("v$version")
				->add($objectId);

				$url->getQuery()
				->add('access_token',$this->token)
				->add('metadata',1);

				if(is_array($fields)){

					$fields	=	implode(',',$fields);

				}

				if(!empty($fields)){

					$url->getQuery()->add('fields',$fields);

				}

				$this->graphData->set($this->adapter->request($url));

				return $this;

			}

		}

	}

