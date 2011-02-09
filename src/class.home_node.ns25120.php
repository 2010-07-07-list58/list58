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
require_once dirname(__FILE__).'/class.item_list_widget.ns28376.php';
require_once dirname(__FILE__).'/class.page_links_widget.ns22493.php';
require_once dirname(__FILE__).'/utils/class.cached_time.ns29922.php';
require_once dirname(__FILE__).'/utils/class.mysql_tools.php';

class home_node__ns25120 extends node__ns21085 {
    protected $_base_node__need_db = TRUE;
    protected $_base_node__need_check_auth = TRUE;
    
    protected $_home_node__items_limit = 0;
    protected $_home_node__items_real_limit = 20;
    protected $_home_node__items_offset = 0;
    protected $_home_node__items_count;
    protected $_home_node__items;
    protected $_home_node__item_list_widget;
    protected $_home_node__page_links_widget;
    
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
        
        if(array_key_exists('items_offset', $_GET)) {
            $items_offset = intval($this->get_arg('items_offset'));
            
            if($items_offset > 0) {
                $this->_home_node__items_offset = $items_offset;
            }
        }
        
        if(array_key_exists('items_limit', $_GET)) {
            $items_limit = intval($this->get_arg('items_limit'));
            
            if($items_limit > 0 && $items_limit <= 200) {
                $this->_home_node__items_limit = $items_limit;
                $this->_home_node__items_real_limit = $items_limit;
            }
        }
        
        $result = mysql_query_or_error(
            'SELECT COUNT(*) FROM `items_base`',
            $this->_base_node__db_link
        );
        list($this->_home_node__items_count) = mysql_fetch_array($result);
        mysql_free_result($result);
        
        $result = mysql_query_or_error(
            sprintf(
                'SELECT * FROM `items_base` '.
                    'ORDER BY ABS(%s - `item_modified`) '.
                    'LIMIT %s OFFSET %s',
                intval(get_time__ns29922()),
                intval($this->_home_node__items_real_limit),
                intval($this->_home_node__items_offset)
            ),
            $this->_base_node__db_link
        );
        
        $this->_home_node__items = array();
        for(;;) {
            $row = mysql_fetch_assoc($result);
            if($row) {
                $this->_home_node__items []= $row;
            }
            else {
                break;
            }
        }
        mysql_free_result($result);
        
        $this->_home_node__item_list_widget =
                new item_list_widget__ns28376($this->_home_node__items);
        $this->_home_node__page_links_widget = 
                new page_links_widget__ns22493(
                    $this->_home_node__items_real_limit,
                    $this->_home_node__items_offset,
                    $this->_home_node__items_count,
                    array($this, '_home_node__page_links_widget__get_link_html'),
                    5
                );
    }
    
    protected function _node__get_head() {
        $parent_head = parent::_node__get_head();
        
        $html = '';
        
        $html .=
                $parent_head.
                '<link rel="stylesheet" href="/media/home_node/css/style.css" />'.
                '<script src="/media/home_node/js/autofocus.js"></script>';
        
        return $html;
    }
    
    public function _home_node__page_links_widget__get_link_html($items_offset, $label) {
        $query_node = $this->get_arg('node');
        
        $query_data = array();
        if($query_node) {
            $query_data['node'] = $query_node;
        }
        if($this->_home_node__items_limit) {
            $query_data['items_limit'] = $this->_home_node__items_limit;
        }
        if($items_offset > 0) {
            $query_data['items_offset'] = $items_offset;
        }
        
        $html =
                '<a href="'.htmlspecialchars('?'.http_build_query($query_data)).'">'.
                    htmlspecialchars($label).
                '</a>';
        
        return $html;
    }
    
    protected function _node__get_aside() {
        $quick_search_html = '';
        
        if($this->_base_node__is_permitted('search_items')) {
            $quick_search_html =
                    '<form action="'.htmlspecialchars('?node=search_items').'" method="post">'.
                        '<div class="Margin5Px"><label for="_home_node__general_search">Поиск:</label></div>'.
                        '<div class="Margin5Px">'.
                            '<input class="MinWidth700Px Width100Per" '.
                                'type="text" '.
                                'name="general_search" '.
                                'id="_home_node__general_search" '.
                                'value="" />'.
                        '</div>'.
                        '<div>'.
                            '<input type="hidden" '.
                                'name="post_token" '.
                                'value="'.htmlspecialchars($_SESSION['post_token']).'" />'.
                            '<input class="FloatLeft Margin5Px" type="submit" value="Найти" />'.
                            '<input class="FloatLeft Margin5Px" type="reset" value="Сброс" />'.
                            '<div class="ClearBoth"></div>'.
                        '</div>'.
                    '</form>';
        }
        
        $query_node = $this->get_arg('node');
        $short_page_links_html = '';
        
        if($this->_home_node__items_offset > 0) {
            $query_items_offset = $this->_home_node__items_offset - $this->_home_node__items_real_limit;
            
            $query_data = array();
            if($query_node) {
                $query_data['node'] = $query_node;
            }
            if($this->_home_node__items_limit) {
                $query_data['items_limit'] = $this->_home_node__items_limit;
            }
            if($query_items_offset > 0) {
                $query_data['items_offset'] = $query_items_offset;
            }
            
            $short_page_links_html .=
                    '<a class="FloatLeft" href="'.htmlspecialchars('?'.http_build_query($query_data)).'">'.
                        htmlspecialchars('<< Более новые').
                    '</a>';
        }
        
        if(
            $this->_home_node__items_offset + $this->_home_node__items_real_limit <
            $this->_home_node__items_count
        ) {
            $query_items_offset = $this->_home_node__items_offset + $this->_home_node__items_real_limit;
            
            $query_data = array();
            if($query_node) {
                $query_data['node'] = $query_node;
            }
            if($this->_home_node__items_limit) {
                $query_data['items_limit'] = $this->_home_node__items_limit;
            }
            if($query_items_offset > 0) {
                $query_data['items_offset'] = $query_items_offset;
            }
            
            $short_page_links_html .=
                    '<a class="FloatRight" href="'.htmlspecialchars('?'.http_build_query($query_data)).'">'.
                        htmlspecialchars('Более старые >>').
                    '</a>';
        }
        
        $html =
                '<div class="SmallFrame">'.
                    $quick_search_html.
                    '<h2 class="TextAlignCenter">Последние добавленные данные</h2>'.
                    '<div class="GroupFrame">'.
                        $this->_home_node__item_list_widget->get_widget().
                    '</div>'.
                    '<div>'.
                        $short_page_links_html.
                        '<div class="Margin10Px TextAlignCenter">'.
                            'Стр.: '.$this->_home_node__page_links_widget->get_widget().
                        '</div>'.
                        '<div class="ClearBoth"></div>'.
                    '</div>'.
                '</div>';
        
        return $html;
    }
}

