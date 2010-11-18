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
require_once dirname(__FILE__).'/utils/class.cached_time.ns29922.php';

class home_node__ns25120 extends node__ns21085 {
    protected $_node_base__need_check_auth = TRUE;
    
    protected $_home_node__items_page = 0;
    protected $_home_node__items_limit = 20;
    protected $_home_node__items;
    
    protected function _node_base__on_add_check_perms() {
        parent::_node_base__on_add_check_perms();
        
        $this->_node_base__add_check_perms(
            array(
                // требуется разрешение на поиск Элементов Данных:
                'search_items' => TRUE,
            )
        );
    }
    
    protected function _node_base__on_init() {
        parent::_node_base__on_init();
        
        if(array_key_exists('items_page', $_GET)) {
            $items_page = intval($this->get_arg('items_page'));
            
            if($items_page >= 0) {
                $this->_home_node__items_page = $items_page;
            }
        }
        
        if(array_key_exists('items_limit', $_GET)) {
            $items_limit = intval($this->get_arg('items_limit'));
            
            if($items_limit >= 1 && $items_limit <= 200) {
                $this->_home_node__items_limit = $items_limit;
            }
        }
        
        $result = mysql_query_or_error(
            sprintf(
                'SELECT * FROM `items_base` '.
                    'ORDER BY ABS(%s - `item_modified`) '.
                    'LIMIT %s OFFSET %s',
                intval(get_time__ns29922()),
                intval($this->_home_node__items_limit),
                intval($this->_home_node__items_page * $this->_home_node__items_limit)
            ),
            $this->_node_base__db_link
        );
        
        $this->_home_node__items = array();
        if($result) {
            for(;;) {
                $row = mysql_fetch_assoc($result);
                if($row) {
                    $this->_home_node__items[] = $row;
                }
                else {
                    break;
                }
            }
            
            mysql_free_result($result);
        }
        
        // TODO: код для инициализации
    }
    
    protected function _node__get_head() {
        $parent_head = parent::_node__get_head();
        
        $html = '';
        
        $html .=
            $parent_head.
            '<link rel="stylesheet" type="text/css" href="/media/home_node/css/style.css" />';
        
        return $html;
    }
    
    protected function _node__get_aside() {
        $html = '';
        
        $html .=
            '<div class="SmallFrame">'.
                '<h1>Последние добавленные</h1>'.
                '<pre>'.
                    str_replace(' ', '&nbsp;', htmlspecialchars(print_r($this->_home_node__items, TRUE))).
                '</pre>'.
            '</div>';
        
        return $html;
    }
}




