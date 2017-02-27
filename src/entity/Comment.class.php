<?php

	namespace stange\fbsucker\entity{

		use \stange\fbsucker\entity\Profile	as	ProfileEntity;
		use \stange\fbsucker\Entity			as AbstractEntity;

		class Comment extends AbstractEntity{

			private	$profile	=	NULL;

			public function getId(){

				return $this->getGraphAttribute("id");

			}

			public function getCreatedAt($format=NULL){

				$date	=	$this->getGraphAttribute("created_time");
				$date	=	\DateTime::createFromFormat(\DateTime::ISO8601,$date);

				return $format ? $date->format($format) : $date;

			}

			public function getFrom(){

				if($this->profile){

					return $this->profile;

				}

				$from	=	$this->getGraphAttribute('from');

				if(!isset($from->name)){

					throw new \RuntimeException("Can not get name of user who created this comment");

				}

				$from->username	=	$from->name;
				$this->profile		=	new ProfileEntity();
				$this->profile->setGraphData($from);

				return $this->profile;

			}

			public function getLikes(){

				return $this->getGraphAttribute('like_count');

			}

			public function getMessage($clean=FALSE){

				$message = $this->getGraphAttribute('message');

				if(!$clean){

					return $message;

				}

				$message = preg_replace("/\./",' ',$message);

				return preg_replace("/[^a-zA-Z0-9àèìòùáéíóúâêîôûäëïöüç]/",' ',$message);

			}

			public function __toString(){

				return '';

			}

		}

	}
