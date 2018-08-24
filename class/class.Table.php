<?php 



	/**
	* 
	*/
	class Table
	{
		
		static public function createBD($database)
		{
			/*Create a DataBase (Only for localhost)*/
			$bd = 'CREATE DATABASE '.$database;
			$enlace = new mysqli("localhost","root","");

			if ($enlace->query($bd)) {
				echo "Base de datos ".$database.' creada exitosamente';
			}else{
				echo "Ya existe la base de datos ".$database.'. No se requiere ninguna acción.';
			}
			echo "<br><br>";
		}
		
		static public function create( $tb )
		{	
			/*
				$tb = [
					'tablename',
					'rows' => array[
						....
					],
					'foreign' => array [
						['tablecolumn',otherTable(otherColumnTable)]
					]
				];

				$rows = [		
					0 => 'nameRow',
					1 => $type=VARCHAR(30),
					2 => 'unsigned=false',
					3 => 'not null = false',
					4 => 'autoincrement=false',
					5 => 'primary_key=false',
					6 => $default='data'
				]
			*/

			$tableName = $tb['name'];
			$rows = $tb['cols'];


			$sql = "CREATE TABLE $tableName ( ";

			foreach ($rows as $row) {
				$sql.= $row[0].' '.$row[1].' ';
				if ($row[2] == true) {
					$sql.= ' UNSIGNED ';
				}
				if ($row[3] == true) {
					$sql.= ' NOT NULL ';
				}
				if ($row[4] == true) {
					$sql.= ' UNIQUE ';
				}
				if ($row[5] == true) {
					$sql.= ' AUTO_INCREMENT ';
				}
				if ($row[6] == true) {
					$sql.= ' PRIMARY KEY ';
				}
				if ($row[7] != '') {

					if ($row[7] !== 'CURRENT_TIMESTAMP') {
						$row[7] = "'".$row[7]."'";
					}

					$sql.= " DEFAULT ".$row[7]." ";
				}
				$sql .= ', ';
			}

			if (isset($tb['foreign'])) {
				foreach ($tb['foreign'] as $fr) {
					$sql .= ' FOREIGN KEY ('.$fr[0].') REFERENCES '. $fr[1].' ';
					$sql .= ', ';
				}
			} 	

			$sql = substr($sql, 0, -2);

			$sql.= ')';

			$bd = Database::getInstancia();
			$mysqli = $bd->getConnection();

			$crearTabla = $mysqli->query($sql);
			
			if ($crearTabla === true) {
				$estado = 'CREADA';
			}else{				
				$estado = $mysqli->error;

				if ($estado == "Table '".$tableName."' already exists") {
					$estado = 'OK';
				}
			} 
			// echo $sql;
			echo "<table style='width:50%; min-width: 200px; margin: 0 auto; border: 1px solid grey;'>
			  <tr>
			    <td>$tableName</td>
			    <td style='float: right;'>$estado</td> 
			  </tr>
			</table>";			
		}

		static public function delete($tables=[])
		{
			$sql = 'DROP TABLE IF EXISTS ';

			foreach ($tables as $table) {
				$sql .= $table.', ';
			}

			$sql = substr($sql, 0, -2);

			$bd = Database::getInstancia();
			$mysqli = $bd->getConnection();

			return $mysqli->query($sql);
		}

		static public function addColumn($tableName,$column,$type=null,$after=null)
		{
			$sql = "ALTER TABLE $tableName ADD $column ";

			if ($type !== null) {
				$sql .= $type;
			}

			if ($after !== null) {
				$sql .= " AFTER $after";
			}

			$bd = Database::getInstancia();
			$mysqli = $bd->getConnection();

			$addColumn = $mysqli->query($sql);
			if ($addColumn) {
				echo "Columna ".$column." insertada";
				echo "<br>";
			}
		}

		static public function editColumn($table,$old_name_column,$new_name_column,$type=null)
		{
			$sql = "ALTER TABLE $table CHANGE COLUMN $old_name_column $new_name_column ";

			if ($type !== null) {
				$sql .= $type;
			}

			$bd = Database::getInstancia();
			$mysqli = $bd->getConnection();

			$editColumn = $mysqli->query($sql);

			if ($editColumn) {
				echo "Columna <b>".$old_name_column."</b> de la tabla <b> ".$table."</b> ha cambiado a <b> ".$new_name_column.'</b>';
				echo "<br>";
			}
		}
		
		static public function truncate($tableName)
		{	
			$sql = "TRUNCATE ".$tableName;
			return Sqlconsult::consultaBd($sql,[]);
		}

	}
