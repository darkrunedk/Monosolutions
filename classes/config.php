<?php

	class config {
	
		public static function get($path = null) {
			if($path) {
				$config = $GLOBALS['config'];
				$path = explode("/", $path);
				
				foreach($path as $bit) {
					if(in_array($config[$bit], $config)) {
						$config = $config[$bit];
					} else {
						die("The config setting doesn't exist!");
					}
				}
				
				return $config;
			}
			
			return false;
		
		}
	
	}

?>