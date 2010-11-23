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
require_once dirname(__FILE__).'/class.frame.ns26442.php';
require_once dirname(__FILE__).'/utils/class.msg_bus.ns1438.php';

class error_frame__ns22966 extends frame__ns26442 {
    protected $_error_frame__message_html;
    protected $_error_frame__buttons_html;
    
    protected function _base_node__on_init() {
        parent::_base_node__on_init();
        
        $msg_token = $this->get_arg('msg_token');
        $args = recv_msg__ns1438($msg_token, 'error_frame__ns22966::args');
        
        if($args && array_key_exists('message', $args)) {
            $message = $args['message'];
        } else {
            $message = '(Неопределённая Ошибка)';
        }
        
        $this->_error_frame__message_html = $this->html_from_txt($message);
    }
    
    protected function _frame__get_head() {
        $parent_head = parent::_frame__get_head();
        
        $html = '';
        
        $html .=
            $parent_head.
            '<link rel="stylesheet" href="/media/error_frame/css/style.css" />';
        
        return $html;
    }
    
    protected function _frame__get_aside() {
        $button_html = '';
        
        $html = '';
        
        $html .=
            '<div class="SmallFrame">'.
                '<div class="ErrorColor TextAlignCenter">'.
                    $this->_error_frame__message_html.
                '</div>'.
            '</div>';
        
        return $html;
    }
}

