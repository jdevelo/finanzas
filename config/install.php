<?php 
	require 'config.php';

	/*Create DataBase (Only for LOCALHOST)*/
	Table::createBD('finanzas');	
	/* #Database */

/* =======================================================
    *	
    *	TABLES FROM PROYECT ------------------->
    *	
   		$table[] = [
		    'name' => Table Name,
		    'cols' => [
			        [0,1,2,3,4,5,6,7],***
			        [0,1,2,3,4,5,6,7],
			        ...
			     ],
		    'foreign' => ['self col','other_table(other_table_col)'], (OPTIONAL)
		];

    ***	$col = [		
				0 => NAME ROW = 'string',
				1 => $type = (VARCHAR(30),INT(10),DATETIME()...),
				2 => UNSIGNED = false (DEFAULT),
				3 => NOT NULL = false (DEFAULT),
				4 => UNIQUE = false (DEFAULT),
				5 => AUTOINCREMENT = false (DEFAULT),
				6 => PRIMARY_KEY = false (DEFAULT),
				7 => $default = '' (EMPTY BY DEFAULT)
		]
    *
======================================================= */

/* ==============  TABLES ADJUST  ============= */
	
	function adjustTables()
	{
		// Table::addColumn('pagos','tipo_deuda','TINYINT(1)','id_pagos');
		Table::editColumn('deudas','detalle_concepto','concepto','VARCHAR(150)');
		// Table::editColumn('gastos','fecha','fecha','DATE');



		Table::addColumn('deudas','primer_pago','DATE','dia_pago');
		Table::addColumn('cargos_fijos','primer_pago','DATE','mora');
		Table::addColumn('pagos','n_cuota','SMALLINT(3)','id_deuda');

		Table::addColumn('deudas_balance','ultimo_mes_pagado','CHAR(10)','saldo');
		Table::addColumn('deudas_balance','mora','INT(10) DEFAULT 0','saldo');
		Table::addColumn('cargos_fijos','mora','INT(10) DEFAULT 0','valor');
		Table::addColumn('cargos_fijos','ultimo_mes_pagado','CHAR(10)','id_estado_deuda');
		// Table::truncate('estados_deuda');
	}

/* ==============  #TABLES ADJUST  ============= */



