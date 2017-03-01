<?php

	/**
	 * The request class creates the proper URL to be passed to an HTTP Adapter
	 * passed in the constructor, it uses said adapter in order to fetch the contents through http.
	 */

	namespace stange\fbsucker\http{

		use \stange\fbsucker\request\Url;
		use \stange\fbsucker\http\iface\Adapter			as HttpAdapterIface;
		use \stange\fbsucker\http\request\Cursor			as	RequestCursor;
		use \stange\fbsucker\request\iface\Token			as	TokenIface;
		use \stange\fbsucker\graph\Data						as	GraphData;
		use \stange\fbsucker\graph\request\Fields			as	RequestFields;

		use \stange\fbsucker\request\Cache					as	RequestCache;

		class Request{

			private	$adapter			=	NULL;
			private	$url				=	NULL;
			private	$graphData		=	NULL;
			private	$token			=	NULL;
			private	$objectId		=	NULL;
			private	$cacheAdapter	=	NULL;

			private	$fields			=	NULL;

			public function __construct(Array $args=Array()){

				$adapter		=	isset($args['adapter'])		? $args['adapter']	:	NULL;
				$url			=	isset($args['url'])			? $args['url']			:	'https://graph.facebook.com';
				$token		=	isset($args['token'])		? $args['token']		:	NULL;
				$fields		=	isset($args['fields'])		? $args['fields']		:	Array();

				$this->setToken($token);
				$this->setUrl(new Url($url));
				$this->setAdapter($adapter);

				$this->graphData	=	new GraphData($this);

				$this->setFields($fields);

				//Add by default the metadata{type} field to be able to identify the 
				//returned graph object type

				$this->fields->add('metadata{type}');

			}

			public function setFields($fields){

				$this->fields	=	new RequestFields($fields);
				return $this;

			}

			public function getFields(){

				return $this->fields;

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

			public function setCache(RequestCache $cache){

				$this->cacheAdapter	=	$cache;
				return $this;

			}

			public function getCache(){
	
				return $this->cacheAdapter;

			}

			public function request($objectId=NULL,$fields=NULL,$version='2.8'){

				$objectId	=	$objectId	?	$objectId	:	$this->objectId;

				if(empty($objectId)){

					throw new \InvalidArgumentException("No object id specified to be queried");

				}

				if($fields !== NULL){

					$this->setFields($fields);

				}

				$url	=	clone($this->url);

				$url->getPath()
				->add("v$version")
				->add($objectId);

				$this->fields->getQuery()->replace('access_token',$this->token);

				$url->setQuery($this->fields->getQuery());

				/**
				 * If the request needs not to be cached
				 */

				if(!$this->cacheAdapter){

					$this->graphData->set(
												$this->adapter->request($url)
					);

					return $this;

				}

				$this->cacheAdapter->setFilename(sprintf('request_%s',base64_encode($url)));

				try{
			
					$data	=	$this->getCache()->getContents();

				}catch(\LogicException $e){

					//If the file is not readable, a logic exception will be thrown
					throw $e;

				}catch(\Exception $e){

					//If the cache file doesn't exists an \Exception will be thrown

					$data	=	$this->adapter->request($url);
					$this->cacheAdapter->save($data);

				}

				$this->graphData->set($data);

				return $this;

			}

		}

	}

