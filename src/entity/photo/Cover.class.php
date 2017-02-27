<?php

	namespace stange\fbsucker\entity\photo{

		use \stange\fbsucker\parser\photo\Metadata;
		use \stange\fbsucker\traits\entity\photo\Metadata		as	TraitMetadata;
		use \stange\fbsucker\Entity									as	AbstractEntity;

		class Cover extends AbstractEntity{

			private	$metaData;

			use TraitMetadata;

			public function getCoverId(){

				return $this->cover_id;

			}

			public function getOffsetX(){

				return $this->offset_x;

			}

			public function getOffsetY(){

				return $this->offset_y;

			}

			public function save($location){

				file_put_contents($location,file_get_contents($this->getSource()));

				return $this;

			}

			public function __toString(){
	
				try{

					return $this->getSource();

				}catch(\Exception $e){

					return '';

				}

			}

		}

	}
