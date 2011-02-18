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

require_once dirname(__FILE__).'/class.site_error.ns14329.php';
require_once dirname(__FILE__).'/class.base_node.ns8054.php';

class frame__ns26442 extends base_node__ns8054 {
    protected function _base_node__throw_site_error($message, $options=NULL) {
        throw_site_frame_error__ns14329($message, $options);
    }
    
    protected function _frame__get_head() {
        $html =
                '<meta charset="utf-8" />'.
                '<title></title>'.
                '<link rel="stylesheet" href="/media/share/css/style.css" />';
        
        return $html;
    }
    
    protected function _frame__get_aside() {
        throw new abstract_function_error__ns8054();
    }
    
    protected function _frame__get_body() {
        $html =
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
        $body_html = $this->_frame__get_body();
        $head_html = $this->_frame__get_head();
        
        $html =
                '<!DOCTYPE html>'."\n".
                '<html>'.
                '<head>'.
                    $head_html.
                '</head>'.
                '<body>'.
                    $body_html.
                '</body>'.
                '</html>';
        
        return $html;
    }
}

