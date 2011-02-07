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


class page_links_widget__ns22493 {
    protected $_LABEL_TEMPLATE = '%s';
    protected $_items_limit;
    protected $_items_offset;
    protected $_items_count;
    protected $_get_link_html;
    protected $_size;
    protected $_curr_page;
    protected $_last_page;
    
    public function offset_from_page($page) {
        $offset = ($page - 1) * $this->_items_limit;
        
        return $offset;
    }
    
    public function page_from_offset($offset) {
        $page = intval($offset / $this->_items_limit) + 1;
        
        return $page;
    }
    
    public function __construct($items_limit, $items_offset, $items_count, $get_link_html, $size) {
        $this->_items_limit = $items_limit;
        $this->_items_offset = $items_offset;
        $this->_items_count = $items_count;
        $this->_get_link_html = $get_link_html;
        $this->_size = $size;
        
        $this->_curr_page = $this->page_from_offset($this->_items_offset);
        $this->_last_page = intval(ceil(floatval($this->_items_count) / $this->_items_limit));
    }
    
    protected function _get_link_html($render_offset, $render_page, $last_rendered_page) {
        $html = '';
        
        if($last_rendered_page !== NULL && $render_page - $last_rendered_page != 1) {
            $html .= '... ';
        }
        
        if($render_page != $this->_curr_page) {
            $html .= call_user_func(
                $this->_get_link_html,
                $render_offset,
                sprintf($this->_LABEL_TEMPLATE, $render_page)
            );
        } else {
            $html .= sprintf($this->_LABEL_TEMPLATE, $render_page);
        }
        
        return $html;
    }
    
    public function get_widget() {
        $was_rendered_page = array();
        $last_rendered_page = NULL;
        $html_elems = array();
        
        // первые элементы:
        
        for(
            $page = 1;
            $page <= $this->_size + 1;
            ++$page
        ) {
            if(
                !in_array($page, $was_rendered_page) &&
                $page <= $this->_last_page
            ) {
                $offset = $this->offset_from_page($page);
                
                $html_elems []= $this->_get_link_html($offset, $page, $last_rendered_page);
                $was_rendered_page []= $page;
                $last_rendered_page = $page;
            }
        }
        
        // текущие элементы:
        
        for(
            $page = $this->_curr_page - $this->_size;
            $page <= $this->_curr_page + $this->_size;
            ++$page
        ) {
            if(
                !in_array($page, $was_rendered_page) &&
                $page >= 1 &&
                $page <= $this->_last_page
            ) {
                $offset = $this->offset_from_page($page);
                
                $html_elems []= $this->_get_link_html($offset, $page, $last_rendered_page);
                $was_rendered_page []= $page;
                $last_rendered_page = $page;
            }
        }
        
        // последние элементы:
        
        for(
            $page = $this->_last_page - $this->_size;
            $page <= $this->_last_page;
            ++$page
        ) {
            if(
                !in_array($page, $was_rendered_page) &&
                $page >= 1
            ) {
                $offset = $this->offset_from_page($page);
                
                $html_elems []= $this->_get_link_html($offset, $page, $last_rendered_page);
                $was_rendered_page []= $page;
                $last_rendered_page = $page;
            }
        }
        
        // вывод результатов:
        
        if($html_elems) {
            $html = join(' ', $html_elems);
        } else {
            $html = '(Нет)';
        }
        
        return $html;
    }
}

