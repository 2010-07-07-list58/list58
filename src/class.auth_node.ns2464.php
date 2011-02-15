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

require_once dirname(__FILE__).'/class.base_node.ns8054.php';
require_once dirname(__FILE__).'/class.node.ns21085.php';
require_once dirname(__FILE__).'/utils/class.cached_time.ns29922.php';
require_once dirname(__FILE__).'/utils/class.real_ip.ns5513.php';
require_once dirname(__FILE__).'/utils/class.captcha.ns8574.php';
require_once dirname(__FILE__).'/utils/class.msg_bus.ns1438.php';
require_once dirname(__FILE__).'/utils/class.mysql_tools.php';

class auth_error__ns2464
        extends Exception {}

class auth_node__ns2464 extends node__ns21085 {
    protected $_auth_node__login;
    protected $_auth_node__password;
    protected $_auth_node__perms;
    
    protected $_auth_node__show_form = TRUE;
    protected $_auth_node__captcha_html = '';
    protected $_auth_node__message_html = '';
    
    protected function _auth_node__init_perms() {
        $this->_auth_node__perms = array();
        
        $result = mysql_query_or_error(
            sprintf(
                'SELECT `group` FROM `user_groups` '.
                        'WHERE `login` = \'%s\'',
                mysql_real_escape_string($this->_auth_node__login, $this->_base_node__db_link)
            ),
            $this->_base_node__db_link
        );
        
        for(;;) {
            $row = mysql_fetch_row($result);
            
            if($row) {
                list($stored_group) = $row;
                
                $this->_auth_node__perms []= $stored_group;
            } else {
                break;
            }
        }
        
        mysql_free_result($result);
    }
    
    protected function _auth_node__check_capture() {
        // эта функция проверяет, что захват сессии возможен, или не требуется
        
        if(!in_array('multisession', $this->_auth_node__perms)) {
            // захват требуется!
            
            // можно будет захватить сессию (после окончания активностей) через 1.5 часа:
            $capture_session_timeout = 60 * 60 * 1.5;
            
            $time = get_time__ns29922();
            
            $result = mysql_query_or_error(
                sprintf(
                    'SELECT `login`, `session` FROM `user_sessions` '.
                            'WHERE `login` = \'%s\' AND `session` <> \'%s\' AND '.
                            'ABS(\'%s\' - `last_time`) < \'%s\'',
                    mysql_real_escape_string($this->_auth_node__login, $this->_base_node__db_link),
                    mysql_real_escape_string($_SESSION['session_token'], $this->_base_node__db_link),
                    mysql_real_escape_string($time, $this->_base_node__db_link),
                    mysql_real_escape_string($capture_session_timeout, $this->_base_node__db_link)
                ),
                $this->_base_node__db_link
            );
            $row = mysql_fetch_row($result);
            mysql_free_result($result);
            if($row) {
                throw new auth_error__ns2464(
                    'Ошибка авторизации:'."\n".
                    'Авторизация не возможна пока кто-то другой уже использует Вашу учётную запись. '.
                            '(Либо учётная запись уже (или недавно была) открыта в другом Вашем броузере).'."\n".
                    'Чтобы захват сессии был возможен, необходимо подождать несколько минут, '.
                            'в течении которых не должна наблюдаться активность той сессий, '.
                            'которая использует Вашу учётную запись'
                );
            }
            
        }
    }
    
    protected function _auth_node__init_session() {
        if(in_array('multisession', $this->_auth_node__perms)) {
            mysql_query_or_error(
                sprintf(
                    'DELETE FROM `user_sessions` WHERE `login` = \'%s\' AND `session` = \'%s\'',
                    mysql_real_escape_string($this->_auth_node__login, $this->_base_node__db_link),
                    mysql_real_escape_string($_SESSION['session_token'], $this->_base_node__db_link)
                ),
                $this->_base_node__db_link
            );
        } else {
            mysql_query_or_error(
                sprintf(
                    'DELETE FROM `user_sessions` WHERE `login` = \'%s\'',
                    mysql_real_escape_string($this->_auth_node__login, $this->_base_node__db_link)
                ),
                $this->_base_node__db_link
            );
        }
        
        $time = get_time__ns29922();
        $ip = get_real_ip__ns5513();
        $browser = array_key_exists('HTTP_USER_AGENT', $_SERVER)?$_SERVER['HTTP_USER_AGENT']:NULL;
        $query = array_key_exists('QUERY_STRING', $_SERVER)?$_SERVER['QUERY_STRING']:NULL;
        
        mysql_query_or_error(
            sprintf(
                'INSERT INTO `user_sessions` '.
                        '(`login`, `session`, `login_time`, `login_ip`, `login_browser`, '.
                        '`last_time`, `last_ip`, `last_browser`, `last_query`) '.
                        'VALUES (\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', '.
                        '\'%s\', \'%s\', \'%s\', \'%s\')',
                mysql_real_escape_string($this->_auth_node__login, $this->_base_node__db_link),
                mysql_real_escape_string($_SESSION['session_token'], $this->_base_node__db_link),
                mysql_real_escape_string($time, $this->_base_node__db_link),
                mysql_real_escape_string($ip, $this->_base_node__db_link),
                mysql_real_escape_string($browser, $this->_base_node__db_link),
                mysql_real_escape_string($time, $this->_base_node__db_link),
                mysql_real_escape_string($ip, $this->_base_node__db_link),
                mysql_real_escape_string($browser, $this->_base_node__db_link),
                mysql_real_escape_string($query, $this->_base_node__db_link)
            ),
            $this->_base_node__db_link
        );
        
        $_SESSION['reg_data'] = array(
            'login' => $this->_auth_node__login,
        );
        $_SESSION['authorized'] = TRUE;
        
        $this->_auth_node__message_html .=
            '<p class="SuccessColor TextAlignCenter">'.
                'Авторизация успешно пройдена...'.
            '</p>'.
            '<p class="SuccessColor TextAlignCenter">'.
                'Добро пожаловать!'.
            '</p>';
    }
    
