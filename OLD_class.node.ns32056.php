<?php

class node__ns32056 {
	public function impl() {
		return $this;
	}
	
	public function __construct() {}
	
	public function _node__get_arg($arg_name) {
		if(array_key_exists($arg_name, $_GET)) {
			$arg_value = stripslashes($_GET[$arg_name]);
			
			return $arg_value;
		} else {
			return NULL;
		}
	}
	
	public function _node__post_arg($arg_name) {
		if(array_key_exists($arg_name, $_POST)) {
			$arg_value = stripslashes($_POST[$arg_name]);
			
			return $arg_value;
		} else {
			return NULL;
		}
	}
	
	public function _node__get_title() {
		return 'GALAXY - METEOR';
	}
	
	public function _node__get_head() {
		$html = '';
		
		$html .=
			'<meta http-equiv="X-UA-Compatible" content="chrome=1" />'.
			'<script type="text/javascript" src="/media/js/google-chrome-frame-for-microsoft-ie-with-cancel.js"></script>'.
			'<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />'.
			'<title>'.htmlspecialchars($this->_node__get_title()).'</title>'.
			'<link rel="stylesheet" type="text/css" href="/media/css/style.css" />'.
			'<script type="text/javascript" src="/media/js/jquery-1.4.2.js"></script>'.
			'<link rel="stylesheet" type="text/css" '.
				'href="/media/js/jquery.fancybox-1.3.1/fancybox/jquery.fancybox-1.3.1.css" />'.
			'<script type="text/javascript" '.
				'src="/media/js/jquery.fancybox-1.3.1/fancybox/jquery.fancybox-1.3.1.js"></script>';
		
		return $html;
	}
	
	public function _node__get_menu() {
		$menu = array();
		
		$menu[] = array('menu_name' => 'Начало', 
			'menu_link' => $_SERVER['SCRIPT_NAME']);
		
		$menu[] = array('menu_name' => 'Инфо', 
			'menu_link' => $_SERVER['SCRIPT_NAME'].'?node=info');
		
		if(!$_SESSION['authorized']) {
			$menu[] = array('menu_name' => 'Вход',
				'menu_link' => $_SERVER['SCRIPT_NAME'].'?node=login');
		} else {
			$menu[] = array('menu_name' => 'Выход ['.$_SESSION['reg_data']['nickname'].']',
				'menu_link' => $_SERVER['SCRIPT_NAME'].'?node=logout');
		}
		
		return $menu;
	}
	
	public function _node__get_menu_widget() {
		$menu = $this->_node__get_menu();
		
		$htmls = array();
		
		foreach($menu as $menu_item) {
			$htmls[] = 
				'<a href="'.htmlspecialchars($menu_item['menu_link']).'" >'.
					htmlspecialchars($menu_item['menu_name']).
				'</a> ';
		}
		
		$html = '';
		
		$html .= ': '.join(' : ', $htmls).' :';
		
		return $html;
	}
	
	public function _node__get_content() {
		throw new Exception('Abstract function');
	}
	
	public function _node__get_body() {
		$html = '';
		
		$html .=
			'<table class="Width100Per Height100Per">'.
				'<tr>'.
					'<td class="Padding10Px TextAlignLeft"></td>'.
					'<td class="Width100Per Padding10Px TextAlignCenter">'.
						$this->_node__get_menu_widget().
					'</td>'.
					'<td class="Padding10Px TextAlignRight"></td>'.
				'</tr>'.
				'<tr>'.
					'<td colspan="3" class="Height100Per Padding10Px">'.
						'<table class="MarginAuto">'.
							'<tr>'.
								'<td>'.
									$this->_node__get_content().
								'</td>'.
							'</tr>'.
						'</table>'.
					'</td>'.
				'</tr>'.
				'<tr>'.
					'<td class="Padding10Px TextAlignLeft"></td>'.
					'<td class="Width100Per Padding10Px TextAlignCenter"></td>'.
					'<td class="Padding10Px TextAlignRight"></td>'.
				'</tr>'.
			'</table>';
		
		$html .= '<div class="Padding5Px"></div>'; // ЭТО -- дополнительное пустое пространство,
		// 	для того чтобы полоса прокрутки была ПОСТОЯННО.
		// 	так как Fancybox при своей работе -- вызывает
		// 	ЕЩЁ ДОПОЛНИТЕЛЬНОЕ паразитное (bug) пустое пространство,
		// 	что может приводить к динамическому появляению/исчезновению полосы прокрутки
		
		return $html;
	}
	
	public function _node__get_html() {
		$html = '';
		
		$html .=
			'<!DOCTYPE html>'."\n".
			'<html>'.
			'<head>'.
				$this->_node__get_head().
			'</head>'.
			'<body>'.
				$this->_node__get_body().
			'</body>'.
			'</html>';
		
		return $html;
	}
}

class node_type__ns32056 {
	public $obj_impl;
	public function impl() {
		return $this->obj_impl;
	}
	public function __construct($obj) {
		$this->obj_impl = $obj->impl();
	}
	public static function new_obj() {
		return new self(new node__ns32056());
	}
	
	public function get_html() {
		return $this->obj_impl->_node__get_html();
	}
}


