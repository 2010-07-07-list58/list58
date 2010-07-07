<?php

require_once dirname(__FILE__).'/class.node.ns32056.php';
require_once dirname(__FILE__).'/class.db.ns5547.php';

class registration_error__ns6739
		extends Exception {};

class login_node__ns6739 
		extends node__ns32056 {
	
	public $_login_node__db;
	public $_login_node__login_error = NULL;
	public $_login_node__mail;
	public $_login_node__password;
	
	public function __construct($db) {
		parent::__construct();
		
		$this->_login_node__mail = $this->_node__post_arg('mail');
		$this->_login_node__password = $this->_node__post_arg('password');
		
		$this->_login_node__db = $db;
		$this->_login_node__login();
	}
	
	public function _login_node__login() {
		
		
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			try {
				$post_key = $this->_node__post_arg('post_key');
				
				if($post_key != $_SESSION['post_key']) {
					throw new registration_error__ns6739(
						'Несоответствие ключа полномочий на POST-запрос');
				}
				
				if(!$this->_login_node__mail) {
					throw new registration_error__ns6739(
						'Пожалуйста укажите свою электронную почту');
				}
				
				if(!$this->_login_node__password) {
					throw new registration_error__ns6739(
						'Пожалуйста укажите пароль от регистрации');
				}
				
				try{
					$reg_data = $this->_login_node__db->login(
						$this->_login_node__mail,
						$this->_login_node__password);
				} catch(registration_error__ns5547 $e) {
					$error = $e->getMessage();
					
					throw new registration_error__ns6739($error);
				}
				
				$_SESSION['authorized'] = TRUE;
				$_SESSION['reg_data'] = $reg_data;
			} catch(registration_error__ns6739 $e) {
				$error = $e->getMessage();
				
				$this->_login_node__login_error = $error;
			}
		}
		
	}
	
	public function _node__get_head() {
		$html = '';
		
		$html .= parent::_node__get_head();
		
		if(!$this->_login_node__login_error &&
				$_SERVER['REQUEST_METHOD'] == 'POST') {
			$html .= '<meta http-equiv="Refresh" content="1;url='.htmlspecialchars(
						$_SERVER['SCRIPT_NAME'].'?node=info').'" />';
		}
		
		//$html .= 
		//	'<link rel="stylesheet" type="text/css" '.
		//		'href="/media/css/login_node.css" />';
		$html .= 
			'<script type="text/javascript" '.
				'src="/media/js/login_node.js"></script>';
		
		return $html;
	}
	
	public function _node__get_content() {
		$last_error = $this->_node__get_arg('error');
		
		$show_form = TRUE;
		
		$html = '';
		
		if($last_error) {
			$html .=
				'<div class="Margin10Px TextAlignCenter ErrorColor">'.
					htmlspecialchars($last_error).
				'</div>';
		}
		
		if(!$this->_login_node__login_error) {
			if($_SERVER['REQUEST_METHOD'] == 'POST') {
				$show_form = FALSE;
			
				$html .=
					'<div class="Margin10Px TextAlignCenter SuccessColor">'.
						'Вход осуществлён!'.
					'</div>';
			}
		} else {
			$html .=
				'<div class="Margin10Px TextAlignCenter ErrorColor">'.
					htmlspecialchars('Ошибка: '.
						$this->_login_node__login_error).
				'</div>';
		}
		
		if($show_form) {
			$html .= '<h1 class="TextAlignCenter">Вход</h1>';
			$html .= 
				'<form class="Margin10Px Width500Px" '.
						'action="'.htmlspecialchars(
							$_SERVER['SCRIPT_NAME'].'?node='.
							urlencode($this->_node__get_arg('node'))
						).'" method="post">'.
					'<p>'.
						'<input class="FloatRight" id="_login_node__mail" name="mail" '.
							'value="'.htmlspecialchars($this->_login_node__mail).'" />'.
						'<label for="_login_node__mail" >E-mail: </label>'.
						'<div class="ClearBoth"></div>'.
					'</p>'.
					'<p>'.
						'<input class="FloatRight" id="_login_node__password" '.
							'type="password" name="password" '.
							'value="'.htmlspecialchars($this->_login_node__password).'" />'.
						'<label for="_login_node__password">Пароль: </label>'.
						'<div class="ClearBoth"></div>'.
					'</p>'.
					'<p>'.
						'<input type="hidden" name="post_key" '.
							'value="'.htmlspecialchars($_SESSION['post_key']).'" />'.
						'<input class="FloatRight Margin5Px" type="submit" value="Войти!" />'.
						'<div class="ClearBoth"></div>'.
					'</p>'.
					'<p>'.
						'<a id="_login_node__ragistration_button" '.
								'class="FloatRight Margin5Px" '.
								'href="'.htmlspecialchars($_SERVER['SCRIPT_NAME'].'?node=registration').'" >'.
							'Регистрация'.
						'</a>'.
						'<div class="ClearBoth"></div>'.
					'</p>'.
				'</form>';
		}
		
		return $html;
	}
}

class login_node_type__ns6739
		extends node_type__ns32056 {
	public function __construct($obj) {
		parent::__construct($obj);
	}
	public static function new_obj($db) {
		return new self(new login_node__ns6739($db));
	}
}






