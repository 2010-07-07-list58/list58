<?php

/*
    This file is part of List58.

    List58 is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    List58 is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with List58.  If not, see <http://www.gnu.org/licenses/>.

*/

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




