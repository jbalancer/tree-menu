<?php 

	session_start();

	if ( isset($_POST['outAuth']) ) {

		$_SESSION = array();

		session_destroy();

		header('Location: /index.php');

	}

	require '../libs/rb-mysql.php';
	require '../assets/db_connect.php';

	$user_login = trim($_POST['auth_login']);
	$user_password = trim($_POST['auth_password']);

	if ( $user_login && $user_password ) {

		$admin = R::findOne('admins', 'login = ?', array($user_login));

		if ( isset($_POST['do_reg']) && !$admin ) {

			$cur_admin = R::dispense('admins');
			$cur_admin->login = $user_login;
			$cur_admin->password = password_hash($user_password, PASSWORD_DEFAULT);
			$_SESSION['id'] = R::store($cur_admin);

		} elseif ( isset($_POST['do_auth']) && $admin ) {		

			if ( password_verify($user_password, $admin->password) ) {

				$_SESSION['id'] = $admin->id;

				$_SESSION['have_menus'] = R::getCol('SELECT `id` FROM `menu` WHERE `admin_id` = ' . $_SESSION['id']);

			}

		}

	}

	header('Location: /index.php');

?>