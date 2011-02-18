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
require_once dirname(__FILE__).'/utils/class.cached_time.ns29922.php';
require_once dirname(__FILE__).'/utils/class.real_ip.ns5513.php';
require_once dirname(__FILE__).'/utils/class.gpc.ns2886.php';
require_once dirname(__FILE__).'/utils/class.mysql_tools.php';

class abstract_function_error__ns8054
        extends site_error__ns14329 {}

class base_node__ns8054 {
    public $environ;
    
    protected $_base_node__need_check_post_token = TRUE;
    protected $_base_node__need_check_post_token_for_get = FALSE;
    protected $_base_node__enforce_referer_for_check_post_token = TRUE;
    protected $_base_node__need_check_auth = FALSE;
    protected $_base_node__need_check_perms = array();
    
    protected $_base_node__authorized = FALSE;
    protected $_base_node__db_link = NULL;
    protected $_base_node__perms = array();
    
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
        if($this->_base_node__enforce_referer_for_check_post_token) {
            // какой бы не был 'post_token', но если проверка усилена соблюдением 'referer',
            //      то сначало нужно проверить 'referer'
            
            if(array_key_exists('HTTP_REFERER', $_SERVER) && $_SERVER['HTTP_REFERER']) {
                $referer = parse_url($_SERVER['HTTP_REFERER']);
                
                $error_massage =
                        'Ошибка системы безопасности: '."\n".
                        'Данный модифицирующий запрос не может быть послан с постороннего ресурса';
                
                if(!$referer) {
                    // если мы не можем распознать 'referer', то значит он какойто плохой (скорее всего чужой)
                    
                    $this->_base_node__throw_site_error(
                            $error_massage, array('return_back' => TRUE));
                }
                
                if(array_key_exists('host', $referer) &&
                        $referer['host'] != $_SERVER['SERVER_NAME']) {
                    // если 'referer' содержит имя хоста, но он не совпадает с нашим, значит ресурс чужой
                    
                    $this->_base_node__throw_site_error(
                            $error_massage, array('return_back' => TRUE));
                }
                
                if(array_key_exists('port', $referer) &&
                        $referer['port'] != $_SERVER['SERVER_PORT']) {
                    // если 'referer' содержит номер порта, но он не совпадает с нашим, значит ресурс чужой
                    
                    $this->_base_node__throw_site_error(
                            $error_massage, array('return_back' => TRUE));
                    
                    // технически может существовать и другая (обратная) проблема:
                    //      когда 'referer' не содержит номер порта, но наш порт не является стандартным.
                    //      но я не вижу элегантного способа выявить эту проблему.
                    //  поэтому лучше либо использовать только стандартный порт,
                    //      либо доверять запросам из стандартного порта при использовании нестандартного порта
                }
            }
        }
        
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
            
            if(!$post_token) {
                $this->get_arg('post_token');
                
                // ничего страшного в том что нам может понадобиться
                //      передать 'post_token' внутри GET-параметра.
                //  это например может пригодится для Ajax-целей
            }
            
