<?php

	namespace stange\fbsucker\http\iface{

		interface Adapter{

			public function request($url,$method="GET");

		}

	}
