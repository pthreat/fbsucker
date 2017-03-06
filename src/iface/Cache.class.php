<?php

	namespace stange\fbsucker\iface{

		interface Cache{

			/** Load cache **/
			public function load($name);

			/** Save cache **/
			public function save($name,$value);

			/** Save OR load cache **/
			public function sload($name,$value);


		}

	}		

