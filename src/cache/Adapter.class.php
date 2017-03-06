<?php

	namespace stange\fbsucker\cache{

		abstract class Adapter{

			abstract public function __load($name);
			abstract public function __save($name,$value);

			public function load($name){

			}

			public function save($name,$value){
			}

		}

	}
