<?php 
	session_start();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<title>Admin</title>

	<style>
		
		body {
			padding: 10px;
			font-size: 17px;
		}

		.menu {
			white-space: pre;
		}

		.menu .submenu,
		.menu .item {
			margin-left: 1rem;
		}

		.menu > .item,
		.menu > .item > .submenu {
			margin-left: 0;
		}

		a {
			color: #2d9dfe;			
			text-decoration: none;			
		}

		a:hover {
			text-decoration: underline;
		}

	</style>

</head>
<body>

<?php 
	if ( !$_SESSION['id'] ):
?>

	<form id="doAuth" action="func/assets/sign_in.php" method="POST">

		<h3>Авторизация</h3>

	    <input type="text" name="auth_login" value="admin" placeholder="Login" required autofocus>

	    <input type="password" name="auth_password" value="admin" placeholder="Password" required>

	    <button name="do_auth" type="submit">Вход</button>
	    <button name="do_reg" type="submit">Регистрация</button>

	</form>

<?php
	else:
?>

	<form id="doAuth" action="func/assets/sign_in.php" method="POST">
		<button name="outAuth" type="submit">Выйти</button>
	</form>
	<br>
	<a href="pages/add_cat.php">Добавить категорию</a>
	<hr>
	<p>Разделы</p>

<?php

	require 'func/libs/rb-mysql.php';
	require 'func/assets/db_connect.php';
	require 'func/assets/sub_html.php';

	$menus = R::find('menu', 'admin_id = ' . $_SESSION['id']);

	if ( $menus ) {

		$sections = R::exportAll($menus);

		$cats_menu = get_html_cats($sections, '', '', '');	

		echo $cats_menu;

	}

	endif;

?>
	
</body>
</html>