/* ==============  TABLES  ============= */
	$table = [];

	/*-- USERS --*/

		$table[] = [ 
		    'name' => 'users',
		    'cols' => [
			        ['id_user','INT(11)',true,true,false,true,true,''],
			        ['name','VARCHAR(80)',false,true,false,false,false,''],
			        ['last_name','VARCHAR(80)',false,true,false,false,false,''],
			        ['mail','VARCHAR(60)',false,true,true,false,false,''],
			        ['password','VARCHAR(32)',false,true,false,false,false,''],
			        ['tm_create','DATETIME',false,false,false,false,false,'CURRENT_TIMESTAMP'],
			        ['tm_delete','DATETIME',false,false,false,false,false,'']
			    ]
		];

		$table[] = [
		    'name' => 'users_logs',
		    'cols' => [
			        ['id_logs','INT(11)',true,false,false,true,true,''],
			        ['id_user','INT(11)',true,true,true,false,false,''],
			        ['num_logs','TINYINT(2)',true,true,false,false,false,''],
			        ['tm_delete','DATETIME',false,false,false,false,false,'']
			    ],
		    'foreign' => [['id_user','users(id_user)']],
		];

		$table[] = [
			'name' => 'users_psw_restore',
			'cols' => [
					['id_psw_restore','INT(11)',true,true,false,true,true,''],
					['mail','VARCHAR(100)',false,true,true,false,false,''],
					['token','VARCHAR(40)',false,true,false,false,false,''],
					['tm_create','DATETIME',false,true,false,false,false,'CURRENT_TIMESTAMP'],
			        ['tm_delete','DATETIME',false,false,false,false,false,'']
				],
		    'foreign' => [['mail','users(mail)']],
		];

	/*-- #USERS --*/

	/*-- HERRAMIENTAS DE BALANCE --*/		

			$table[] = [ 
				'name' => 'balance_general',
				'cols' => [
						['id_balance','TINYINT(3)',true,false,false,true,true,''],
						['mes','DATE',false,false,false,false,false,''],
						['ingresos','INT(11)',false,true,false,false,false,''],
						['egresos','INT(11)',true,true,false,false,false,''],
						['dinero_actual','INT(11)',true,true,false,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,false,'']
					]	
			];	

			$table[] = [ 
				'name' => 'ingresos',
				'cols' => [
						['id_ingreso','TINYINT(3)',true,false,false,true,true,''],
						['concepto','VARCHAR(35)',false,true,false,false,false,''],
						['valor','MEDIUMINT(8)',true,true,false,false,false,''],
						['fecha','DATETIME',false,false,false,false,false,'CURRENT_TIMESTAMP'],
						['tm_delete','DATETIME',false,false,false,false,false,'']
					]	
			];	

			$table[] = [ 
				'name' => 'conceptos_egresos',
				'cols' => [
						['id_concepto_egreso','TINYINT(3)',true,false,false,true,true,''],
						['concepto','VARCHAR(100)',false,true,true,false,false,''],
						['tm_delete','DATETIME',false,false,false,false,false,''],
					]	
			];

			$table[] = [ 
				'name' => 'egresos',
				'cols' => [
						['id_egresos','TINYINT(3)',true,false,false,true,true,''],
						['id_concepto_egreso','SMALLINT(5)',true,true,true,false,false,''],
						['detalle_concepto','VARCHAR(5)',false,true,false,false,false,''],
						['valor','MEDIUMINT(8)',true,true,true,false,false,''],
						['fecha','DATETIME',false,false,false,false,false,'CURRENT_TIMESTAMP'],
						['tm_delete','DATETIME',false,false,false,false,false,''],
					]	
			];

			$table[] = [ 
				'name' => 'tipos_ahorro',
				'cols' => [
						['id_tipo','SMALLINT(5)',true,false,false,true,true,''],
						['destino','VARCHAR(10)',false,true,true,false,false,''],
						['fecha','DATETIME',false,false,false,false,false,'CURRENT_TIMESTAMP'],
						['tm_delete','DATETIME',false,false,false,false,false,'']
					]
			];

			$table[] = [ 
				'name' => 'ahorro',
				'cols' => [
						['id_ahorro','SMALLINT(5)',true,false,false,true,true,''],
						['id_tipo','TINYINT(3)',true,true,false,false,false,''],
						['valor','MEDIUMINT(8)',true,true,true,false,false,''],
						['fecha','DATETIME',false,false,false,false,false,'CURRENT_TIMESTAMP'],
						['tm_delete','DATETIME',false,false,false,false,false,'']
					],
		    	'foreign' => [['id_tipo','tipos_ahorro(id_tipo)']],
			];

	/*-- #HERRAMIENTAS DE BALANCE --*/

	/*-- MOVIMIENTOS FINANCIEROS --*/
			$table[] = [
				'name' => 'acreedores',
				'cols' => [
					['id_acreedor','INT(11)',true,false,false,true,true,''],
					['banco','VARCHAR(80)',false,true,true,false,false,''],
					['propietario','VARCHAR(100)',false,true,false,false,false,''],
					['fecha','DATETIME',false,false,false,false,false,'CURRENT_TIMESTAMP'],
			        ['tm_delete','DATETIME',false,false,false,false,false,'']
				]
			];

			$table[] = [
				'name' => 'estados_deuda',
				'cols' => [
					['id_estado_deuda','INT(11)',true,false,false,true,true,''],
					['estados','VARCHAR(20)',false,true,false,false,false,''],
			        ['tm_delete','DATETIME',false,false,false,false,false,'']
				],
			];

			$table[] = [
				'name' => 'deudas_balance',
				'cols' => [
					['id_deuda_balance','INT(11)',true,false,false,true,true,''],
					['id_deuda','INT(11)',true,true,false,false,false,''],
					['abonos_capital','INT(10)',true,true,false,false,false,'0'],
					['interes_pagado','INT(11)',true,true,false,false,false,'0'],
					['cuotas_pagas','SMALLINT(5)',true,true,false,false,false,'0'],
					['saldo','INT(11)',true,true,false,false,false,''],
			        ['tm_delete','DATETIME',false,false,false,false,false,'']
				],
		    	'foreign' => [
		    			['id_deuda','deudas(id_deuda)'],
		    		],
			];

			$table[] = [
				'name' => 'deudas',
				'cols' => [
					['id_deuda','INT(11)',true,false,false,true,true,''],
					['id_acreedor','INT(11)',true,true,false,false,false,''],
					['id_concepto','SMALLINT(7)',true,true,false,false,false,''],
					['detalle_concepto','VARCHAR(100)',false,true,false,false,false,''],
					['valor','INT(10)',true,true,false,false,false,''],
					['tasa','FLOAT',true,false,false,false,false,''],
					['cuotas','TINYINT(3)',true,true,false,false,false,''],
					['dia_pago','TINYINT(2)',true,true,false,false,false,''],
					['id_estado_deuda','TINYINT(2)',false,true,false,false,false,1],
					['fecha','DATETIME',false,true,false,false,false,'CURRENT_TIMESTAMP'],
			        ['tm_delete','DATETIME',false,false,false,false,false,'']
				],
		    	'foreign' => [
		    			['id_acreedor','acreedores(id_acreedor)'],
		    			['id_estado_deuda','estados_deuda(id_estado_deuda)'],
		    		],
			];


			$table[] = [
				'name' => 'cargos_fijos',
				'cols' => [
					['id_cargo_fijo','INT(11)',true,false,false,true,true,''],
					['concepto','VARCHAR(100)',false,true,false,false,false,''],
					['valor','INT(10)',true,true,false,false,false,''],
			        ['dia_pago','TINYINT(2)',true,false,false,false,false,''],
					['id_estado_deuda','TINYINT(2)',false,true,false,false,false,'1'],
			        ['tm_delete','DATETIME',false,false,false,false,false,'']
				],
		    	'foreign' => [
		    			['id_estado_deuda','estados_deuda(id_estado_deuda)'],
		    		],
			];

			$table[] = [
				'name' => 'pagos',
				'cols' => [
					['id_pagos','INT(11)',true,false,false,true,true,''],
					['id_deuda','INT(11)',true,true,false,false,false,'0'],
					['id_cargo_fijo','INT(11)',true,true,false,false,false,'0'],
					['abono_capital','INT(10)',true,true,false,false,false,''],
					['pago_intereses','INT(10)',true,true,false,false,false,'0'],
					['pago_total','INT(10)',true,true,false,false,false,''],
					['fecha','DATETIME',false,true,false,false,false,'CURRENT_TIMESTAMP'],
			        ['tm_delete','DATETIME',false,false,false,false,false,'']
				],
		    	'foreign' => [
		    			['id_deuda','deudas(id_deuda)'],
		    			['id_cargo_fijo','cargos_fijos(id_cargo_fijo)'],
		    		],
			];

			$table[] = [
				'name' => 'gastos',
				'cols' => [
					['id_gastos','INT(11)',true,false,false,true,true,''],
					['concepto','VARCHAR(100)',false,true,false,false,false,'0'],
					['valor','INT(11)',true,true,false,false,false,'0'],
					['fecha','DATETIME',false,true,false,false,false,'CURRENT_TIMESTAMP'],
			        ['tm_delete','DATETIME',false,false,false,false,false,'']
				]
			];



	/*-- #MOVIMIENTOS FINANCIEROS --*/

	/*
		2 => UNSIGNED = false (DEFAULT),
		3 => NOT NULL = false (DEFAULT),
		4 => UNIQUE = false (DEFAULT),
		5 => AUTOINCREMENT = false (DEFAULT),
		6 => PRIMARY_KEY = false (DEFAULT)
		7 => $default = '' (EMPTY BY DEFAULT)
	*/

	/*-- MAINTENANCE --*/
		$table[] = [
			'name' => 'maintenance',
			'cols' => [
				['id_maintenance','TINYINT(3)',true,false,false,true,true,''],
				['state','VARCHAR(60)',false,true,false,false,false,''],
				['tm_maintenance','DATETIME',false,false,false,false,false,''],
			    ['tm_delete','DATETIME',false,false,false,false,false,'']
			]
		];

		$table[] = [
			'name' => 'maintenance_ip',
			'cols' => [
				['id_ip','TINYINT(3)',true,false,false,true,true,''],
				['id_maintenance','TINYINT(3)',true,true,false,false,false,''],
				['ip_address','VARCHAR(50)',false,true,true,false,false,''],
			    ['tm_delete','DATETIME',false,false,false,false,false,''],
			],
		    'foreign' => [ ['id_maintenance','maintenance(id_maintenance)'] ],
		];
	/*-- #MAINTENANCE --*/

	foreach ($table as $tb) { Table::create( $tb );	}
	adjustTables();
/* ==============  #TABLES  ============= */

/* ==============  DEFAULT DATA  ============= */
		
	/*-- ADMINS --*/
		$users = [
			['JUAN DAVID','LEON PONCE','jlp25@hotmail.com','ctlb31207'],
		];

		foreach ($users as $user) {
			$set = [
				'name' => $user[0],
				'last_name' => $user[1],
				'mail' => $user[2],
				'password' => Secure::montar_clave_verificacion($user[3]),
			];

			$unique = [
				'conditional' => 'mail = ?',
				'where_values' => [$user[2]]
			];
			CRUD::insert('users',$set,$unique);	
		}	
	/*-- #ADMINS --*/

	/*-- DEBTS --*/
		$debt_states = [
			'activo','atrasado','saldado','pago'
		];

		foreach ($debt_states as $states) {
			$set = [
				'estados' => $states
			];

			$unique = [
				'conditional' => 'estados = ?',
				'where_values' => [$states]
			];
			CRUD::insert('estados_deuda',$set,$unique);
		}
	/*-- #DEBTS --*/

/* ==============  #DEFAULT DATA  ============= */

