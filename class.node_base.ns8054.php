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

require_once dirname(__FILE__).'/class.site_error.ns14329.php';
require_once dirname(__FILE__).'/class.not_authorized_error.ns3300.php';

class abstract_function_error__ns8054
        extends site_error__ns14329 {}

class node_base__ns8054 {
    public $environ;
    
    protected $_node_base__need_db = FALSE;
    protected $_node_base__need_check_post_key = TRUE;
    protected $_node_base__need_check_post_key_for_get = FALSE;
    protected $_node_base__need_check_auth = FALSE;
    protected $_node_base__need_check_perms = array();
    
    protected $_node_base__db_link = NULL;
    protected $_node_base__perms_cache = array();
    
    protected function _node_base__init_db() {
        $mysql_conf_php = dirname(__FILE__).'/data/class.mysql_conf.ns14040.php';
        
        if(file_exists($mysql_conf_php)) {
            require_once $mysql_conf_php;
        } else {
            throw new low_level_error__ns28655(
                sprintf(
                    'Конфигураций файл Базы Данных отсутствует (%s)',
                    $mysql_conf_php
                )
            );
        }
        
        $conf = mysql_conf__ns14040();
        
        $link = @mysql_connect(
            $conf['server'], $conf['username'], $conf['password']
        );
        if(!$link) {
            throw new low_level_error__ns28655(
                sprintf(
                    'Ошибка подключения к Базе Данных (%s)',
                    mysql_error()
                )
            );
        }
        
        $success = @mysql_selectdb($conf['database'], $link);
        if(!$success) {
            throw new low_level_error__ns28655(
                sprintf(
                    'Ошибка открытия Базы Данных (%s)',
                    mysql_error($link)
                )
            );
        }
        
        $success = @mysql_set_charset('utf8', $link);
        if(!$success) {
            throw new low_level_error__ns28655(
                sprintf(
                    'Ошибка кодировки Базы Данных (%s)',
                    mysql_error($link)
                )
            );
        }
        
        mysql_query('AUTOCOMMIT = 0', $link);
        
        $this->_node_base__db_link = $link;
    }
    protected function _node_base__begin_db() {
        mysql_query('BEGIN', $this->_node_base__db_link);
    }
    protected function _node_base__rollback_db() {
        mysql_query('ROLLBACK', $this->_node_base__db_link);
    }
    protected function _node_base__commit_db() {
        mysql_query('COMMIT', $this->_node_base__db_link);
    }
    
    protected function _node_base__check_post_key_for($post_key) {
        if(!$post_key || $post_key != $_SESSION['post_key']) {
            throw_site_error__ns14329(
                'Ошибка системы безопасности: '.
                'Неавторизованный POST-запрос ('.
                'внезапная потеря сессии или, '.
                'возможно, была произведена попытка CSRF-атаки)',
                array('return_back' => TRUE)
            );
        }
    }
    
    protected function _node_base__check_post_key() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post_key = $this->post_arg('post_key');
            
