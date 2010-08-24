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
    
    public function __construct(
            $message='', $code = 0, $previous = NULL,
            $options=array()) {
        parent::__construct($message, $code, $previous);
        
        $this->_site_error__options = $options;
    }
    
    public function _public__site_error__get_options() {
        return $this->_site_error__options;
    }
}

function get_error_options__ns14329($e) {
    return $e->_public__site_error__get_options();
}


