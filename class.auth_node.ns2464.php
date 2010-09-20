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
require_once dirname(__FILE__).'/utils/class.captcha.ns8574.php';
require_once dirname(__FILE__).'/utils/class.msg_bus.ns1438.php';

class auth_node__ns2464 extends node__ns21085 {
    protected $_node_base__need_db = TRUE;    
    
    protected $_auth_node__show_form = TRUE;
    protected $_auth_node__captcha_html = '';
    protected $_auth_node__message_html = '';
    
    protected function _node_base__on_init() {
        parent::_node_base__on_init();
        
        $msg_key = $this->get_arg('msg_key');
        $args = recv_msg__ns1438($msg_key, 'auth_node__ns2464::args');
        
        if($args && array_key_exists('error_message', $args)) {
            $error_message = $args['error_message'];
            
            $this->_auth_node__message_html .=
                '<p class="ErrorColor TextAlignCenter">'.
                    htmlspecialchars($error_message).
                '</p>';
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if(captcha_check_answer__ns8574($_POST)) {
                $login_success = FALSE;
                
                $login = $this->post_arg('login');
                $password = $this->post_arg('password');
                
                $result = mysql_query(
                    sprintf(
                        'SELECT `login`, `password` FROM `users_base` WHERE '.
                            '`login` = \'%s\' AND `password` = \'%s\'',
                        mysql_real_escape_string($login, $this->_node_base__db_link),
                        mysql_real_escape_string($password, $this->_node_base__db_link)
                    ),
                    $this->_node_base__db_link
                );
                
                if($result) {
                    $row = mysql_fetch_row($result);
                    if($row) {
                        list($stored_login, $stored_password) = $row;
                        
                        if($stored_login == $login &&
                                $stored_password == $password) {
                            $login_success = TRUE;
                        }
                    }
                    
                    mysql_free_result($result);
                }
                
                if($login_success) {
                    $_SESSION['reg_data'] = array(
                        'login' => $login,
                    );
                    $_SESSION['authorized'] = TRUE;
                    
                    $this->_auth_node__message_html .=
                        '<p class="SuccessColor TextAlignCenter">'.
                            'Авторизация успешно пройдена...'.
                        '</p>'.
                        '<p class="SuccessColor TextAlignCenter">'.
                            'Добро пожаловать!'.
                        '</p>';
                        
                    @header('Refresh: 1;url=?');
                    $this->_auth_node__show_form = FALSE;
                } else {
                    $this->_auth_node__message_html .=
                        '<p class="ErrorColor TextAlignCenter">'.
                            'Логин и/или Пароль -- неверны'.
                        '</p>';
                }
            } else {
                $captcha_last_error = get_captcha_last_error__ns8574();
                
                $this->_auth_node__message_html .=
                    '<p class="ErrorColor TextAlignCenter">'.
                        'Ошибка Каптчи:<br />'.
                        htmlspecialchars($captcha_last_error).
                    '</p>';
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
            '<link rel="stylesheet" type="text/css" href="/media/auth_node/css/style.css" />'.
            '<script type="application/javascript" src="/media/auth_node/js/autofocus.js" /></script>';
        
        return $html;
    }
    
    protected function _node__get_aside() {
        $form_html = '';
        
        if($this->_auth_node__show_form) {
            $form_html =
                '<form action="'.htmlspecialchars('?node='.urlencode($this->get_arg('node'))).'" method="post">'.
                    '<h2 class="TextAlignCenter">Авторизация в системе</h2>'.
                    '<hr />'.
                    '<p>'.
                        '<label class="FloatLeft Margin5Px" '.
                            'for="_auth_node__login" >'.
                            'Логин: '.
                        '</label>'.
                        '<input class="FloatRight Margin5Px" type="text" '.
                            'name="login" '.
                            'id="_auth_node__login" '.
                            'value="" />'.
                        '<div class="ClearBoth"></div>'.
                    '</p>'.
                    '<p>'.
                        '<label class="FloatLeft Margin5Px" '.
                            'for="_auth_node__password" >'.
                            'Пароль: '.
                        '</label>'.
                        '<input class="FloatRight Margin5Px" type="password" '.
                            'name="password" '.
                            'id="_auth_node__password" '.
                            'value="" />'.
                        '<div class="ClearBoth"></div>'.
                    '</p>'.
                    '<p>'.
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
                    '</p>'.
                    '<hr />'.
                    '<p>'.
                        '<input type="hidden" '.
                            'name="post_token" '.
                            'value="'.htmlspecialchars($_SESSION['post_token']).'" />'.
                        '<input class="FloatLeft Margin5Px" type="submit" value="Войти" />'.
                        '<input class="FloatLeft Margin5Px" type="reset" value="Сброс" />'.
                        '<div class="ClearBoth"></div>'.
                    '</p>'.
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




