<?php

	namespace stange\fbsucker\http{

		use \stange\fbsucker\iface\Cache			as	CacheInterface;
		use stange\fbsucker\http\iface\Adapter	as	HttpAdapterInterface;

		abstract class Adapter implements HttpAdapterInterface{

			private	$cache	=	NULL;

			public function __construct(Array $params=Array()){

				$cache	=	isset($params['cache'])	?	$params['cache']	:	NULL;

				if($cache){

					$this->setCache($cache);

				}

			}

			public function setCache(CacheInterface $cache){

				$this->cache	=	$cache;
				return $this;

			}

			public function getCache(){

				return $this->cache;

			}

			public function request($url,$method=NULL){

				/** If there's a cache object set in the http adapter **/

				if($this->cache){

					/** Save the cache **/

					$this->cache->save($this->__request($url,$method));

				}

			}

			abstract public function __request($path,$method=NULL);

		}

	}
