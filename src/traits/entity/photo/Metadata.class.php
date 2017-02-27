<?php

	namespace stange\fbsucker\traits\entity\photo{

		use \stange\fbsucker\parser\photo\Metadata	as	MetadataParser;

		trait Metadata{

			public function getMetadata($mockPic=NULL,$dimensions='max'){

				if($this->metaData){

					return $this->metaData;

				}

				if(!$mockPic){

					$file	=	tempnam(sys_get_temp_dir(),'fbsucker_');
					$this->save($file);

				}

				$this->metaData	=	new MetadataParser($file);

				if(!$mockPic){

					unlink($file);

				}

				return $this->metaData;

			}

		}

	}
