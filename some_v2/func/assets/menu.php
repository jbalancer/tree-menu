<?php

	session_start();

	if ( !$_SESSION['id'] ) {
		header('Location: /index.php');
	}

	function find_subs($menu, $subs) {

		$found_items = array();
		$found_state = false;

		for ($t = 0; $t < count($subs); $t++) {

			if ( $subs[$t]['sub_id'] == $menu['id'] ) {

				$found_items[] = $subs[$t]['id'];

				$found_state = true;

				if ( !empty($subs[$t]) ) {
					
					$now_item = find_subs($subs[$t], $subs);

					if ( $now_item !== false ) {

						$found_items = array_merge($found_items, $now_item);

					}

				}
				
			}
			
		}

		if ( $found_state === true ) {
			return $found_items;
		} else {
			return false;
		}

	}	

	require '../libs/rb-mysql.php';
	require 'db_connect.php';

	if ( isset($_POST['add_menu']) ) {

		$menu_title = trim($_POST['val']);
		$sub_id = trim($_POST['sub_place']);

		if ( $menu_title != '' && $sub_id != '' ) {

			$found_menu = R::findOne('menu', 'LOWER (`title`) LIKE (?)', array($menu_title));

			if ( !$found_menu ) {

				if ( intval($sub_id) == 0 ) {

					$menu = R::dispense('menu');
					$menu->admin_id = $_SESSION['id'];
					$menu->title = $menu_title;
					$menu->sub_id = 0;
					array_push($_SESSION['have_menus'], R::store($menu));

					header('Location: /index.php');
					
				} else {

					if ( in_array($sub_id, $_SESSION['have_menus']) ) {

						$submenu = R::dispense('menu');
						$submenu->admin_id = $_SESSION['id'];
						$submenu->title = $menu_title;
						$submenu->sub_id = $sub_id;
						array_push($_SESSION['have_menus'], R::store($submenu));					

						header('Location: /index.php');
						
					} else {
						exit('Такого раздела не существует! <a href="/pages/add_cat.php">Вернуться</a>');
					}

				}
				
			} else {
				exit('Раздел "' . $menu_title . '" уже сущетсвует! <a href="/pages/add_cat.php">Вернуться</a>');
			}

		}

		exit('Введите название раздела! <a href="/pages/add_cat.php">Вернуться</a>');
		
	} elseif ( isset($_POST['do_id']) ) {

		$all_menu_id = trim($_POST['do_id']);

		if ( in_array($all_menu_id, $_SESSION['have_menus']) ) {

			if ( isset($_POST['change_menu']) ) {

				$sub_menu_id = trim($_POST['sub_id']);

				if ( intval($sub_menu_id) == 0 ) {

					$now_menu = R::load('menu', $all_menu_id);
					$now_menu->sub_id = 0;
					$now_menu_id = R::store($now_menu);

					header('Location: /pages/sections.php?cat=' . $now_menu_id);

				}

				$all_menu = R::find('menu', 'admin_id = ' . $_SESSION['id']);
				$all_menu = R::exportAll($all_menu);
				$cur_menu = array();
				$sub_menu = array();
				$sub_near = array();

				for ($i = 0; $i < count($all_menu); $i++) {

					if ( $all_menu[$i]['id'] == $all_menu_id ) {
						$cur_menu = $all_menu[$i];
					}

					if ( $all_menu[$i]['id'] == $sub_menu_id ) {
						$sub_menu = $all_menu[$i];
					}					

				}

				if ( !$sub_menu || !$cur_menu ) {
					exit('Неожиданная ошибка!');
				}

				if ( $sub_menu['id'] == $cur_menu['id'] ) {
					exit('Выберите др. раздел!');
				}

				for ($i = 0; $i < count($all_menu); $i++) { 

					if ( $cur_menu['id'] == $all_menu[$i]['sub_id'] ) {
						$sub_near[] = $all_menu[$i]['id'];
					}

				}

				$change_menu = R::load('menu', $all_menu_id);
				$change_menu->sub_id = $sub_menu['id'];
				$change_menu_id = R::store($change_menu);
				

				if ( $_POST['allow'] == 'no' && count($sub_near) > 0 ) {
					R::exec('UPDATE `menu` SET `sub_id` = 0 WHERE `id` IN (' . implode(',', $sub_near) . ')');
				}

				header('Location: /pages/sections.php?cat=' . $change_menu_id);
				
			}

			$cur_val = R::load('menu', $all_menu_id);

			if ( isset($_POST['edit_menu']) ) {

				$new_val = trim($_POST['val']);

				if ( $new_val != '' && $cur_val->title != $new_val ) {

					$all_titles = R::getCol('SELECT `title` FROM `menu` WHERE `admin_id` = ' . $_SESSION['id']);

					if ( !in_array($new_val, $all_titles) ) {

						$cur_val->title = $new_val;
						$cur_val_id = R::store($cur_val);

						header('Location: /pages/sections.php?cat=' . $cur_val_id);
						
					} else {
						exit('Такой под/-раздел уже сущетсвует! <a href="/index.php">Вернуться</a>');
					}


				} else {
					exit('Введите др. название! <a href="/index.php">Вернуться</a>');
				}
				
			} elseif ( isset($_POST['del_menu']) ) {

				$del_menu = $cur_val->export();				
				$del_subs = R::find('menu', 'admin_id = ' . $_SESSION['id']);				
				$del_subs = R::exportAll($del_subs);
				$del_subs = find_subs($del_menu, $del_subs);
				$all_dels = array($del_menu['id']);

				if ( $del_subs ) {

					$all_dels = array_merge($all_dels, $del_subs);

				}

				R::exec('DELETE FROM `menu` WHERE `id` IN ('. implode(',', $all_dels) .')');

				header('Location: /index.php');

			}

		}

		exit('Ошибка! <a href="/index.php">Вернуться</a>');

	}

?>