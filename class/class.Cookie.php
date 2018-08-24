<?php 

	/**
	* Cookies
	*/
	class Cookie
	{	

		public static function readCookie($cookieName)
		{	
			if(isset($_COOKIE[$cookieName])) {
			    return unserialize($_COOKIE[$cookieName]);
			}else{
				return false;
			}
		}

		static public function find($cookieName,$column=[])
		{
			/*
				$column = [ key => value ];
			*/
			$cookie = self::readCookie($cookieName);
			$key = array_keys($column)[0];
			$value = $column[array_keys($column)[0]];
			$indice = array_search($value, array_column($cookie, $key));
			
			return $cookie[$indice];
		}

		public static function searchInCookie($arrayCookie=[],$dataToCompare,$compare)
		{	
			$cantidadDatosRepetidos = 0;
			foreach ($arrayCookie as $key => $value) {
				if ($value[$dataToCompare] == $compare) {
					$cantidadDatosRepetidos++;
				}
			}
			return $cantidadDatosRepetidos;
		}

		public static function insertInCookie($cookieName,$keyDataArray=[],$seconds = 2592000,$unique = null)
		{		
			/*	
				$keyDataArray = [ ['key','data'] ];
				$seconds = Seconds Cookie will be before destroy ifself
				$unique = Define if there are data that would be unique in DB.
			*/
			$dataCookie = array();
			$dataCookie = self::readCookie($cookieName);

			if ($dataCookie != false) {				
				$uni = 0;
				if ($unique != null) {
					foreach ($keyDataArray as $value) {
						$uni = $uni + self::searchInCookie($dataCookie,$unique,$value[$unique]);
					}			
				}
				if ($uni > 0) {
					return false;
				}	
			}

		    if (count($dataCookie) == 0) {
		    	$dataCookie = [];
		    }
	
			// Anidando a la lista de deseos
			if (count($dataCookie) > 0) {
				$newCookie = array_merge($dataCookie,$keyDataArray);
			}else{
				$newCookie = $keyDataArray;
			}
			
			self::createCookie($cookieName,$newCookie,$seconds);			
			
			return true;
		}

		public static function createCookie($cookieName,$arrayCookie,$seconds = 2592000)
		{
			 $iTemCad = time() + $seconds;
			return setcookie($cookieName, serialize($arrayCookie), $iTemCad,'/');
		}

		public static function updateCookie($cookieName,$keyCompare=[],$valueToInsert=[])
		{
			/*
				$keyCompare = [ 'key' => 'value' ];
				$valueToInsert = [ 
					['key' => 'data' ]
				];
			*/
			$cookie = self::readCookie($cookieName);

			if ($cookie == null) {
				return false;
			}

			$contar = 0;
			foreach ($cookie as $valueC) {
				if ( $valueC[array_keys($keyCompare)[0]] == $keyCompare[array_keys($keyCompare)[0]] ) {
					foreach ( $valueToInsert as $keyInsert => $dataInsert ) {
						$cookie[$contar][$keyInsert] = $dataInsert;
					}
				}
				$contar++;
			}
			
			return self::createCookie($cookieName,$cookie);
		}

		public static function deleteCookie($cookieName)
		{
			$timeDelete = time() - 12;
	 		unset($_COOKIE[$cookieName]);
			setcookie($cookieName, null, -1, '/');
			return "Cookie Eliminada";
		}


		static public function delValue($cookieName,$column,$value)
		{
			$cookie = self::readCookie($cookieName);
			$indice = array_search($value, array_column($cookie, $column));

			if ($indice !== false) {
				unset($cookie[$indice]);
				$newCookie = array_values($cookie);
				return self::createCookie($cookieName,$newCookie);
			}
			return false;
		}

	}



 ?>