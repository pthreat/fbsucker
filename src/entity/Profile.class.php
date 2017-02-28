<?php

	namespace stange\fbsucker\entity{

		use \stange\fbsucker\Entity					as	AbstractEntity;
		use \stange\fbsucker\entity\photo\Cover	as	CoverPhoto;
		use \stange\fbsucker\parser\Social			as	SocialMediaParser;
		use \stange\fbsucker\graph\Data				as	GraphData;
		use \stange\fbsucker\graph\data\GList		as	GraphList;
		use \stange\fbsucker\entity\Photo			as	PhotoEntity;
		use \stange\fbsucker\http\Request;

		abstract class Profile extends AbstractEntity{

			public function getZodiacSign(){

				$zodiac	=	Array(
										'356'	=>	'Capricorn','326'	=>	'Sagittarius','296'	=>	'Scorpio',
										'266'	=>	'Libra','235'	=>	'Virgo','203'	=>	'Leo','172'	=>	'Cancer',
										'140'	=>	'Gemini','111'	=>	'Taurus','78'	=>	'Aries','51'	=>	'Pisces',
										'20'	=>	'Aquarius','0'	=>	'Capricorn'
				);

				$dayOfTheYear	=	$this->getBirthday('z');
				$isLeapYear		=	$this->getBirthday('L');

				if($isLeapYear && ($dayOfTheYear > 59)){

					$dayOfTheYear = $dayOfTheYear - 1;

				}

				foreach($zodiac as $day => $sign){ 

					if ($dayOfTheYear > $day){ 

						break;

					}

				}

				return $sign;

			}

			public function getCover(){

				return new CoverPhoto(
												new GraphData(
																	$this->getGraphData()->getRequest(),
																	$this->cover
												)
				);

				return $cover;

			}

			public function getSocialMedia(){

				$smparser	=	new SocialMediaParser(
																sprintf(
																			'%s %s',
																			$this->getDescription(),
																			$this->getAbout()
																)
				);

				return $smparser->parse();

			}

			public function getBirthday($format=NULL){

				$birthday	=	\DateTime::createFromFormat('d/m/Y',$this->birthday);

				return $format ? $birthday->format($format) : $birthday;

			}

			public function isCommunityPage(){

				return (boolean)$this->is_community_page;

			}

			public function getLikes(){

				$list	=	new GraphList($this->getGraphData()->getRequest(),$this->likes);
				$list->setEntity(new static());

				return $list;

			}

			public function isPublished(){

				return $this->is_published;

			}

			public function getPeopleTalkingAboutCount(){

				return $this->talking_about_count;

			}

			public function getPhotos(){

				$request	=	clone($this->getGraphData()->getRequest());

				$list	=	new GraphList($request);
				$list->setEntity(new PhotoEntity());

				$request->setGraphData($list);
				$request->setObjectId("{$this->id}/photos");

				return $list;

			}

			public function __toString(){

				try{

					return $this->name;

				}catch(\Exception $e){

					return '';

				}

			}

		}

	}