            $this->_base_node__check_post_token_for($post_token);
        }
    }
    
    protected function _base_node__check_post_token_for_get() {
        $post_token = $this->get_arg('post_token');
        
        $this->_base_node__check_post_token_for($post_token);
    }
    
    protected function _base_node__clean_auth() {
        $this->_base_node__authorized = FALSE;
        $_SESSION['authorized'] = FALSE;
        unset($_SESSION['reg_data']);
        $this->_base_node__perms = array();
    }
    
    protected function _base_node__check_auth() {
        try {
            if(!$_SESSION['authorized']) {
                throw new not_authorized_error__ns3300('Доступ ограничен!');
            }
            
            $this->_base_node__init_perms();
            
            // проверка существования сессии:
            $result = mysql_query_or_error(
                sprintf(
                    'SELECT `login`, `session` FROM `user_sessions` '.
                            'WHERE `login` = \'%s\' AND `session` = \'%s\'',
                    mysql_real_escape_string($_SESSION['reg_data']['login'], $this->_base_node__db_link),
                    mysql_real_escape_string($_SESSION['session_token'], $this->_base_node__db_link)
                ),
                $this->_base_node__db_link
            );
            $row = mysql_fetch_row($result);
            mysql_free_result($result);
            if(!$row) {
                throw new not_authorized_error__ns3300(
                        'Требуется повторная авторизация, так как сессия была закрыта');
            }
            
            // проверка ограничений по ip:
            if(in_array('ip_limit', $this->_base_node__perms)) {
                // время жизни автоматических ip-адресов:
                $auto_ip_lifetime = 60 * 60 * 3;
                
                $time = get_time__ns29922();
                $ip = get_real_ip__ns5513();
                
                $result = mysql_query_or_error(
                    sprintf(
                        'SELECT `ip` FROM `user_ips` '.
                                'WHERE `login` = \'%s\' AND '.
                                '(NOT IFNULL(`auto_time`, 0) OR ABS(\'%s\' - `auto_time`) < \'%s\')',
                        mysql_real_escape_string($_SESSION['reg_data']['login'], $this->_base_node__db_link),
                        mysql_real_escape_string($time, $this->_base_node__db_link),
                        mysql_real_escape_string($auto_ip_lifetime, $this->_base_node__db_link)
                    ),
                    $this->_base_node__db_link
                );
                $ips = array();
                $ip_pass = FALSE;
                for(;;) {
                    $row = mysql_fetch_row($result);
                    if($row) {
                        list($stored_ip) = $row;
                        
                        if($stored_ip == $ip) {
                            $ip_pass = TRUE;
                        }
                        
                        $ips []= $stored_ip;
                    } else {
                        break;
                    }
                }
                mysql_free_result($result);
                
                if(!$ip_pass) {
                    if(in_array('auto_ip_limit', $this->_base_node__perms)) {
                        if($ips) {
                            throw new not_authorized_error__ns3300(
                                    'Сессия прервана, так как '.
                                    'эта учётная запись уже (или недавно была) открыта c другого ip-адреса');
                        }
                    } else {
                        throw new not_authorized_error__ns3300(
                                'Сессия прервана, так как '.
                                'с этого ip-адреса -- доступ не возможен');
                    }
                }
                
                // так или иначе, если всё нормально, то обновим таблицу ip-адресов
                
                if(in_array('auto_ip_limit', $this->_base_node__perms)) {
                    // удаляем все автоматические:
                    mysql_query_or_error(
                        sprintf(
                            'DELETE FROM `user_ips` '.
                                    'WHERE `login` = \'%s\' AND IFNULL(`auto_time`, 0)',
                            mysql_real_escape_string($_SESSION['reg_data']['login'], $this->_base_node__db_link)
                        ),
                        $this->_base_node__db_link
                    );
                    
                    // и добавляем текущий ip:
                    mysql_query_or_error(
                        sprintf(
                            'INSERT INTO `user_ips` '.
                                '(`login`, `ip`, `auto_time`) VALUES (\'%s\', \'%s\', \'%s\')',
                            mysql_real_escape_string($_SESSION['reg_data']['login'], $this->_base_node__db_link),
                            mysql_real_escape_string($ip, $this->_base_node__db_link),
                            mysql_real_escape_string($time, $this->_base_node__db_link)
                        ),
                        $this->_base_node__db_link
                    );
                }
            }
            
            if(!in_array('multisession', $this->_base_node__perms)) {
                // если не разрешена мультисессия, то удаляем все посторонние сессии
                
                mysql_query_or_error(
                    sprintf(
                        'DELETE FROM `user_sessions` WHERE `login` = \'%s\' AND `session` <> \'%s\'',
                        mysql_real_escape_string($_SESSION['reg_data']['login'], $this->_base_node__db_link),
                        mysql_real_escape_string($_SESSION['session_token'], $this->_base_node__db_link)
                    ),
                    $this->_base_node__db_link
                );
            }
            
            // устанавливаем флаг, свидетельствующий о том что авторизация проверена
            $this->_base_node__authorized = TRUE;
        } catch(not_authorized_error__ns3300 $e) {
            $message = $e->getMessage();
            
            if(!$message) {
                $message = 'Ошибка авторизации!';
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
    
    protected function _base_node__init_perms() {
        $this->_base_node__perms = array();
        
        $result = mysql_query_or_error(
            sprintf(
                'SELECT `group` FROM `user_groups` '.
                        'WHERE `login` = \'%s\'',
                mysql_real_escape_string(
                        $_SESSION['reg_data']['login'], $this->_base_node__db_link)
            ),
            $this->_base_node__db_link
        );
        
        for(;;) {
            $row = mysql_fetch_row($result);
            
            if($row) {
                list($stored_group) = $row;
                
                $this->_base_node__perms []= $stored_group;
            } else {
                break;
            }
        }
        
        mysql_free_result($result);
    }
    
    protected function _base_node__is_permitted($perm, $options=array()) {
        // строгая проверка разрешений
        
        if($this->_base_node__authorized) {
            if(in_array($perm, $this->_base_node__perms)) {
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
    
    protected function _base_node__track_session() {
        if($this->_base_node__authorized) {
            $time = get_time__ns29922();
            $ip = get_real_ip__ns5513();
            $browser = array_key_exists('HTTP_USER_AGENT', $_SERVER)?$_SERVER['HTTP_USER_AGENT']:NULL;
            $query = array_key_exists('QUERY_STRING', $_SERVER)?$_SERVER['QUERY_STRING']:NULL;
            
            mysql_query_or_error(
                sprintf(
                    'UPDATE `user_sessions` SET '.
                            '`last_time` = \'%s\', '.
                            '`last_ip` = \'%s\', '.
                            '`last_browser` = \'%s\', '.
                            '`last_query` = \'%s\''.
                            'WHERE `login` = \'%s\' AND `session` = \'%s\'',
                    mysql_real_escape_string($time, $this->_base_node__db_link),
                    mysql_real_escape_string($ip, $this->_base_node__db_link),
                    mysql_real_escape_string($browser, $this->_base_node__db_link),
                    mysql_real_escape_string($query, $this->_base_node__db_link),
                    mysql_real_escape_string($_SESSION['reg_data']['login'], $this->_base_node__db_link),
                    mysql_real_escape_string($_SESSION['session_token'], $this->_base_node__db_link)
                ),
                $this->_base_node__db_link
            );
        }
    }
    protected function _base_node__on_init() {
        // проверка авторизации:
        if($_SESSION['authorized'] || $this->_base_node__need_check_auth) {
            $this->_base_node__check_auth();
        }
        
        // проверка разрешений:
        $this->_base_node__on_add_check_perms();
        if($this->_base_node__need_check_perms) {
            $this->_base_node__check_perms(
                $this->_base_node__need_check_perms
            );
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
        
        // отслеживаем активность сессии:
        $this->_base_node__track_session();
    }
    
    public function __construct($environ) {
        $this->environ = $environ;
        
        $this->_base_node__init_db();
        
        $this->_base_node__begin_db();
        try{
            $this->_base_node__on_init();
            $this->_base_node__commit_db();
        } catch (Exception $e) {
            $this->_base_node__rollback_db();
            $this->_base_node__clean_db();
            
            throw $e;
        }
        
        // защита от случайного безтранзактного использования базы данных:
        $this->_base_node__clean_db();
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
    
    protected function _base_node__get_content_type() {
        return 'text/html;charset=utf-8';
    }
    
    protected function _base_node__get_html() {
        throw new abstract_function_error__ns8054();
    }
    
    public function get_redirect() {
        return $this->_base_node__get_redirect();
    }
    
    public function get_content_type() {
        return $this->_base_node__get_content_type();
    }
    
    public function get_html() {
        return $this->_base_node__get_html();
    }
}

