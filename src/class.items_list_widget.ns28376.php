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

class items_list_widget__ns28376 {
    protected $_items;
    
    public function __construct($items) {
        $this->_items = $items;
    }
    
    public function get_widget() {
        $html = '';
        
        $html .= 
            '<pre>'.
                htmlspecialchars(print_r($this->_items, TRUE)).
            '</pre>';
        
        return $html;
    }
}

