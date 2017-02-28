<?php

	namespace stange\fbsucker\entity{

		use \stange\fbsucker\entity\User		as	UserEntity;
		use \stange\fbsucker\Entity			as AbstractEntity;

		class Comment extends AbstractEntity{

			public function getCreatedAt($format=NULL){

				$date	=	$this->getGraphAttribute("created_time");
				$date	=	\DateTime::createFromFormat(\DateTime::ISO8601,$date);

				return $format ? $date->format($format) : $date;

			}

			public function getFrom(){

				$data	=	clone($this->getGraphData());
				$data->set($this->from);
				$data->getRequest()->setObjectId($this->from->id);

				return new UserEntity($data);

			}

			public function getLikes(){

				return $this->like_count;

			}

			public function getMessage($clean=FALSE){

				$message = $this->message;

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
