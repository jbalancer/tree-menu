<?php

	function get_html_cats($sections, $tpe, $sel, $place) {

		function create_submenu_html($menu, $subs, $lns, $ln, $nd, $type, $selected) {

			$found_state = false;

			if ( $type != 'sel' ) {
				$cur_sub = '<div class="submenu">';
			} else {
				$cur_sub = '';
			}

			$lns .= $ln;

			for ($e = 0; $e < count($subs); $e++) {				

				if ( $menu['id'] == $subs[$e]['sub_id'] ) {

					$found_state = true;			

					if ( $type != 'sel' ) {

						$cur_sub .= '<div class="item">';
						$cur_sub .= '<div class="text">' . $lns . $nd . '<a href="pages/sections.php?cat=' . $subs[$e]['id'] . '">' . $subs[$e]['title'] . '</a><a style="padding-left: 10px; color: #FF8080" href="pages/sections.php?del=del&cat=' . $subs[$e]['id'] . '">&#10006;</a></div>';
						$cur_sub .= '</div>';

					} else {

						$seld = '';

						if ( $subs[$e]['id'] == $selected ) {
							$seld = 'selected';
						}

						$cur_sub .= '<option value="' . $subs[$e]['id'] . '" ' . $seld . '>' . $lns . $subs[$e]['title'] . '</option>';

					}

					if ( !empty($subs[$e]) ) {		

						$now_sub = create_submenu_html($subs[$e], $subs, $lns, $ln, $nd, $type, $selected);

						if ( $now_sub !== false ) {

							$cur_sub .= $now_sub;

						}
						
					}
					
				}
			}

			if ( $type != 'sel' ) {
				$cur_sub .= '</div>';
			}

			if ( $found_state === true ) {
				return $cur_sub;
			} else {
				return false;
			}

		}

		$menus = array();
		$subs_count = count($sections);

		for ($i = 0; $i < $subs_count; $i++) {

			if ( intval($sections[$i]['sub_id']) === 0 ) {

				array_push($menus, $sections[$i]);

				unset($sections[$i]);

			}

		}

		$sections = array_values($sections);

		if ( $tpe != 'sel' ) {

			$menu_html = '<div class="menu">';

			for ($i = 0; $i < count($menus); $i++) {

				$menu_html .= '<div class="item">';
				$menu_html .= '<div class="text">   -   <a href="pages/sections.php?cat=' . $menus[$i]['id'] . '">' . $menus[$i]['title'] . '</a><a style="padding-left: 10px; color: #FF8080" href="pages/sections.php?del=del&cat=' . $menus[$i]['id'] . '">&#10006;</a></div>';
				$menu_html .= create_submenu_html($menus[$i], $sections, '', '   ', '-   ', '', '');
				$menu_html .= '</div>';

			}

			$menu_html .= '</div>';

			return $menu_html;
			
		} else {

			$shtml = '<select name="' . $place . '"><option value="0">Новый раздел</option>';

			for ($i = 0; $i < count($menus); $i++) {

				$seld = '';

				if ( $menus[$i]['id'] == $sel ) {
					$seld = 'selected';
				}

				$shtml .= '<option value="' . $menus[$i]['id'] . '" ' . $seld . '>' . $lns . $menus[$i]['title'] . '</option>';
				$shtml .= create_submenu_html($menus[$i], $sections, '', '--- ', '', 'sel', $sel);

			}

			$shtml .= '</select>';

			return $shtml;

		}

	}

?>