<?php 
	session_start();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<title>Admin</title>

	<!--<link rel="stylesheet" href="libs/bootstrap/css/bootstrap.min.css">-->
	 <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"> 

	<!--<link rel="stylesheet" href="icons/line_icons/css/line_icons.min.css">-->
	 <link href="https://cdn.lineicons.com/1.0.1/LineIcons.min.css" rel="stylesheet"> 

	<link rel="stylesheet" href="assets/css/style.css">

	<!--<script defer src="libs/jquery/js/jquery.min.js"></script>-->
	 <script defer src="https://code.jquery.com/jquery-3.4.1.min.js"></script> 

	<!--<script defer src="libs/bootstrap/js/bootstrap.min.js"></script>-->
	 <script defer src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script> 
	
	<script defer src="assets/js/msg.js"></script>
	<script defer src="assets/js/script.js"></script>
</head>
<body>

<?php 
	if ( !$_SESSION['id'] ):
?>

	<form id="doAuth" action="func/assets/sign_in.php" method="POST" class="form-signin">

		<h3 class="text-center mb-3">Авторизация</h3>

	    <div class="form-label-group mx-auto my-lg-2">
	        <input type="text" class="form-control" name="auth_login" value="admin" placeholder="Login" required autofocus>
	    </div>

	    <div class="form-label-group mx-auto my-lg-2">
	   		<input type="password" class="form-control" name="auth_password" value="admin" placeholder="Password" required>
	    </div>

	    <button class="btn btn-primary btn-block" name="do_auth" type="submit">Вход</button>
	    <button class="btn btn-secondary btn-block" name="do_reg" type="submit">Регистрация</button>

	</form>

<?php
	else:
?>

	<form id="doAuth" action="func/assets/sign_in.php" method="POST" class="form-signin">
		<button class="btn btn-primary" name="outAuth" type="submit">Выйти</button>
	</form>

	<div class="menu m-1">
		<div class="item-add ml-3">
			<div class="content editable">
				<span class="pointer lni-pencil-alt"></span>
				<span class="text">Редактировать</span>
				<span class="indicator off"></span>
			</div>
		</div>
<?php

	require 'func/libs/rb-mysql.php';
	require 'func/assets/db_connect.php';

	$menus = R::find('menu', 'admin_id = ' . $_SESSION['id']);

	if ( $menus ) {

		$subs = R::exportAll($menus);
		$menus = array();
		$menu_html = '';
		$subs_count = count($subs);

		for ($i = 0; $i < $subs_count; $i++) {

			if ( intval($subs[$i]['sub_id']) === 0 ) {

				array_push($menus, $subs[$i]);

				unset($subs[$i]);

			}

		}

		$subs = array_values($subs);

		function create_submenu_html($menu, $subs) {

			$found_state = false;
			$pointer_icon = '<span class="pointer lni-chevron-down"></span>';
			$cur_sub = '<div class="submenu">';

			for ($e = 0; $e < count($subs); $e++) {						

				if ( $menu['id'] == $subs[$e]['sub_id'] ) {

					$found_state = true;

					$cur_sub .= '<div class="item ml-3"><div class="content" data-id="' . $subs[$e]['id'] . '">' . $pointer_icon;
					$cur_sub .= '<span class="text">' . $subs[$e]['title'] . '</span>';
					$cur_sub .= '<div class="btn-icons"><div class="menu_do" data-name="edit_menu" data-id="' . $subs[$e]['id'] . '"><span class="icon lni-pencil"></span></div>';
					$cur_sub .= '<div class="menu_do" data-name="del_menu" data-id="' . $subs[$e]['id'] . '"><span class="icon lni-trash"></span></div>';
					$cur_sub .= '<div class="menu_do" data-name="add_sub" data-id="' . $subs[$e]['id'] . '"><span class="icon lni-plus"></span></div>';
					$cur_sub .= '<div class="menu_do" data-name="in_menu" data-id="' . $subs[$e]['id'] . '"><span class="icon lni-radio-button"></span></div>';
					$cur_sub .= '<div class="menu_do" data-name="change_menu" data-id="' . $subs[$e]['id'] . '"><span class="icon lni-list"></span></div></div></div>';					

					if ( !empty($subs[$e]) ) {

						$now_sub = create_submenu_html($subs[$e], $subs);

						if ( $now_sub !== false ) {

							$cur_sub .= $now_sub;

						}
						
					}

					$cur_sub .= '</div>';
					
				}
			}

			$cur_sub .= '</div>';

			if ( $found_state === true ) {
				return $cur_sub;
			} else {
				return false;
			}

		}

		for ($i = 0; $i < count($menus); $i++) {

			$menu_html .= '<div class="item ml-3"><div class="content" data-id="' . $menus[$i]['id'] . '"><span class="pointer lni-chevron-down"></span>';
			$menu_html .= '<span class="text">' . $menus[$i]['title'] . '</span>';
			$menu_html .= '<div class="btn-icons"><div class="menu_do" data-name="edit_menu" data-id="' . $menus[$i]['id'] . '"><span class="icon lni-pencil"></span></div>';
			$menu_html .= '<div class="menu_do" data-name="del_menu" data-id="' . $menus[$i]['id'] . '"><span class="icon lni-trash"></span></div>';
			$menu_html .= '<div class="menu_do" data-name="add_sub" data-id="' . $menus[$i]['id'] . '"><span class="icon lni-plus"></span></div>';
			$menu_html .= '<div class="menu_do" data-name="in_menu" data-id="' . $menus[$i]['id'] . '"><span class="icon lni-radio-button"></span></div>';
			$menu_html .= '<div class="menu_do" data-name="change_menu" data-id="' . $menus[$i]['id'] . '"><span class="icon lni-list"></span></div></div></div>';
			$menu_html .= create_submenu_html($menus[$i], $subs);
			$menu_html .= '</div>';

		}

		echo $menu_html;

	}

?>

		<div class="item-add form-elem ml-3">
			<input type="text" class="col-md-3 form-control mt-2 mb-2" placeholder="Название раздела">
			<div class="errors"></div>
			<div class="content menu_do" data-name="add_menu">				
				<span class="pointer lni-plus"></span>
				<span class="text">Добавить раздел</span>
			</div>
		</div>

	</div>

<?php
	endif;
?>	
	
</body>
</html>