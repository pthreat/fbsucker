<?php

	namespace stange\fbsucker\graph\request{
	
		use \stange\fbsucker\request\url\Query	as	UrlQuery;

		class Fields{

			private	$query	=	NULL;

			public function __construct($fields){

				if(is_string($fields)){

					$fields	=	explode(',',sprintf('%s',$fields));

					if(count($fields)==1 && empty($fields[0])){

						$fields	=	NULL;

					}

				}

				if(!is_array($fields)){

					$msg	=	"A comma delimited string with fields ot an array is required";
					throw new \InvalidArgumentException($msg);

				}

				$this->query	=	new UrlQuery(Array());
				$this->query->add('fields','');

				foreach($fields as $field){

					$this->add($field);

				}

			}

			public function getQuery(){

				return $this->query;

			}

			public function add($field){

				if(!is_string($field)){

					throw new \InvalidArgumentException("Graph Request query only takes in strings");

				}

				$field	=	trim($field);

				if(empty($field)){

					throw new \InvalidArgumentException("Graph Request field can not be empty");

				}

				$fields	=	explode(',',$this->query->find('fields')->getValue());
				$fields	=	empty($fields[0]) ? Array() : $fields;

				if(in_array($field,$fields)){

					return $this;

				}

				$isMetadata	=	preg_match('/metadata/',$field);
				$fields[]	=	$field;

				sort($fields);

				$this->query->find('fields')->setValue(implode(',',$fields));

				if($isMetadata){

					$this->query->replace('metadata',1);

				}

				return $this;

			}

			public function __toString(){

				return sprintf('%s',$this->query->find('fields'));

			}

		}

	}
