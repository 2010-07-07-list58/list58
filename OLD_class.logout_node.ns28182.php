<?php

require_once dirname(__FILE__).'/class.node.ns32056.php';

class logout_node__ns28182
		extends node__ns32056 {
	
	public function __construct() {
		parent::__construct();
		
		$this->_logout_node__logout();
	}
	
	public function _logout_node__logout() {
		$_SESSION['authorized'] = FALSE;
	}
	
	public function _node__get_head() {
		$html = '';
		
		$html .= parent::_node__get_head();
		
		$html .= 
			'<meta http-equiv="Refresh" content="3;url='.htmlspecialchars(
					$_SERVER['SCRIPT_NAME']
				).'" />';
		//$html .= 
		//	'<script type="text/javascript" src="/media/js/logout_node.js"></script>'.
		//	'<link rel="stylesheet" type="text/css" href="/media/css/logout_node.css" />';
		
		return $html;
	}
	
	public function _node__get_content() {
		$html = '';
		
		$html .=
			'<div class="Margin10Px TextAlignCenter SuccessColor">'.
				'Выход осуществлён!'.
			'</div>';
		
		
		return $html;
	}
}

class logout_node_type__ns28182
		extends node_type__ns32056 {
	public function __construct($obj) {
		parent::__construct($obj);
	}
	public static function new_obj() {
		return new self(new logout_node__ns28182());
	}
}


