<?php

require_once dirname(__FILE__).'/class.node_base.ns8054.php';
require_once dirname(__FILE__).'/class.node.ns21085.php';

class exit_node__ns212 extends node__ns21085 {
	protected function _node_base__on_init() {
		parent::_node_base__on_init();
		
		$_SESSION['authorized'] = FALSE;
		unset($_SESSION['reg_data']);
		
		@header('Refresh: 1;url=?');
	}
	
	protected function _node__get_title() {
		$parent_title = parent::_node__get_title();
		
		return 'Выход - '.$parent_title;
	}
	
	protected function _node__get_head() {
		$parent_head = parent::_node__get_head();
		
		$html = '';
		
		$html .=
			$parent_head.
			'<link rel="stylesheet" type="text/css" href="/media/about_node/css/style.css" />';
		
		return $html;
	}
	
	protected function _node__get_aside() {
		$html = '';
		
		$html .=
			'<div class="SmallFrame">'.
				'Выход...'.
			'</div>';
		
		return $html;
	}
}




