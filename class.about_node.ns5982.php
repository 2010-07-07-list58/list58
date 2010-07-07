<?php

require_once dirname(__FILE__).'/class.node_base.ns8054.php';
require_once dirname(__FILE__).'/class.node.ns21085.php';

class about_node__ns5982 extends node__ns21085 {
	protected function _node__get_title() {
		$parent_title = parent::_node__get_title();
		
		return 'О Системе - '.$parent_title;
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
				'<p style="color: rgb(255,0,0)">(здесь в будущем будет информация о системе)</p>'.
				'<p style="color: rgb(128,128,0)">(здесь в будущем будет информация о системе)</p>'.
				'<p style="color: rgb(0,255,0)">(здесь в будущем будет информация о системе)</p>'.
				'<p style="color: rgb(0,128,128)">(здесь в будущем будет информация о системе)</p>'.
				'<p style="color: rgb(0,0,255)">(здесь в будущем будет информация о системе)</p>'.
				'<p style="color: rgb(128,0,128)">(здесь в будущем будет информация о системе)</p>'.
			'</div>';
		
		return $html;
	}
}




