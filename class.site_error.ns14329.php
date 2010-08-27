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

class site_error__ns14329
        extends Exception {
    protected $_site_error__options;
    
    public function _public__site_error__get_options() {
        return $this->_site_error__options;
    }
    
    public function _public__site_error__set_options($options) {
        $this->_site_error__options = $options;
    }
}

function get_error_options__ns14329($e) {
    return $e->_public__site_error__get_options();
}

function set_error_options__ns14329($e, $options) {
    $e->_public__site_error__set_options($options);
}

function throw_site_error__ns14329($message, $options=array()) {
    // конструктор для PHP-класса 'Exception' -- различается в PHP-5.2 и PHP-5.3.
    //  поэтому данная функция предоставляет совместимость
    
    $e = new site_error__ns14329('Узел страницы не найден');
    
    set_error_options__ns14329($e, $options);
    
    throw $e;
}


