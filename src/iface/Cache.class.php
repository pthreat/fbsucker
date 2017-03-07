<?php

	namespace stange\fbsucker\iface{

		use \stange\fbsucker\iface\Log as LoggingInterface;

		interface Cache{

			/** set a logging class **/
			public function setLog(LoggingInterface $log);

			/** Load cache **/
			public function load($name);

			/** Save cache **/
			public function save($name,$value);

			/** Save OR load cache **/
			public function sload($name,$value);


		}

	}		

