<?php

namespace stange\fbsucker\parser\photo{

	class Metadata{

		private	$data	=	NULL;

		public function __construct($file){

			if(!file_exists($file) || !is_readable($file)){

				throw new \InvalidArgumentException("File \"$file\" doesn't exists");

			}

			if(!class_exists('Imagick')){

				$msg	=	"Could not find class Imagick, read the documentation on how to install the module";

				throw new \RuntimeException($msg);

			}

			$i	=	new \Imagick($file);

			$this->setData($i->getImageProperties());

		}

		public function setData($data){

			$this->data	=	array_change_key_case($data,\CASE_LOWER);
			return $this;

		}

		public function getData(){

			return $this->data;

		}

		public function getCreatedAt($format=NULL){

			return $this->parseDateField("create",$format);

		}

		public function getUpdatedAt($format=NULL){

			return $this->parseDateField("modify",$format);

		}

		private function parseDateField($field,$format){

			$date	=	NULL;

			if(isset($this->data["date:$field"])){

				$date	=	$this->data["date:$field"];


			}elseif(isset($this->data["$field"])){

				$date	=	$this->data[$field];

			}

			$date	=	\DateTime::createFromFormat(\DateTime::ATOM,$date);

			if(!$date){

				return '';

			}

			return $format ? $date->format($format) : $date;
		}


		private function getGPS($exifCoord, $hemi){

			$exifCoord	=	explode(',',$exifCoord);

			foreach($exifCoord as &$coord){

				$coord = trim($coord);

			}

			$degrees = count($exifCoord) > 0 ? $this->gps2Dec($exifCoord[0]) : 0;
			$minutes = count($exifCoord) > 1 ? $this->gps2Dec($exifCoord[1]) : 0;
			$seconds = count($exifCoord) > 2 ? $this->gps2Dec($exifCoord[2]) : 0;

			$flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

			return $flip * ($degrees + $minutes / 60 + $seconds / 3600);

		}

		private function gps2Dec($coordPart) {

			$parts = explode('/', $coordPart);

			if (count($parts) <= 0){

				return 0;

			}

			if (count($parts) == 1){

				return $parts[0];

			}

			return floatval($parts[0]) / floatval($parts[1]);

		}

		public function getLatitude(){

			$latitude	=	$this->getExif("GPSLatitude");
			$latRef		=	$this->getExif("GPSLatitudeRef");

			return $latitude && $latRef ? $this->getGPS($latitude,$latRef) : NULL;

		}

		public function getLongitude(){

			$longitude	=	$this->getExif("GPSLongitude");
			$longRef		=	$this->getExif("GPSLongitudeRef");

			return $longitude && $longRef ? $this->getGPS($longitude,$longRef) : NULL;

		}

		public function getExif($attr){

			$attr	=	strtolower($attr);

			if(array_key_exists("exif:$attr",$this->data)){

				return $this->data["exif:$attr"];

			}

		}

		public function getXMP($attr){

			$attr	=	strtolower($attr);

			if(array_key_exists("xmp:$attr",$this->data)){

				return $this->data["xmp:$attr"];

			}

		}

		public function __call($method,$args){

			$m=strtolower($method);

			if(preg_match('/^get.*/',$m)){

				return $this->getExif(substr($m,3));

			}

			throw new \BadMethodCallException("No such method $method");

		}

		public function __toString(){

			$ret = '';

			foreach($this->data as $key=>$value){

				$ret = sprintf("%s%s:%s\n",$ret,$key,$value);
				
			}

			return $ret;

		}

	}

}
