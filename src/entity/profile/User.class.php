<?php

	namespace stange\fbsucker\entity\profile{

		use \stange\fbsucker\entity\Profile			as	AbstractProfile;
		use \stange\fbsucker\entity\photo\Cover	as	CoverPhoto;
		use \stange\fbsucker\parser\Social			as	SocialMediaParser;
		use \stange\fbsucker\graph\Data				as	GraphData;
		use \stange\fbsucker\graph\data\GList		as	GraphList;
		use \stange\fbsucker\entity\Photo			as	PhotoEntity;
		use \stange\fbsucker\http\Request;

		class User extends AbstractProfile{

			public function __toString(){

				try{

					return $this->name;

				}catch(\Exception $e){

					return '';

				}

			}

		}

	}