    protected function _base_node__on_init() {
        parent::_base_node__on_init();
        
        $msg_token = $this->get_arg('msg_token');
        $args = recv_msg__ns1438($msg_token, 'auth_node__ns2464::args');
        
        if($args && array_key_exists('error_message', $args)) {
            $error_message = $args['error_message'];
            
            $this->_auth_node__message_html .=
                '<div class="ErrorColor TextAlignCenter MaxWidth800Px">'.
                    $this->html_from_txt($error_message).
                '</div>';
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                if(!captcha_check_answer__ns8574($_POST)) {
                    $captcha_last_error = get_captcha_last_error__ns8574();
                    
                    throw new auth_error__ns2464(
                            'Ошибка Каптчи:'."\n".$captcha_last_error);
                }
                
                $this->_auth_node__login = $this->post_arg('login');
                $this->_auth_node__password = $this->post_arg('password');
                
                $result = mysql_query_or_error(
                    sprintf(
                        'SELECT `login`, `password` FROM `users_base` '.
                                'WHERE `login` = \'%s\' AND `password` = \'%s\'',
                        mysql_real_escape_string($this->_auth_node__login, $this->_base_node__db_link),
                        mysql_real_escape_string($this->_auth_node__password, $this->_base_node__db_link)
                    ),
                    $this->_base_node__db_link
                );
                $row = mysql_fetch_row($result);
                mysql_free_result($result);
                if(!$row) {
                    throw new auth_error__ns2464(
                            'Логин и/или Пароль -- неверны');
                }
                
                // логин-пароль успешно подошли.. инициируем новую сессию!
                
                $this->_auth_node__init_perms();
                
                $this->_auth_node__check_capture();
                $this->_auth_node__init_session();
                
                @header('Refresh: 1;url=?');
                $this->_auth_node__show_form = FALSE;
            } catch (auth_error__ns2464 $e) {
                $message = $e->getMessage();
                
                $this->_auth_node__message_html .=
                       '<div class="ErrorColor TextAlignCenter MaxWidth800Px">'.
                            $this->html_from_txt($message).
                        '</div>';
            }
        }
        
        if($this->_auth_node__show_form) {
            $this->_auth_node__captcha_html = captcha_get_html__ns8574();
        }
    }
    
    protected function _node__get_title() {
        $parent_title = parent::_node__get_title();
        
        return 'Авторизация - '.$parent_title;
    }
    
    protected function _node__get_head() {
        $parent_head = parent::_node__get_head();
        
        $html = '';
        
        $html .=
            $parent_head.
            '<link rel="stylesheet" href="/media/auth_node/css/style.css" />'.
            '<script src="/media/auth_node/js/autofocus.js"></script>';
        
        return $html;
    }
    
    protected function _node__get_aside() {
        $form_html = '';
        
        if($this->_auth_node__show_form) {
            $form_html =
                '<form action="'.htmlspecialchars('?node='.urlencode($this->get_arg('node'))).'" method="post">'.
                    '<h2 class="TextAlignCenter">Авторизация в системе</h2>'.
                    '<hr />'.
                    '<div>'.
                        '<label class="FloatLeft Margin5Px" '.
                            'for="_auth_node__login" >'.
                            'Логин: '.
                        '</label>'.
                        '<input class="FloatRight Margin5Px" type="text" '.
                            'name="login" '.
                            'id="_auth_node__login" '.
                            'value="" />'.
                        '<div class="ClearBoth"></div>'.
                    '</div>'.
                    '<div>'.
                        '<label class="FloatLeft Margin5Px" '.
                            'for="_auth_node__password" >'.
                            'Пароль: '.
                        '</label>'.
                        '<input class="FloatRight Margin5Px" type="password" '.
                            'name="password" '.
                            'id="_auth_node__password" '.
                            'value="" />'.
                        '<div class="ClearBoth"></div>'.
                    '</div>'.
                    '<div>'.
                        '<h3>Каптча (тест Тьюринга): </h3>'.
                        $this->_auth_node__captcha_html.
                        '<div>'.
                            '<div class="MarginLeft20Px FontSize08Em">'.
                                '<b>Комментарий к Каптче:</b>'.
                                '<div class="MarginLeft20Px">'.
                                    'Если одно из двух слов плохо-читабельно, <br />'.
                                    'то его можно не писать, или написать неточно'.
                                '</div>'.
                            '</div>'.
                        '</div>'.
                        '<div class="ClearBoth"></div>'.
                    '</div>'.
                    '<hr />'.
                    '<div>'.
                        '<input type="hidden" '.
                            'name="post_token" '.
                            'value="'.htmlspecialchars($_SESSION['post_token']).'" />'.
                        '<input class="FloatLeft Margin5Px" type="submit" value="Войти" />'.
                        '<input class="FloatLeft Margin5Px" type="reset" value="Сброс" />'.
                        '<div class="ClearBoth"></div>'.
                    '</div>'.
                '</form>';
        }
        
        $html = '';
        
        $html .=
            '<div class="SmallFrame">'.
                $this->_auth_node__message_html.
                $form_html.
            '</div>';
        
        return $html;
    }
}

