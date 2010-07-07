<?php

require_once dirname(__FILE__).'/class.node.ns32056.php';

class home_node__ns28614
		extends node__ns32056 {
	public function __construct() {
		parent::__construct();
	}
	
	public function _node__get_head() {
		$html = '';
		
		$html .= parent::_node__get_head();
		
		$html .= 
			'<script type="text/javascript" src="/media/js/home_node.js"></script>'.
			'<link rel="stylesheet" type="text/css" href="/media/css/home_node.css" />';
		
		return $html;
	}
	
	public function _node__get_content() {
		$html = '';
		
		$html .=
			'<div class="TextAlignCenter">'.
				'<div class="Margin15Px">'.
					'<img src="/media/img/home_node/infometeor.jpg" '.
						'alt="" />'.
				'</div>'.
				'<div class="Margin15Px _HomeNode__RegistrationButtons">'.
					'<a id="_home_node__ragistration_button" '.
							'href="'.htmlspecialchars(
							$_SERVER['SCRIPT_NAME'].'?node=registration'
						).'">Регистрация</a>'.
					' :: '.
					'<a href="'.htmlspecialchars(
							$_SERVER['SCRIPT_NAME'].'?node=info'
						).'">Информация</a>'.
				'</div>'.
			'</div>';
		
		return $html;
	}
}

class home_node_type__ns28614
		extends node_type__ns32056 {
	public function __construct($obj) {
		parent::__construct($obj);
	}
	public static function new_obj() {
		return new self(new home_node__ns28614());
	}
}


