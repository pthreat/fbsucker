<?php

	namespace stange\fbsucker\entity{

		use \stange\fbsucker\Entity								as	AbstractEntity;
		use \stange\fbsucker\collection\Comment				as	CommentCollection;
		use \stange\fbsucker\entity\Comment						as	CommentEntity;
		use \stange\fbsucker\traits\entity\photo\Metadata	as	TraitMetadata;
		use \stange\fbsucker\graph\data\Glist					as	GraphList;
		use \stange\fbsucker\parser\photo\Metadata;

		class Photo extends AbstractEntity{

			private	$widths		=	Array();
			private	$heights		=	Array();
			private	$metaData	=	NULL;

			use TraitMetadata;

			public function getComments(){

				$list	=	new GraphList($this->getGraphData()->getRequest(),$this->comments);
				$list->setEntity(new Comment());

				return $list;
		
			}

			public function getCreatedAt($format=NULL){

				$date	=	\DateTime::createFromFormat(\DateTime::ATOM,$this->created_time);

				if(!$date){

					throw new \InvalidArgumentException("Could not create date object");

				}

				return $date->format($format) ? $date->format($format) : $date;

			}

			public function getUpdatedAt($format){

				$date	=	\DateTime::createFromFormat(\DateTime::ATOM,$this->updated_time);

				if(!$date){

					throw new \InvalidArgumentException("Could not create date object");

				}

				return $date->format($format) ? $date->format($format) : $date;

			}

			public function getMaxQuality($wholeNode=FALSE){

				$max	=	NULL;

				foreach($this->images as $image){

					if(!isset($image->width)){

						$msg	=	"The image node does not has a width";
						throw new \RuntimeException($msg);

					}

					if($max === NULL){

						$max = $image;

					}

					$max = $image->width > $max->width ? $image : $max;

				}

				if(!isset($max->source)){

					throw new \RuntimeException("The max quality pic seems to be missing the source");

				}

				return $wholeNode ? $max : $max->source;

			}

			public function getAvailableWidths(){

				if($this->widths){

					return $this->widths;

				}

				$widths	=	Array();

				foreach($this->getQualities() as $quality){

					$widths[]	=	$quality['width'];

				}

				$this->widths	=	$widths;

				return $widths;

			}

			public function getAvailableHeights(){

				if($this->heights){

					return $this->heights;

				}

				$heights	=	Array();

				foreach($this->getQualities() as $quality){

					$heights[]	=	$quality['height'];

				}

				$this->heights	=	$heights;

				return $heights;

			}

			public function getInQuality($dimensions,$withNode=FALSE){

				$isNumeric	=	is_numeric($dimensions);

				if(!$isNumeric && empty($dimensions)){

					throw new \InvalidArgumentException("Dimensions must be passed to get a picture in a given quality");

				}

				if(is_string($dimensions) && $dimensions == 'max'){

					return $this->getMaxQuality($withNode);

				}

				$width		=	NULL;
				$height		=	NULL;

				if($isNumeric){

					$width	=	(int)$dimensions;

				}elseif(is_array($dimensions)){

					$width	=	array_shift($dimensions);

					if(sizeof($dimensions)){

						$height	=	$dimensions[array_keys($dimensions)[0]];

					}

				}

				if(!in_array($width,$this->getAvailableWidths())){

					throw new \InvalidArgumentException("Image is not available in width: \"$width\"");

				}

				if($height !== NULL && !in_array($height,$this->getAvailableHeights())){

					throw new \InvalidArgumentException("Image is not available in height: \"$height\"");

				}

				foreach($this->getGraphAttribute("images") as $image){
					
					if(
							$image->width == $width ||
							($height!==NULL && $image->height == $height)
					){
							
						return $withNode ? $image : $image->source;

					}

				}


			}

			public function getQualities(){

				$qualities	=	Array();
			
				foreach($this->images as $image){

					$image	=	(Array)$image;
					unset($image['source']);
					$qualities[]	=	$image;

				}

				return $qualities;

			}

			public function getQualitiesAsString(){

				$ret	=	'';

				foreach($this->getQualities() as $image){

					$ret = sprintf('%s%sx%s, ',$ret,$image['width'],$image['height']);

				}

				return substr($ret,0,-2);

			}

			public function save($location,$inQuality='max'){

				$image	=	$this->getInQuality($inQuality);

				file_put_contents($location,file_get_contents($image));

				return $this;

			}

			public function __toString(){

				return sprintf('%s',$this->getLink());

			}

		}

	}