            $this->_node_base__check_post_key_for($post_key);
        }
    }
    
    protected function _node_base__check_post_key_for_get() {
        $post_key = $this->get_arg('post_key');
        
        $this->_node_base__check_post_key_for($post_key);
    }
    
    protected function _node_base__clean_auth() {
        $_SESSION['authorized'] = FALSE;
        unset($_SESSION['reg_data']);
    }
    
    protected function _node_base__check_auth() {
        try {
            if(!$_SESSION['authorized']) {
                throw new not_authorized_error__ns3300();
            }
            
            // TODO: эта часть функции долна быть расширена для более глубокой проверки!
            //      (
            //          проверка по идентификаторам сессий (в базе данных),
            //          проверка по IP-адресам,
            //          ...
            //      )
        } catch(not_authorized_error__ns3300 $e) {
            $message = $e->getMessage();
            
            if(!$message) {
                $message = 'Доступ ограничен!';
            }
            
            // так или иначе если авторизация не пройдена, то сессия должна быть вычищина от этого:
            $this->_node_base__clean_auth();
            
            throw new not_authorized_error__ns3300($message);
        }
    }
    
    protected function _node_base__add_check_perms($new_perms) {
        $this->_node_base__need_check_perms = array_merge(
            $this->_node_base__need_check_perms,
            $new_perms
        );
    }
    
    protected function _node_base__on_add_check_perms() {}
    
    protected function _node_base__is_permitted_nocache($perm) {
        $success = FALSE;
        
        $login = $_SESSION['reg_data']['login'];
        
        $result = mysql_query(
            sprintf(
                'SELECT `login`, `group` FROM `user_groups` WHERE '.
                    '`login` = \'%s\' AND `group` = \'%s\'',
                mysql_real_escape_string($login, $this->_node_base__db_link),
                mysql_real_escape_string($perm, $this->_node_base__db_link)
            ),
            $this->_node_base__db_link
        );
        
        if($result) {
            $row = mysql_fetch_row($result);
            if($row) {
                list($stored_login, $stored_group) = $row;
                
                if($stored_login == $login &&
                        $stored_group == $perm) {
                    $success = TRUE;
                }
            }
            
            mysql_free_result($result);
        }
        
        return $success;
    }
    
    protected function _node_base__is_permitted($perm, $options=array()) {
        // кэшируемая проверка разрешений
        
        if(array_key_exists($perm, $this->_node_base__perms_cache)) {
            return $this->_node_base__perms_cache[$perm];
        } else {
            $is_permitted = $this->_node_base__is_permitted_nocache($perm);
            
            $this->_node_base__perms_cache[$perm] = $is_permitted;
            
            return $is_permitted;
        }
    }
    
    protected function _node_base__check_perms($perms) {
        foreach($perms as $perm => $perm_is_required) {
            if($perm_is_required) {
                $is_permitted = $this->_node_base__is_permitted($perm);
                
                if(!$is_permitted) {
                    throw_site_error__ns14329(
                        sprintf(
                            'Доступ запрещен (требуемое разрешение: %s)',
                            $perm
                        ),
                        array('return_back' => TRUE)
                    );
                }
            }
        }
    }
    
    protected function _node_base__on_init() {
        // проверка авторизации:
        if($this->_node_base__need_check_auth) {
            $this->_node_base__check_auth();
            
            $this->_node_base__on_add_check_perms();
            if($this->_node_base__need_check_perms) {
                $this->_node_base__check_perms(
                    $this->_node_base__need_check_perms
                );
            }
        }
        
        // проверка на CSRF-атаку:
        if($this->_node_base__need_check_post_key) {
            // проверка на CSRF-атаку для POST-запросов (ВКЛючена поумолчанию)
            
            $this->_node_base__check_post_key();
        }
        if($this->_node_base__need_check_post_key_for_get) {
            // проверка на CSRF-атаку для GET-запросов (ВЫКЛючена поумолчанию)
            
            $this->_node_base__check_post_key_for_get();
        }
    }
    
    public function __construct($environ) {
        $this->environ = $environ;
        
        if($this->_node_base__need_db ||
                $this->_node_base__need_check_auth) {
            $this->_node_base__init_db();
            
            $this->_node_base__begin_db();
            try{
                $this->_node_base__on_init();
                $this->_node_base__commit_db();
            } catch (Exception $e) {
                $this->_node_base__rollback_db();
                throw $e;
            }
        } else {
            $this->_node_base__on_init();
        }
    }
    
    public function get_arg($arg_name, $def=NULL) {
        if(array_key_exists($arg_name, $_GET)) {
            $arg_value = stripslashes($_GET[$arg_name]);
            
            return $arg_value;
        } else {
            return $def;
        }
    }
    
    public function post_arg($arg_name, $def=NULL) {
        if(array_key_exists($arg_name, $_POST)) {
            $arg_value = stripslashes($_POST[$arg_name]);
            
            return $arg_value;
        } else {
            return $def;
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




