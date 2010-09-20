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

class error_node__ns21717 extends node__ns21085 {
    protected $_error_node__message_html;
    protected $_error_node__buttons_html;
    
    protected function _node_base__on_init() {
        parent::_node_base__on_init();
        
        $msg_token = $this->get_arg('msg_token');
        $args = recv_msg__ns1438($msg_token, 'error_node__ns21717::args');
        
        if($args && array_key_exists('message', $args)) {
            $message = $args['message'];
        } else {
            $message = '(Неопределённая Ошибка)';
        }
        
        if($args && array_key_exists('return_to', $args)) {
            $return_to = $args['return_to'];
            
            $button = sprintf(
                '<a href="%s">Назад</a>',
                htmlspecialchars($return_to)
            );
        } else {
            $button = '<a href="?">Начало</a>';
        }
        
        $this->_error_node__message_html =
            '<p class="ErrorColor TextAlignCenter">'.
                htmlspecialchars($message).
            '</p>';
        
        $this->_error_node__buttons_html = '<p>'.$button.'</p>';
    }
    
    protected function _node__get_title() {
        $parent_title = parent::_node__get_title();
        
        return 'Ошибка - '.$parent_title;
    }
    
    protected function _node__get_head() {
        $parent_head = parent::_node__get_head();
        
        $html = '';
        
        $html .=
            $parent_head.
            '<link rel="stylesheet" type="text/css" href="/media/error_node/css/style.css" />';
        
        return $html;
    }
    
    protected function _node__get_aside() {
        $button_html = '';
        
        $html = '';
        
        $html .=
            '<div class="SmallFrame">'.
                $this->_error_node__message_html.
                $this->_error_node__buttons_html.
            '</div>';
        
        return $html;
    }
}




