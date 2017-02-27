<?php

	namespace stange\fbsucker\entity{

		use \stange\fbsucker\Entity	as	AbstractEntity;

		class Place extends AbstractEntity{

			public function getId(){

				return $this->getGraphAttribute('id');

			}

			public function getName(){

				return $this->getGraphAttribute('name');

			}

			public function getCity(){

				return $this->getGraphAttribute('location')->city;

			}

			public function getCountry(){

				return $this->getGraphAttribute('location')->country;

			}

			public function getLatitude(){

				return $this->getGraphAttribute('location')->latitude;

			}

			public function getLongitude(){

				return $this->getGraphAttribute('location')->longitude;

			}

			public function getStreet(){

				return $this->getGraphAttribute('location')->street;

			}

			public function __toString(){

				try{

					return sprintf(
										'%s, %s, %s/%s',
										$this->getName(),
										$this->getStreet(),
										$this->getCity(),
										$this->getCountry()
					);

				}catch(\Exception $e){

					return '';

				}

			}

		}

	}
