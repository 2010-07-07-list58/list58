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
require_once dirname(__FILE__).'/utils/class.mysql_tools.php';
require_once dirname(__FILE__).'/utils/class.cached_time.ns29922.php';

class del_item_node__ns179 extends node__ns21085 {
    protected $_base_node__need_db = TRUE;
    protected $_base_node__need_check_auth = TRUE;
    
    protected $_del_item_node__item_id = 0;
    protected $_del_item_node__item = NULL;
    
    protected $_del_item_node__next = NULL;
    protected $_del_item_node__next_message = NULL;
    protected $_del_item_node__next_message_html = NULL;
    
    protected function _base_node__on_add_check_perms() {
        parent::_base_node__on_add_check_perms();
        
        $this->_base_node__add_check_perms(
            array(
                // требуется разрешение на просмотр Элементов Данных:
                'view_items' => TRUE,
                // требуется разрешение на модификацию Элементов Данных:
                'mod_items' => TRUE,
            )
        );
    }
    
    protected function _base_node__throw_site_error($message, $options=NULL) {
        if($options &&
                !array_key_exists('next', $options) &&
                array_key_exists('return_back', $options) && $options['return_back'] &&
                $this->_mod_item_node__next) {
            $options = array_merge(
                $options,
                array(
                    'next' => $this->_mod_item_node__next,
                )
            );
            unset($options['return_back']);
        }
        
        node__ns21085::_base_node__throw_site_error($message, $options);
    }
    
    protected function _base_node__on_init() {
        parent::_base_node__on_init();
        
        if(array_key_exists('item_id', $_GET)) {
            $this->_del_item_node__item_id = intval($this->get_arg('item_id'));
        } else {
            $this->_base_node__throw_site_error('Недостаточно аргументов');
        }
        
        $msg_token = $this->get_arg('msg_token');
        $args = recv_msg__ns1438($msg_token, 'del_item_node__ns179::args');
        
        if($args && array_key_exists('next', $args)) {
            $this->_del_item_node__next = $args['next'];
        }
        if($args && array_key_exists('next_message', $args)) {
            $this->_del_item_node__next_message = $args['next_message'];
        }
        if($args && array_key_exists('next_message_html', $args)) {
            $this->_del_item_node__next_message_html = $args['next_message_html'];
        }
        
        $result = mysql_query_or_error(
            sprintf(
                'SELECT * FROM `items_base` WHERE NOT IFNULL(`item_deleted`, FALSE) AND `id` = \'%s\'',
                mysql_real_escape_string($this->_del_item_node__item_id)
            ),
            $this->_base_node__db_link
        );
        
        $row = mysql_fetch_assoc($result);
        mysql_free_result($result);
        if($row) {
            $this->_del_item_node__item = $row;
        } else {
            $this->_base_node__throw_site_error(
                'Данные отсутствуют',
                 array('return_back' => TRUE)
            );
        }
        
        if($_SESSION['reg_data']['login'] != $this->_del_item_node__item['item_owner'] &&
                !$this->_base_node__is_permitted('mod_other_items')) {
            $this->_base_node__throw_site_error(
                'Вы не можете изменить эту запись данных',
                array('return_back' => TRUE)
            );
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // TODO: ...
        }
    }
    
    // TODO: ...
}

