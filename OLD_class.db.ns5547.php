<?php

require_once dirname(__FILE__).'/data/class.mysql_conf.ns14040.php';

class registration_error__ns5547
		extends Exception {}

class db__ns5547 {
	public function impl() {
		return $this;
	}
	
	public $_db__mysql_conf;
	public $_db__link;
	
	public function __construct() {
		$mysql_conf = mysql_conf__ns14040();
		$link = mysql_connect(
			$mysql_conf['server'],
			$mysql_conf['username'],
			$mysql_conf['password']);
		
		mysql_select_db($mysql_conf['database'], $link);
		mysql_set_charset('utf8', $link);
		if(mysql_query(
				'CREATE TABLE `members` ('.
					'`id` BIGINT AUTO_INCREMENT PRIMARY KEY, '.
					'`mail` VARCHAR(255), '.
					'`password` VARCHAR(255), '.
					'`activation_key` VARCHAR(255), '.
					'`nickname` VARCHAR(255), '.
					'`name` VARCHAR(255), '.
					'`lastname` VARCHAR(255), '.
					'`phone` VARCHAR(255), '.
					'`reg_date` BIGINT, '.
					'`reg_ip` TEXT, '.
					'`reg_browser` TEXT)',
				$link)) {
			mysql_query(
				'CREATE INDEX `members(mail)` ON `members` (`mail`)',
				$link);
			mysql_query(
				'CREATE INDEX `members(password)` ON `members` (`password`)',
				$link);
			mysql_query(
				'CREATE INDEX `members(activation_key)` ON `members` (`activation_key`)',
				$link);
			mysql_query(
				'CREATE INDEX `members(nickname)` ON `members` (`nickname`)',
				$link);
			mysql_query(
				'CREATE INDEX `members(name)` ON `members` (`name`)',
				$link);
			mysql_query(
				'CREATE INDEX `members(lastname)` ON `members` (`lastname`)',
				$link);
			mysql_query(
				'CREATE INDEX `members(phone)` ON `members` (`phone`)',
				$link);
		}
		
		$this->_db__mysql_conf = $mysql_conf;
		$this->_db__link = $link;
		$this->_db__lockfile = dirname(__FILE__).'/data/mysql.lock';
	}
	
	public function _db__registration(
			$mail, $password, $activation_key,
			$nickname, $name, $lastname, $phone,
			$reg_date, $reg_ip, $reg_browser) {
		$lock = fopen($this->_db__lockfile, 'w');
		flock($lock, LOCK_EX);

		// отмена регистрации (но не ФОРМЫ регистрации):
		throw new registration_error__ns5547(
			'Регистрация закрыта (уже не производиться). Спасибо всем кто зарегестрировался!');
		//
		
		$result = mysql_query(sprintf(
			'SELECT `id` FROM `members` WHERE `mail` = \'%s\'',
			mysql_real_escape_string($mail)), 
			$this->_db__link);
		if(mysql_fetch_assoc($result)) {
			mysql_free_result($result);
			fclose($lock);
			
			throw new registration_error__ns5547(
				'Участник с такой же электронной почтой уже зарегестрирован(а)');
		} else {
			mysql_free_result($result);
		}
		
		$result = mysql_query(sprintf(
			'SELECT `id` FROM `members` WHERE `phone` = \'%s\'',
			mysql_real_escape_string($phone)), 
			$this->_db__link);
		if(mysql_fetch_assoc($result)) {
			mysql_free_result($result);
			fclose($lock);
			
			throw new registration_error__ns5547(
				'Участник с таким же номером телефона уже зарегестрирован(а)');
		} else {
			mysql_free_result($result);
		}
		
		$result = mysql_query(sprintf(
			'INSERT INTO `members` ('.
				'`mail`, `password`, `activation_key`, '.
				'`nickname`, `name`, `lastname`, `phone`, '.
				'`reg_date`, `reg_ip`, `reg_browser`) '.
			'VALUES (\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', %s, \'%s\', \'%s\') ',
			mysql_real_escape_string($mail),
			mysql_real_escape_string($password),
			mysql_real_escape_string($activation_key),
			mysql_real_escape_string($nickname),
			mysql_real_escape_string($name),
			mysql_real_escape_string($lastname),
			mysql_real_escape_string($phone),
			intval($reg_date),
			mysql_real_escape_string($reg_ip),
			mysql_real_escape_string($reg_browser)), 
			$this->_db__link);
		if(!$result) {
			fclose($lock);
			
			throw new registration_error__ns5547(
				'Системная ошибка при записи в базу данных');
		}
		
		fclose($lock);
	}
	
