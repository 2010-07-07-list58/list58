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

class abstract_function_error__ns8054
		extends Exception {}

class node_base__ns8054 {
	public $environ;
	
	protected $_node_base__need_read = FALSE;
	protected $_node_base__need_write = FALSE;
	
	protected function _node_base__on_init() {}
	protected function _node_base__on_read() {
		throw new abstract_function_error__ns8054();
	}
	protected function _node_base__on_write() {
		throw new abstract_function_error__ns8054();
	}
	
	public function __construct($environ) {
		$this->environ = $environ;
		
		$this->_node_base__on_init();
		if($this->_node_base__need_read) {
			$this->_node_base__on_read();
		}
		if($this->_node_base__need_write) {
			$this->_node_base__on_write();
		}
	}
	
	public function get_arg($arg_name) {
		if(array_key_exists($arg_name, $_GET)) {
			$arg_value = stripslashes($_GET[$arg_name]);
			
			return $arg_value;
		} else {
			return NULL;
		}
	}
	
	public function post_arg($arg_name) {
		if(array_key_exists($arg_name, $_POST)) {
			$arg_value = stripslashes($_POST[$arg_name]);
			
			return $arg_value;
		} else {
			return NULL;
		}
	}
	
	protected function _node_base__get_redirect() {
		return NULL;
	}
	
	protected function _node_base__get_html() {
		throw new abstract_function_error__ns8054();
	}
	
	public function get_redirect() {
		return $this->_node_base__get_redirect();
	}
	
	public function get_html() {
		return $this->_node_base__get_html();
	}
}




