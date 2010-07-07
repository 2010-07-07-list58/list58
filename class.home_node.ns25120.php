<?php

require_once dirname(__FILE__).'/class.node_base.ns8054.php';
require_once dirname(__FILE__).'/class.node.ns21085.php';

class home_node__ns25120 extends node__ns21085 {
	protected function _node_base__on_init() {
		parent::_node_base__on_init();
		
		if(!$_SESSION['authorized']) {
			throw new not_authorized_error__ns3300('Доступ ограничен!');
		}
	}
	
	//protected function _node__get_title() {
	//	$parent_title = parent::_node__get_title();
	//	
	//	return 'ыыЫЫЫыыЫ? - '.$parent_title;
	//}
	
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
				'<p style="color: rgb(255,0,0)">(здесь в будущем будет основная страницца)</p>'.
				'<p style="color: rgb(128,128,0)">(здесь в будущем будет основная страницца)</p>'.
				'<p style="color: rgb(0,255,0)">(здесь в будущем будет основная страницца)</p>'.
				'<p style="color: rgb(0,128,128)">(здесь в будущем будет основная страницца)</p>'.
				'<p style="color: rgb(0,0,255)">(здесь в будущем будет основная страницца)</p>'.
				'<p style="color: rgb(128,0,128)">(здесь в будущем будет основная страницца)</p>'.
			'</div>';
		
		return $html;
	}
}




