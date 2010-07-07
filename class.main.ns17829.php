<?php

require_once dirname(__FILE__).'/class.not_authorized_error.ns3300.php';
require_once dirname(__FILE__).'/class.node_base.ns8054.php';

class main__ns17829 {
	public function __construct() {}
	
	public function _main__get_arg($arg_name) {
		if(array_key_exists($arg_name, $_GET)) {
			$arg_value = stripslashes($_GET[$arg_name]);
			
			return $arg_value;
		} else {
			return NULL;
		}
	}
	
	public function _main__init_session() {
		$lifetime = 60 * 60 * 24 * 7 * 10; // 10 недель
		
		session_set_cookie_params($lifetime);
		session_cache_expire($lifetime / 60);
		session_start();
		session_regenerate_id();
		
		if(!array_key_exists('post_key', $_SESSION)) {
			$_SESSION['post_key'] = 
				rand().':'.rand().':'.rand().':'.rand();
		}
		
		if(!array_key_exists('authorized', $_SESSION)) {
			$_SESSION['authorized'] = FALSE;
		}
	}
	
	public function _main__run() {
		$this->_main__init_session();
		
		$node = $this->_main__get_arg('node');
		if($node == NULL) {
			$node = 'home';
		}
		
		$environ = array(
			// ...
		);
		
		try {
			switch($node) {
			case 'home':
				require_once dirname(__FILE__).'/class.home_node.ns25120.php';
				
				$node = new home_node__ns25120($environ);
				
				break;
			
			case 'auth':
				require_once dirname(__FILE__).'/class.auth_node.ns2464.php';
				
				$node = new auth_node__ns2464($environ);
				
				break;
			
			case 'about':
				require_once dirname(__FILE__).'/class.about_node.ns5982.php';
				
				$node = new about_node__ns5982($environ);
				
				break;
			
			case 'exit':
				require_once dirname(__FILE__).'/class.exit_node.ns212.php';
				
				$node = new exit_node__ns212($environ);
				
				break;
			
			default:
				$message = 'Ошибка: Узел не найден';
				
				@header('Content-Type: text/plain;charset=UTF-8');
				if(array_key_exists('HTTP_REFERER', $_SERVER)) {
					@header('Refresh: 1;url='.$_SERVER['HTTP_REFERER']);
				}
				echo $message."\n";
				
				return;
			}
		} catch(not_authorized_error__ns3300 $e) {
			$error = $e->getMessage();
			
			@header('Location: ?node=auth&error='.urlencode($error));
			
			return;
		}
		
		$redirect = $node->get_redirect();
		if($redirect) {
			@header('Location: '.$redirect);
			
			return;
		}
		
		$html = $node->get_html();
		
		@header('Content-Type: text/html;charset=UTF-8');
		@header('X-UA-Compatible: chrome=1');
		echo $html."\n";
	}
	
	public function run() {
		return $this->_main__run();
	}
}



