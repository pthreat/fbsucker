<?php

	namespace stange\fbsucker\graph{

		use \stange\fbsucker\request\Url;
		use \stange\fbsucker\graph\Data	as	GraphData;

		class Cursor{

			private	$data		=	NULL;

			public function __construct(GraphData &$data){

				$this->data	=	$data;

			}

			public function next(){

				if(!isset($this->data->paging->next)){

					return FALSE;

				}

				$url	=	new Url($this->data->paging->next);

				$this->data->set(
										$this->data
										->getRequest()
										->getAdapter()
										->request($url)
				);

			}

			public function prev(){

				if(!isset($this->data->paging->previous)){

					return FALSE;

				}

				$url	=	new Url($this->data->paging->previous);

				$this->data->set(
										$this->data
										->getRequest()
										->getAdapter()
										->request($url)
				);

			}

		}

	}
