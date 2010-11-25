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
require_once dirname(__FILE__).'/utils/class.msg_bus.ns1438.php';
require_once dirname(__FILE__).'/utils/class.cached_time.ns29922.php';

class search_items_node__ns8184 extends node__ns21085 {
    protected $_base_node__need_db = TRUE;
    protected $_base_node__need_check_auth = TRUE;
    
    // TODO: данные поиска (получаемые через msg_bus)
    protected $_search_items_node__items_limit = 0;
    protected $_search_items_node__items_offset = 0;
    protected $_search_items_node__items_count;
    protected $_search_items_node__items;
    
    protected function _base_node__on_add_check_perms() {
        parent::_base_node__on_add_check_perms();
        
        $this->_base_node__add_check_perms(
            array(
                // требуется разрешение на поиск Элементов Данных:
                'search_items' => TRUE,
            )
        );
    }
    
    protected function _base_node__on_init() {
        parent::_base_node__on_init();
        
        $msg_token = $this->get_arg('msg_token');
        $search_args = recv_msg__ns1438($msg_token, 'search_items_node__ns8184::search_args');
        
        if($search_args) {
            // TODO: ...
        }
    }
    
    protected function _node__get_head() {
        $parent_head = parent::_node__get_head();
        
        $html = '';
        
        $html .=
            $parent_head.
            '<link rel="stylesheet" href="/media/search_items_node/css/style.css" />';
        
        return $html;
    }
    
    public function _search_items_node__page_links_widget__get_link_html($items_offset, $label) {
        $query_node = $this->get_arg('node');
        $query_msg_token = $this->get_arg('msg_token');
        
        $query_data = array();
        if($query_node) {
            $query_data['node'] = $query_node;
        }
        if($query_msg_token) {
            $query_data['msg_token'] = $query_msg_token;
        }
        if($this->_search_items_node__items_limit) {
            $query_data['items_limit'] = $this->_search_items_node__items_limit;
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
    
    protected function _search_items_node__get_search_widget() {
        $html =
            '<div class="GroupFrame">'.
                '(форма поиска)'.
            '</div>';
        
        return $html;
    }
    
    protected function _search_items_node__get_result_widget() {
        $html =
            '<div class="GroupFrame">'.
                '(форма ответа)'.
            '</div>'.
            '(а тут будут номера страниц)';
        
        return $html;
    }
    
    protected function _node__get_aside() {
        $search_widget_html = $this->_search_items_node__get_search_widget();
        $result_widget_html = $this->_search_items_node__get_result_widget();
        
        $html = '';
        
        $html .=
            '<div class="SmallFrame">'.
                '<h2 class="TextAlignCenter">Поиск данных</h2>'.
                $search_widget_html.
                (
                    $result_widget_html?
                    '<h3>Найдено:</h3>'.
                    $result_widget_html
                    :''
                ).
            '</div>';
        
        return $html;
    }
}

