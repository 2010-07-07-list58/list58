<?php

require_once dirname(__FILE__).'/class.node.ns32056.php';
require_once dirname(__FILE__).'/class.db.ns5547.php';

class activation_node__ns24059
		extends node__ns32056 {
	
	public $_activation_node__db;
	public $_activation_node__activation_error = NULL;
	
	public function __construct($db) {
		parent::__construct();
		
		$this->_activation_node__db = $db;
		$this->_activation_node__activation();
	}
	
	public function _activation_node__activation() {
		$mail = $this->_node__get_arg('mail');
		$activation_key = $this->_node__get_arg('activation_key');
		
		try{
			$this->_activation_node__db->activation(
				$mail, $activation_key);
		} catch(registration_error__ns5547 $e) {
			$error = $e->getMessage();
			
			$this->_activation_node__activation_error = $error;
		}
	}
	
	public function _node__get_head() {
		$html = '';
		
		$html .= parent::_node__get_head();
		
		if(!$this->_activation_node__activation_error) {
			$html .= 
				'<meta http-equiv="Refresh" content="3;url='.htmlspecialchars(
						$_SERVER['SCRIPT_NAME']
					).'" />';
		}
		//$html .= 
		//	'<script type="text/javascript" src="/media/js/activation_node.js"></script>'.
		//	'<link rel="stylesheet" type="text/css" href="/media/css/activation_node.css" />';
		
		return $html;
	}
	
	public function _node__get_content() {
		$html = '';
		
		if(!$this->_activation_node__activation_error) {
			$html .=
				'<div class="Margin10Px TextAlignCenter SuccessColor">'.
					'Спасибо за активацию вашей электронной почты!'.
				'</div>';
		} else {
			$html .=
				'<div class="Margin10Px TextAlignCenter ErrorColor">'.
					htmlspecialchars('Ошибка: '.
						$this->_activation_node__activation_error).
				'</div>';
		}
		
		return $html;
	}
}

class activation_node_type__ns24059
		extends node_type__ns32056 {
	public function __construct($obj) {
		parent::__construct($obj);
	}
	public static function new_obj($db) {
		return new self(new activation_node__ns24059($db));
	}
}


