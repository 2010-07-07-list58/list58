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

class home_node__ns20822 extends node__ns21085 {
    // по сути -- этот класс выполняет 2 функции:
    //      1. проверяет наличие/отсутствие авторизации
    //      2. указывает какая для сайта страница -- является главной
    
    protected $_base_node__need_check_auth = TRUE;
    
    protected function _base_node__get_redirect() {
        $redirect = '?'.http_build_query(array(
                'node' => 'search_items'));
        
        return $redirect;
    }
}

