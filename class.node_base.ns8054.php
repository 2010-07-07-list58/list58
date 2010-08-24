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
    
    protected $_node_base__need_db = FALSE;
    protected $_node_base__db_link = NULL;
    
    protected function _node_base__db_init() {
        require_once dirname(__FILE__).'/data/class.mysql_conf.ns14040.php';
        $conf = mysql_conf__ns14040();
        
        if($this->_node_base__need_db) {
            $link = mysql_connect(
                $conf['server'], $conf['username'], $conf['password']
            );
        }
        
        mysql_selectdb($conf['database'], $link); 
        mysql_set_charset('utf8', $link); 
        
        mysql_query('AUTOCOMMIT = 0', $link);
        
        $this->_node_base__db_link = $link;
    }
    protected function _node_base__db_begin() {
        mysql_query('BEGIN', $link);
    }
    protected function _node_base__db_rollback() {
        mysql_query('ROLLBACK', $link);
    }
    protected function _node_base__db_commit() {
        mysql_query('COMMIT', $link);
    }
    
    protected function _node_base__on_init() {}
    
    public function __construct($environ) {
        $this->environ = $environ;
        
        if($this->_node_base__need_db) {
            $this->_node_base__db_init();
            $this->_node_base__db_begin();
            try{
                $this->_node_base__on_init();
            } catch (Exception $e) {
                $this->_node_base__db_rollback();
            }
            $this->_node_base__db_commit();
        } else {
            $this->_node_base__on_init();
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




