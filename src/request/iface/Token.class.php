<?php

	namespace stange\fbsucker\request\iface{

		interface Token{

			public function __construct(Array $args = Array());
			public function getToken();
			public function __toString();

		}

	}
