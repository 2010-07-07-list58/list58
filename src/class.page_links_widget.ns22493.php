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
    protected $_items_limit;
    protected $_items_offset;
    protected $_items_count;
    
    public function __construct($items_limit, $items_offset, $items_count) {
        $this->_items_limit = $items_limit;
        $this->_items_offset = $items_offset;
        $this->_items_count = $items_count;
    }
    
    public function get_widget() {
        $html = '';
        
        $html .= '123';
        
        return $html;
    }
}

