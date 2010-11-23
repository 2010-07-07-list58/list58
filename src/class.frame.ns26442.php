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

class frame__ns26442 extends base_node__ns8054 {
    protected function _frame__get_head() {
        $html = '';
        
        $html .=
            '<meta http-equiv="X-UA-Compatible" content="chrome=1" />'.
            '<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />'.
            '<title></title>'.
            '<script src="/media/share/js/google-chrome-frame-for-microsoft-ie.js"></script>'.
            '<link rel="shortcut icon" href="/media/share/favicon.png" />'.
            '<link rel="stylesheet" href="/media/share/css/style.css" />';
        
        return $html;
    }
    
    protected function _frame__get_aside() {
        throw new abstract_function_error__ns8054();
    }
    
    protected function _frame__get_body() {
        $html = '';
        
        $html .=
            '<table class="Width100Per Height100Per">'.
                '<tr>'.
                    '<td class="Height100Per Padding10Px">'.
                        '<table class="MarginAuto">'.
                            '<tr>'.
                                '<td>'.
                                    $this->_frame__get_aside().
                                '</td>'.
                            '</tr>'.
                        '</table>'.
                    '</td>'.
                '</tr>'.
            '</table>';
        
        return $html;
    }
    
    protected function _base_node__get_html() {
        $html = '';
        
        $html .=
            '<!DOCTYPE html>'."\n".
            '<html>'.
            '<head>'.
                $this->_frame__get_head().
            '</head>'.
            '<body>'.
                $this->_frame__get_body().
            '</body>'.
            '</html>';
        
        return $html;
    }
}

