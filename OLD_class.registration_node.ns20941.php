<?php

require_once dirname(__FILE__).'/class.node.ns32056.php';
require_once dirname(__FILE__).'/class.db.ns5547.php';
require_once dirname(__FILE__).'/class.mail.ns6359.php';

class registration_error__ns20941
		extends Exception {}

class registration_node__ns20941 
		extends node__ns32056 {
	
	public $_registration_node__db;
	
	public function __construct($db) {
		parent::__construct();
		
		$this->_registration_node__db = $db;
	}
	
	public function _node__get_head() {
		$html = '';
		
		$html .= parent::_node__get_head();
		
		$html .= 
			'<link rel="stylesheet" type="text/css" '.
				'href="/media/css/registration_node.css" />';
		
		return $html;
	}
	
	// на случай закрытия ФОРМЫ регистрации (но не самой регистрации)
	public function _node__get_content() {
		$error_message = 'Регистрация закрыта (уже не производиться). Спасибо всем кто зарегестрировался!';
		
		$html = '';
		
		$html .=
			'<div class="Margin10Px TextAlignCenter ErrorColor">'.
				htmlspecialchars($error_message).
			'</div>';
		
		return $html;
	}
	
	public function _node__get_content_DISABLED() {
		$show_form = TRUE;
		
		$nickname = $this->_node__post_arg('nickname');
		$name = $this->_node__post_arg('name');
		$lastname = $this->_node__post_arg('lastname');
		$phone = $this->_node__post_arg('phone');
		$mail = $this->_node__post_arg('mail');
		$password = $this->_node__post_arg('password');
		$password_confirmation = $this->_node__post_arg('password_confirmation');
		
		
		$html = '';
		
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$show_form = FALSE;
			
			try {
				$post_key = $this->_node__post_arg('post_key');
				
				if($post_key != $_SESSION['post_key']) {
					throw new registration_error__ns20941(
						'Несоответствие ключа полномочий на POST-запрос');
				}
				
				$fake_mail = $this->_node__post_arg('email');
				
				if($fake_mail) {
					throw new registration_error__ns20941(
						'Сработала ловушка для роботов');
				}
				
				
				if(!$nickname) {
					throw new registration_error__ns20941(
						'Пожалуйста, укажите ник');
				}
				
				if(!$name) {
					throw new registration_error__ns20941(
						'Пожалуйста, укажите своё имя');
				}
				
				if(!$lastname) {
					throw new registration_error__ns20941(
						'Пожалуйста, укажите свою фамилию');
				}
				
				if(!$phone) {
					throw new registration_error__ns20941(
						'Пожалуйста, укажите свой телефон');
				}
				
				if(!$mail) {
					throw new registration_error__ns20941(
						'Пожалуйста, укажите свою электронную почту');
				}
				
				if(!$password) {
					throw new registration_error__ns20941(
						'Пожалуйста, придумайте и укажите пароль');
				}
				
				if($password != $password_confirmation) {
					throw new registration_error__ns20941(
						'Пароли не совпадают');
				}
				
				$activation_key = 
					rand().':'.rand().':'.rand().':'.rand();
				
				try{
					$reg_date = time();
					$reg_ip = $_SERVER['REMOTE_ADDR'];
					$reg_browser = $_SERVER['HTTP_USER_AGENT'];
					
					$this->_registration_node__db->registration(
						$mail, $password, $activation_key,
						$nickname, $name, $lastname, $phone,
						$reg_date, $reg_ip, $reg_browser);
				} catch(registration_error__ns5547 $e) {
					$error = $e->getMessage();
					
					throw new registration_error__ns20941($error);
				}
				
				$activation_link = 
					'http://'.$_SERVER['SERVER_NAME'].
					$_SERVER['SCRIPT_NAME'].'?node=activation&'.
					'mail='.urlencode($mail).'&'.
					'activation_key='.urlencode($activation_key);
				
				$mail_subject = $_SERVER['SERVER_NAME'].': Регистрация на вчеринку';
				
				$mail_message = 
					'Здравствуйте!'."\n\n".
					'Поздравляем! Вы прошли успешную регистрацию на вечеринку :-) !'."\n\n".
					'Ваши данные: '."\n".
					'Ник: '.$nickname."\n".
					'Имя: '.$name."\n".
					'Фамилия: '.$lastname."\n".
					'Телефон: '.$phone."\n".
					'Пароль: '.$password."\n\n".
					'Для активации, пожалуйста, щёлкните по ссылке: '.$activation_link."\n\n".
					'Спасибо!'."\n\n";
				
				mail__ns6359($mail, $mail_subject, $mail_message);
				
				$html .=
					'<div class="Margin10Px TextAlignCenter SuccessColor">'.
						'Спасибо за регистрацию! '.
						'Пожалуйста, дождитесь активационной ссылки на Ваш E-mail'.
					'</div>';
			} catch(registration_error__ns20941 $e) {
				$error = $e->getMessage();
				
				$html .=
					'<div class="Margin10Px TextAlignCenter ErrorColor">'.
						htmlspecialchars('Ошибка: '.$error).
					'</div>';
				
				$show_form = TRUE;
			}
		}
		
		if($show_form) {
			$html .= '<h1 class="TextAlignCenter">Регистрация на вечеринку</h1>';
			$html .= 
				'<form class="Margin10Px Width500Px" '.
						'action="'.htmlspecialchars(
							$_SERVER['SCRIPT_NAME'].'?node='.
							urlencode($this->_node__get_arg('node'))
						).'" method="post">'.
					'<p>'.
						'<input class="FloatRight" id="_registration_node__nickname" name="nickname" '.
							'value="'.htmlspecialchars($nickname).'" />'.
						'<label for="_registration_node__nickname">Ник: </label>'.
						'<div class="ClearBoth"></div>'.
					'</p>'.
					'<p>'.
						'<input class="FloatRight" id="_registration_node__name" name="name" '.
							'value="'.htmlspecialchars($name).'" />'.
						'<label for="_registration_node__name">Имя: </label>'.
						'<div class="ClearBoth"></div>'.
					'</p>'.
					'<p>'.
						'<input class="FloatRight" id="_registration_node__lastname" name="lastname" '.
							'value="'.htmlspecialchars($lastname).'" />'.
						'<label for="_registration_node__lastname">Фамилия: </label>'.
						'<div class="ClearBoth"></div>'.
					'</p>'.
					'<p>'.
						'<input class="FloatRight" id="_registration_node__phone" name="phone" '.
							'value="'.htmlspecialchars($phone).'" />'.
						'<label for="_registration_node__phone">Телефон (например: +79270101010): </label>'.
						'<div class="ClearBoth"></div>'.
					'</p>'.
					'<p class="DisplayNone">'.
						'<input class="FloatRight" id="_registration_node__email" name="email" />'.
						'<label for="_registration_node__email">E-mail: </label>'.
						'<div class="ClearBoth"></div>'.
					'</p>'.
					'<p>'.
						'<input class="FloatRight" id="_registration_node__mail" name="mail" '.
							'value="'.htmlspecialchars($mail).'" />'.
						'<label for="_registration_node__mail">E-mail: </label>'.
						'<div class="ClearBoth"></div>'.
					'</p>'.
					'<p>'.
						'<input class="FloatRight" id="_registration_node__password" '.
							'type="password" name="password" '.
							'value="'.htmlspecialchars($password).'" />'.
						'<label for="_registration_node__password">Новый пароль: </label>'.
						'<div class="ClearBoth"></div>'.
					'</p>'.
					'<p>'.
						'<input class="FloatRight" id="_registration_node__password_confirmation" '.
							'type="password" name="password_confirmation" '.
							'value="'.htmlspecialchars($password_confirmation).'" />'.
						'<label for="_registration_node__password_confirmation">Новый пароль ещё раз: </label>'.
						'<div class="ClearBoth"></div>'.
					'</p>'.
					'<p>'.
						'<input type="hidden" name="post_key" '.
							'value="'.htmlspecialchars($_SESSION['post_key']).'" />'.
						'<input class="FloatRight Margin5Px" type="reset" value="Сброс!" />'.
						'<input class="FloatRight Margin5Px" type="submit" value="Готово!" />'.
						'<div class="ClearBoth"></div>'.
					'</p>'.
				'</form>';
		}
		
		return $html;
	}
	
	public function _node__get_body() {
		$html = '';
		
		$html .=
			'<table class="Width100Per Height100Per">'.
				'<tr>'.
					'<td class="Padding10Px">'.
						'<table class="MarginAuto">'.
							'<tr>'.
								'<td>'.
									$this->_node__get_content().
								'</td>'.
							'</tr>'.
						'</table>'.
					'</td>'.
				'</tr>'.
			'</table>';
		
		return $html;
	}
}

class registration_node_type__ns20941
		extends node_type__ns32056 {
	public function __construct($obj) {
		parent::__construct($obj);
	}
	public static function new_obj($db) {
		return new self(new registration_node__ns20941($db));
	}
}






