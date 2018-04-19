<?php 

	/**
	* CRUD - MYSQL
	* Developer: JUAN DAVID LEON
	* Developer WebSite: http://www.jdevweb.com 
	*/

	class CRUD extends Sqlconsult
	{
		public static function find($table, $args = [])
		{	

			if (!isset($args['columns'])) {
				$args['columns'] = '*';
			}

			$sql = "SELECT $args[columns] FROM $table ";

			// INNER JOIN - LEFT JOIN - RIGHT JOIN - FULL JOIN
			if (isset($args['join']) && is_array($args['join'])) {
				$sql .= self::join($args['join']);
			}

			// Where conditional
			$params = [];
			if ( isset($args['where']) ) {
				$sql .= " WHERE ".$args['where'];
				if (!isset($args['where_values'])) {
					$args['where_values'] = [];
				}
				$params = self::configParams( $args['where_values'] );
			}			

			// Order by conditional
			if (isset($args['order'])) {
				$sql .= " ORDER BY ".$args['order'];
			}
			
			// limit conditional
			if (isset($args['limit'])) {
				$sql .= " LIMIT ".$args['limit'];
			}
			return parent::consultaBd($sql,$params);
		}

		public static function all( $table, $args = [] )
		{	
			$all = [];
			$consult = self::find($table,$args);
			while ($data = $consult[1]->fetch_object()) {
				array_push($all,$data);
			}
			return $all;
		}
	
		public static function count_all( $table, $args = [] )
		{	
			$args['columns'] = 'COUNT(*) as total';
			$consult = self::find($table,$args);	
			$data = $consult[1]->fetch_object();		
			return $data->total;
		}
		
		public static function insert($table,$data=[],$unique=null)
		{	
			/*
				$data = [ 'row' => 'dataToInsert'];	
				$unique = ['where' => '', 'where_values' => [val1, val2, val3 ...]]	
			*/
				
			$sql = "INSERT INTO $table ";
			$sql .= "(".implode(', ', array_keys($data)).") VALUES ('".implode("', '", $data). "')";

			if ($unique != null) {
				$valUnique = self::unique($table,$unique);
				if ($valUnique > 0) {
					return false;
				}elseif ($valUnique == 0) {
					return parent::consultaBd($sql,[]);					
				}
			}

			return parent::consultaBd($sql,[]);
		}

		public static function delete($table,$where,$where_values=[])	
		{
			$sql = "DELETE FROM $table WHERE $where";
			$params = self::configParams($where_values);
			return parent::consultaBd($sql,$params);
		}

		/*
			// falseDelete() used to save one date of delete form an element without delete it necesarilly update tm_delete from null to CURRENT_TIME
		*/
		public static function falseDelete($table,$where,$where_values=[])
		{	
			$set = ['tm_delete' => date("Y-m-d H:i:s")];
			return self::update($table,$set,$where,$where_values);
		}

		public static function update($table,$set,$where,$where_values = [])	
		{	
			/*
				Set = Data to insert = ['key' => 'value']
				$where = Conditional,
				$where_values = Values of the where conditional
			*/
			$insert = '';
			$type = '';
			$params2 = [];
			foreach ($set as $key => $value) {
				if ($value === NULL) {
					$insert .= ' '.$key.' = NULL, '; 
				}else{
					$insert .= ' '.$key.' = ?, ';  
				}
				if (self::typeChart($value) != false) {
					$type .= self::typeChart($value);					
				}
			}
			$data = substr($insert, 0, -2);

			foreach ($where_values as $p) {
				$type .= self::typeChart($p); 
			}

			if ($type != '') {
	    		array_push($params2, $type); 
	    	}

			foreach ($set as $value) {
				if ($value !== NULL) {
					array_push($params2, $value);
				}
			}

			foreach ($where_values as $p) {
				array_push($params2,$p);
			}

			$sql = "UPDATE $table SET $data WHERE $where";

			return parent::consultaBd($sql,$params2);
		}

		public static function unique($table,$conditional)
		{	
			$args = array(
				'where' => $conditional['conditional'],
				'where_values' => $conditional['where_values'],
			);

			$uniqueData = self::find( $table, $args );
			return $uniqueData[1]->num_rows;			
		}


		static public function configParams($values = [])
		{	
			$type = '';
			$vals = [];
			$params = [];
			foreach ( $values as $vl ) {				
				if (self::typeChart($vl) != false) {
					$type .= self::typeChart($vl);					
				}
				$vals[] = $vl;
			}

			if(count($values) > 0){
				array_push($params,$type);
			}

			return array_merge($params,$vals);
		}

		// Characterizes the value as string, Int or NULL
		static public function typeChart($value)
		{	
			if ($value === NULL) {
				return '';
			}
			if (is_numeric($value)) {
				return 'i';
			}
			if (is_string($value)) {
				return 's';
			}
			return false;
		}

		public static function join($data=[])
		{	
			// Metodo para almacenar condiciones JOIN 0=Method / 1=table / 2=condition
			$queryJoin = '';
			foreach ($data as $query) {
				$queryJoin .= $query[0].' JOIN '. $query[1].' ON '.$query[2].' ';
			}
			return $queryJoin;
		}
	}