	public function _db__activation(
			$mail, $activation_key) {
		$lock = fopen($this->_db__lockfile, 'w');
		flock($lock, LOCK_EX);
		
		$result = mysql_query(sprintf(
			'SELECT `activation_key` FROM `members` WHERE `mail` = \'%s\'',
			mysql_real_escape_string($mail)), 
			$this->_db__link);
		if($row = mysql_fetch_assoc($result)) {
			$gotton_activation_key = $row['activation_key'];
			
			if(!$gotton_activation_key) {
				mysql_free_result($result);
				fclose($lock);
				
				throw new registration_error__ns5547(
					'Активация не требуется (Ваша электронная почта УЖЕ активирована)');
			}
			elseif($activation_key != $gotton_activation_key) {
				mysql_free_result($result);
				fclose($lock);
				
				throw new registration_error__ns5547(
					'Активационный ключ не корректен. '.
					'Пожалуйста обратитесь к системному администратору');
			}
		} else {
			mysql_free_result($result);
			fclose($lock);
			
			throw new registration_error__ns5547(
				'Ваша регистрация не найдена. '.
				'Пожалуйста обратитесь к системному администратору');
		}
		mysql_free_result($result);
		
		$result = mysql_query(sprintf(
			'UPDATE `members` SET `activation_key` = \'\' WHERE `mail` = \'%s\'',
			mysql_real_escape_string($mail)),
			$this->_db__link);
		if(!$result) {
			fclose($lock);
			
			throw new registration_error__ns5547(
				'Системная ошибка при обновлении базы данных');
		}
		
		fclose($lock);
	}
	
	public function _db__login(
			$mail, $password) {
		$lock = fopen($this->_db__lockfile, 'w');
		flock($lock, LOCK_EX);
		
		$login_or_password_error = 
			'Логин и/или пароль неверны';
		
		$result = mysql_query(sprintf(
			'SELECT * FROM `members` WHERE `mail` = \'%s\'',
			mysql_real_escape_string($mail)), 
			$this->_db__link);
		if($row = mysql_fetch_assoc($result)) {
			$gotton_password = $row['password'];
			
			if($password != $gotton_password) {
				mysql_free_result($result);
				fclose($lock);
				
				throw new registration_error__ns5547(
					$login_or_password_error);
			}
			
			$reg_data = $row;
		} else {
			mysql_free_result($result);
			fclose($lock);
			
			throw new registration_error__ns5547(
				$login_or_password_error);
		}
		mysql_free_result($result);
		
		fclose($lock);
		
		return $reg_data;
	}
}

class db_type__ns5547 {
	public $obj_impl;
	public function impl() {
		return $this->obj_impl;
	}
	public function __construct($obj) {
		$this->obj_impl = $obj->impl();
	}
	public static function new_obj() {
		return new self(new db__ns5547());
	}
	
	public function registration(
			$mail, $password, $activation_key,
			$nickname, $name, $lastname, $phone,
			$reg_date, $reg_ip, $reg_browser) {
		return $this->obj_impl->_db__registration(
			$mail, $password, $activation_key,
			$nickname, $name, $lastname, $phone,
			$reg_date, $reg_ip, $reg_browser);
	}
	
	public function activation($mail, $activation_key) {
		return $this->obj_impl->_db__activation(
			$mail, $activation_key);
	}
	
	public function login($mail, $password) {
		return $this->obj_impl->_db__login(
			$mail, $password);
	}
}



