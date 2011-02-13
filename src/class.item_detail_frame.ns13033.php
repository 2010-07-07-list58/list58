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
require_once dirname(__FILE__).'/utils/class.cached_time.ns29922.php';

class item_detail_frame__ns13033 extends frame__ns26442 {
    protected $_base_node__need_db = TRUE;
    protected $_base_node__need_check_auth = TRUE;
    
    protected $_item_detail_frame__item_id;
    protected $_item_detail_frame__item = NULL;
    
    protected $_item_detail_frame__next = NULL;
    protected $_item_detail_frame__next_message = NULL;
    protected $_item_detail_frame__next_message_html = NULL;
    
    protected function _base_node__on_add_check_perms() {
        parent::_base_node__on_add_check_perms();
        
        $this->_base_node__add_check_perms(
            array(
                // требуется разрешение на просмотр Элементов Данных:
                'view_items' => TRUE,
            )
        );
    }
    
    protected function _base_node__on_init() {
        parent::_base_node__on_init();
        
        if(array_key_exists('item_id', $_GET)) {
            $this->_item_detail_frame__item_id = intval($this->get_arg('item_id'));
        } else {
            $this->_base_node__throw_site_error('Недостаточно аргументов');
        }
        
        $msg_token = $this->get_arg('msg_token');
        $args = recv_msg__ns1438($msg_token, 'item_detail_frame__ns13033::args');
        
        if($args && array_key_exists('next', $args)) {
            $this->_item_detail_frame__next = $args['next'];
        }
        if($args && array_key_exists('next_message', $args)) {
            $this->_item_detail_frame__next_message = $args['next_message'];
        }
        if($args && array_key_exists('next_message_html', $args)) {
            $this->_item_detail_frame__next_message_html = $args['next_message_html'];
        }
        
        $result = mysql_query_or_error(
            sprintf(
                'SELECT * FROM `items_base` WHERE NOT IFNULL(`item_deleted`, FALSE) AND `id` = \'%s\'',
                mysql_real_escape_string($this->_item_detail_frame__item_id)
            ),
            $this->_base_node__db_link
        );
        
        $row = mysql_fetch_assoc($result);
        if($row) {
            $this->_item_detail_frame__item = $row;
        }
        mysql_free_result($result);
        
        if(!$this->_item_detail_frame__item) {
            $this->_base_node__throw_site_error('Данные отсутствуют');
        }
    }
    
    protected function _frame__get_head() {
        $parent_head = parent::_frame__get_head();
        
        $html = '';
        
        $html .=
            $parent_head.
            '<link rel="stylesheet" href="/media/item_detail_frame/css/style.css" />';
        
        return $html;
    }
    
    protected function _frame__get_aside() {
        $html =
            '<h2 class="TextAlignCenter">Полная информация ...</h2>'.
            '<pre>'.
                htmlspecialchars(print_r($this->_item_detail_frame__item, TRUE)).
            '</pre>';
        
        return $html;
    }
}

