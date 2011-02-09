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

require_once dirname(__FILE__).'/class.paths.ns1609.php';
require_once dirname(__FILE__).'/class.site_error.ns14329.php';
require_once dirname(__FILE__).'/class.not_authorized_error.ns3300.php';
require_once dirname(__FILE__).'/utils/class.gpc.ns2886.php';
require_once dirname(__FILE__).'/utils/class.mysql_tools.php';

class abstract_function_error__ns8054
        extends site_error__ns14329 {}

class base_node__ns8054 {
    public $environ;
    
    protected $_base_node__need_db = FALSE;
    protected $_base_node__need_check_post_token = TRUE;
    protected $_base_node__need_check_post_token_for_get = FALSE;
    protected $_base_node__need_check_auth = FALSE;
    protected $_base_node__need_check_perms = array();
    
    protected $_base_node__db_link = NULL;
    protected $_base_node__perms_cache = NULL;
    
    protected function _base_node__init_db() {
        $mysql_conf_php = get_var__ns1609().'/class.mysql_conf.ns14040.php';
        
        if(file_exists($mysql_conf_php)) {
            require_once $mysql_conf_php;
        } else {
            throw new low_level_error__ns28655(
                sprintf(
                    'Конфигурационный файл Базы Данных отсутствует (%s)',
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
        
        mysql_query_or_error('SET autocommit = 0', $link);
        
        $this->_base_node__db_link = $link;
    }
    protected function _base_node__begin_db() {
        mysql_query_or_error('BEGIN', $this->_base_node__db_link);
    }
    protected function _base_node__rollback_db() {
        mysql_query_or_error('ROLLBACK', $this->_base_node__db_link);
    }
    protected function _base_node__commit_db() {
        mysql_query_or_error('COMMIT', $this->_base_node__db_link);
    }
    protected function _base_node__clean_db() {
        $this->_base_node__db_link = NULL;
    }
    
    protected function _base_node__throw_site_error($message, $options=NULL) {
        // эта функция виртуально перегружена в классе 'frame__ns26442'
        //
        // а здесь -- её поведение поумолчанию
        
        throw_site_error__ns14329($message, $options);
    }
    
    protected function _base_node__check_post_token_for($post_token) {
        if(!$post_token || $post_token != $_SESSION['post_token']) {
            $this->_base_node__throw_site_error(
                'Ошибка системы безопасности: '."\n".
                'Неавторизованный модифицирующий запрос ('.
                'внезапная потеря сессии или, '.
                'возможно, была произведена попытка CSRF-атаки)',
                array('return_back' => TRUE)
            );
        }
    }
    
    protected function _base_node__check_post_token() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post_token = $this->post_arg('post_token');
            
            $this->_base_node__check_post_token_for($post_token);
        }
    }
    
    protected function _base_node__check_post_token_for_get() {
        $post_token = $this->get_arg('post_token');
        
        $this->_base_node__check_post_token_for($post_token);
    }
    
    protected function _base_node__clean_auth() {
        $_SESSION['authorized'] = FALSE;
        unset($_SESSION['reg_data']);
    }
    
    protected function _base_node__check_auth() {
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
            $this->_base_node__clean_auth();
            
            throw new not_authorized_error__ns3300($message);
        }
    }
    
    protected function _base_node__add_check_perms($new_perms) {
        $this->_base_node__need_check_perms = array_merge(
            $this->_base_node__need_check_perms,
            $new_perms
        );
    }
    
    protected function _base_node__on_add_check_perms() {}
    
    protected function _base_node__init_perms_cache() {
        $this->_base_node__perms_cache = array();
        
        $login = $_SESSION['reg_data']['login'];
        
        $result = mysql_query_or_error(
            sprintf(
                'SELECT `group` FROM `user_groups` '.
                        'WHERE `login` = \'%s\'',
                mysql_real_escape_string($login, $this->_base_node__db_link)
            ),
            $this->_base_node__db_link
        );
        
        if($result) {
            for(;;) {
                $row = mysql_fetch_row($result);
                if(!$row) {
                    break;
                }
                
                if($row) {
                    list($stored_group) = $row;
                    
                    $this->_base_node__perms_cache []= $stored_group;
                }
            }
            
            mysql_free_result($result);
        }
    }
    
    protected function _base_node__is_permitted($perm, $options=array()) {
        // кэшируемая проверка разрешений
        
        if($_SESSION['authorized']) {
            if($this->_base_node__perms_cache === NULL) {
                $this->_base_node__init_perms_cache();
            }
            
            if(in_array($perm, $this->_base_node__perms_cache)) {
                return TRUE;
            }
        }
    }
    
    protected function _base_node__check_perms($perms) {
        foreach($perms as $perm => $perm_is_required) {
            if($perm_is_required) {
                $is_permitted = $this->_base_node__is_permitted($perm);
                
                if(!$is_permitted) {
                    $this->_base_node__throw_site_error(
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
    
    protected function _base_node__on_init() {
        // проверка авторизации:
        if($this->_base_node__need_check_auth) {
            $this->_base_node__check_auth();
            
            $this->_base_node__on_add_check_perms();
            if($this->_base_node__need_check_perms) {
                $this->_base_node__check_perms(
                    $this->_base_node__need_check_perms
                );
            }
        }
        
        // проверка на CSRF-атаку:
        if($this->_base_node__need_check_post_token) {
            // проверка на CSRF-атаку для POST-запросов (ВКЛючена поумолчанию)
            
            $this->_base_node__check_post_token();
        }
        if($this->_base_node__need_check_post_token_for_get) {
            // проверка на CSRF-атаку для GET-запросов (ВЫКЛючена поумолчанию)
            
            $this->_base_node__check_post_token_for_get();
        }
    }
    
    public function __construct($environ) {
        $this->environ = $environ;
        
        if($this->_base_node__need_db ||
                $this->_base_node__need_check_auth) {
            $this->_base_node__init_db();
            
            $this->_base_node__begin_db();
            try{
                $this->_base_node__on_init();
                $this->_base_node__commit_db();
            } catch (Exception $e) {
                $this->_base_node__rollback_db();
                throw $e;
            }
            
            // защита от случайного безтранзактного использования базы данных:
            $this->_base_node__clean_db();
        } else {
            $this->_base_node__on_init();
        }
    }
    
    public function get_arg($arg_name, $def_value=NULL) {
        $value = get_get__ns2886($arg_name, $def_value);
        
        if($value !== NULL) {
            return $value;
        } else {
            return $def_value;
        }
    }
    
    public function post_arg($arg_name, $def_value=NULL) {
        $value = get_post__ns2886($arg_name, $def_value);
        
        if($value !== NULL) {
            return $value;
        } else {
            return $def_value;
        }
    }
    
    public function html_from_txt($txt) {
        $html = 
            '<p>'.
            str_replace("\n", '</p><p>',
                htmlspecialchars($txt)
            ).
            '</p>';
        
        return $html;
    }
    
    protected function _base_node__get_redirect() {
        return NULL;
    }
    
    protected function _base_node__get_html() {
        throw new abstract_function_error__ns8054();
    }
    
    public function get_redirect() {
        return $this->_base_node__get_redirect();
    }
    
    public function get_html() {
        return $this->_base_node__get_html();
    }
}

