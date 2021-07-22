<?php

	session_start();

	if ( !$_SESSION['id'] || !$_GET['cat'] ) {
		header('Location: /index.php');
	}

	require '../func/libs/rb-mysql.php';
	require '../func/assets/db_connect.php';

	$cur_cat = R::findOne('menu', 'id = ?', array($_GET['cat']));
?>

	<form id="doAuth" action="../func/assets/sign_in.php" method="POST" class="form-signin">
		<button name="outAuth" type="submit">Выйти</button>
	</form>
	<a href="../index.php">На главную</a>
	<hr>

<?php
	if ( isset($_GET['del']) ):
?>

<fieldset>
	<legend>Вы точно хотите удалить "<?php echo $cur_cat->title ?>" ?</legend>
	<form action="../func/assets/menu.php" method="POST">
		<input type="hidden" value="<?php echo $_GET['cat'] ?>" name="do_id">
		<input type="submit" value="Да" name="del_menu">
		<a href="../index.php">Нет</a>
	</form>
</fieldset>

<?php
	
	die;
	endif;

	$all_menus = R::find('menu', 'admin_id = ' . $_SESSION['id']);

	if ( $all_menus ):

		require '../func/assets/sub_html.php';
		
		$all_menus = R::exportAll($all_menus);

		$cats_menu = get_html_cats($all_menus, 'sel', $cur_cat->sub_id, 'sub_id');

?>

	<form action="../func/assets/menu.php" method="POST">
		<h4>Смена раздела</h4>
		<?php echo $cats_menu ?>
		<p>Переместить все подразделы "<?php echo $cur_cat->title ?>" (если есть)</p>
		<label>
			<input type="radio" value="yes" name="allow" checked>
			<span>Да</span>
		</label>
		<label>
			<input type="radio" value="no" name="allow">
			<span>Нет</span>
		</label>
		<input type="hidden" value="<?php echo $cur_cat->id ?>" name="do_id">
		<input type="submit" value="Изменить" name="change_menu">
	</form>
	<hr>
	<form action="../func/assets/menu.php" method="POST">
		<h4>Изменение названия</h4>
		<input type="text" value="<?php echo $cur_cat->title ?>" name="val">
		<input type="hidden" value="<?php echo $cur_cat->id ?>" name="do_id">
		<input type="submit" value="Изменить" name="edit_menu">
	</form>

<?php
	endif;
?>