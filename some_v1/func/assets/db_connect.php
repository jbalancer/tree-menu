<?php 

	R::setup('mysql:host=localhost;dbname=id12249841_some_admin_db', 'id12249841_plagire', 'tyx326e5');

	if ( !R::testConnection() ) {
	    exit ('Нет соединения с базой данных');
	}

	R::freeze(true);

?>