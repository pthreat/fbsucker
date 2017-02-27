<?php

	namespace stange\fbsucker\graph\data{

		use \stange\fbsucker\graph\Data	as	GraphData;
		use \stange\fbsucker\Entity		as	AbstractEntity;
		use \stange\fbsucker\request\Url;

		class GList extends GraphData{

			private	$entity	=	NULL;

			public function setEntity(AbstractEntity $entity){

				$this->entity	=	$entity;
				return $this;

			}

			public function getIterator(){

				foreach($this->get()->data as $data){

					$c = clone($this->entity);

					yield(
							$c->setGraphData(
													new GraphData(
																		$this->getRequest(),
																		$data
													)
							)
					);

				}

			}
	
			public function next($limit=NULL){

				if(!isset($this->get()->paging->next)){

					return FALSE;

				}

				$url	=	new Url($this->get()->paging->next);

				if($limit){

					$url->replace('limit',$limit);

				}

				$this->set(
								$this->getRequest()
								->getAdapter()
								->request($url)
				);

				return TRUE;

			}

			public function prev($limit=NULL){

				if(!isset($this->data->paging->prev)){

					return FALSE;

				}

				$url	=	new Url($this->data->paging->previous);

				if($limit){

					$url->replace('limit',$limit);

				}

				$this->set(
								$this->getRequest()->getAdapter()->request($url)
				);

			}

		}

	}
