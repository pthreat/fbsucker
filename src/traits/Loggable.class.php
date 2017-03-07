<?php

	namespace stange\fbsucker\traits{

		use \stange\fbsucker\iface\Log as LogInterface;	

		trait Loggable{

			private	$log	=	NULL;

			public function setLog(LogInterface $log){

				if(method_exists('prepend',$log)){

					$log->prepend(__CLASS__);

				}

				if(method_exists('setPrepend',$log)){

					$log->setPrepend(__CLASS__);

				}

				$this->log	=	$log;
				return $this;

			}

			public function getLog(){

				return $this->log;

			}

			protected function log($msg,$log='log'){

				if(!$this->log){

					return;

				}

				switch($type){

					case 'info':
					case 'error':
					case 'emergency':
					case 'debug':
					case 'success':
					case 'log':
						return $this->log->$type($msg);
					break;

					default:
						throw new \BadMethodCallException("Invalid loggin method $type");
					break;

				}

			}

		}

	}
