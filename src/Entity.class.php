<?php

	/**
	 * Entities take in facebook graph data and define conveniently a set of friendly getters
	 * in order to make the developer experience as swift as possible.
	 *
	 * WARNING:
	 *---------------------------------------------------------------------------------------------
	 *
	 * If a graph property does not exists, the abstract entity will attempt to fetch said property
	 * through the GraphData object. 
	 *
	 * The major inconvenient with this is that you might end up making 
	 * multiple requests to the graph API when you didn't intended to.
	 *
	 */

	namespace stange\fbsucker{

		use \stange\fbsucker\graph\Data	as	GraphData;

		abstract class Entity{

			private	$graphData	=	NULL;

			final public function __construct(GraphData $graphData=NULL){

				if(!is_null($graphData)){

					$this->setGraphData($graphData);

				}

			}

			public function setGraphData(GraphData $data){

				$this->graphData	=	$data;
				return $this;

			}

			public function getGraphData(){

				return $this->graphData;

			}

			public function __get($name){

				return $this->graphData->$name;

			}

			public function __call($method,$args){

				if(substr($method,0,3)=='get'){

					return $this->graphData->get(strtolower(substr($method,3)));

				}

				throw new \BadMethodCallException("No such method $method");

			}

		}

	}
