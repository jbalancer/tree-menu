<?php

	session_start();

	if ( !$_SESSION['id'] ) {
		header('Location: /index.php');
	}

	require '../func/libs/rb-mysql.php';
	require '../func/assets/db_connect.php';

?>

	<form id="doAuth" action="../func/assets/sign_in.php" method="POST">
		<button name="outAuth" type="submit">Выйти</button>
	</form>
	<a href="../index.php">На главную</a>
	<hr>

<?php

	$all_menus = R::find('menu', 'admin_id = ' . $_SESSION['id']);

	if ( $all_menus ):

		require '../func/assets/sub_html.php';
		
		$all_menus = R::exportAll($all_menus);

		$cats_menu = get_html_cats($all_menus, 'sel', $cur_cat->sub_id, 'sub_place');

?>

	<form action="../func/assets/menu.php" method="POST">
		<h4>Добавление раздела</h4>
		<?php echo $cats_menu ?>		
		<input type="text" placeholder="Название" name="val">
		<input type="submit" value="Добавить" name="add_menu">
	</form>

<?php
	endif;
?>