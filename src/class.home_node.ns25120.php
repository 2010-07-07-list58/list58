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
require_once dirname(__FILE__).'/class.items_list_widget.ns28376.php';
require_once dirname(__FILE__).'/utils/class.cached_time.ns29922.php';

class home_node__ns25120 extends node__ns21085 {
    protected $_node_base__need_check_auth = TRUE;
    
    protected $_home_node__items_page = 0;
    protected $_home_node__items_limit = 0;
    protected $_home_node__items_pages;
    protected $_home_node__items;
    protected $_home_node__items_list_widget;
    
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
            
            if($items_limit >= 0 && $items_limit <= 200) {
                $this->_home_node__items_limit = $items_limit;
            }
        }
        
        $sql_limit = $this->_home_node__items_limit?$this->_home_node__items_limit:20;
        
        $result = mysql_query_or_error(
            'SELECT COUNT(*) FROM `items_base`',
            $this->_node_base__db_link
        );
        list($sql_count) = mysql_fetch_array($result);
        $this->_home_node__items_pages = intval(ceil(floatval($sql_count) / $sql_limit));
        mysql_free_result($result);
        
        $result = mysql_query_or_error(
            sprintf(
                'SELECT * FROM `items_base` '.
                    'ORDER BY ABS(%s - `item_modified`) '.
                    'LIMIT %s OFFSET %s',
                intval(get_time__ns29922()),
                intval($sql_limit),
                intval($this->_home_node__items_page * $sql_limit)
            ),
            $this->_node_base__db_link
        );
        
        $this->_home_node__items = array();
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
        
        $this->_home_node__items_list_widget = 
            new items_list_widget__ns28376($this->_home_node__items);
        
        // TODO: код для инициализации: страницы
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
        $page_links_html = '';
        
        if($this->_home_node__items_page > 0) {
            $query_node = $this->get_arg('node');
            $query_items_page = $this->_home_node__items_page - 1;
            $query_items_limit = $this->_home_node__items_limit;
            
            $query_data = array();
            if($query_node) {
                $query_data['node'] = $query_node;
            }
            if($query_items_limit) {
                $query_data['items_limit'] = $query_items_limit;
            }
            if($query_items_page) {
                $query_data['items_page'] = $query_items_page;
            }
            
            $page_links_html .=
                '<a class="Margin10Px FloatLeft" href="'.htmlspecialchars('?'.http_build_query($query_data)).'">'.
                    htmlspecialchars('<< Более новые').
                '</a>';
        }
        
        if($this->_home_node__items_page < ($this->_home_node__items_pages - 1)) {
            $query_node = $this->get_arg('node');
            $query_items_page = $this->_home_node__items_page + 1;
            $query_items_limit = $this->_home_node__items_limit;
            
            $query_data = array();
            if($query_node) {
                $query_data['node'] = $query_node;
            }
            if($query_items_limit) {
                $query_data['items_limit'] = $query_items_limit;
            }
            if($query_items_page) {
                $query_data['items_page'] = $query_items_page;
            }
            
            $page_links_html .=
                '<a class="Margin10Px FloatRight" href="'.htmlspecialchars('?'.http_build_query($query_data)).'">'.
                    htmlspecialchars('Более старые >>').
                '</a>';
        }
        
        $html = '';
        
        $html .=
            '<div class="SmallFrame">'.
                '<h1>Последние добавленные</h1>'.
                $this->_home_node__items_list_widget->get_widget().
                '<div>'.
                    $page_links_html.
                    '<div class="ClearBoth"></div>'.
                '</div>'.
            '</div>';
        
        return $html;
    }
}

