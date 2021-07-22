<?php

	$connect_data = array(
		'local' => array(
			'127.0.0.1',
			'some_admin_db',
			'root',
			''
		),
		'server' => array(
			'localhost',
			'id12249841_some_admin_db',
			'id12249841_plagire',
			'tyx326e5'
		)
	);

	$cur_connect = $connect_data['local'];

	R::setup('mysql:host=' . $cur_connect[0] . ';dbname=' . $cur_connect[1], $cur_connect[2], $cur_connect[3]);

	if ( !R::testConnection() ) {
	    exit('Нет соединения с базой данных');
	}

	R::freeze(true);

?>