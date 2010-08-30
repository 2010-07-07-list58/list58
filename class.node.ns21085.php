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

require_once dirname(__FILE__).'/class.node_base.ns8054.php';

class node__ns21085 extends node_base__ns8054 {
    protected $_node_base__need_db = TRUE;
    
    protected function _node__get_title() {
        return 'List58.Ru';
    }
    
    protected function _node__get_head() {
        $html = '';
        
        $html .=
            '<meta http-equiv="X-UA-Compatible" content="chrome=1" />'.
            '<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />'.
            '<title>'.htmlspecialchars($this->_node__get_title()).'</title>'.
            '<script type="text/javascript" src="/media/share/js/google-chrome-frame-for-microsoft-ie.js"></script>'.
            '<link rel="stylesheet" type="text/css" href="/media/share/css/style.css" />';//.
            //'<script type="text/javascript" src="/media/share/js/jquery-1.4.2.js"></script>'.
            //'<link rel="stylesheet" type="text/css" '.
            //    'href="/media/share/js/jquery.fancybox-1.3.1/fancybox/jquery.fancybox-1.3.1.css" />'.
            //'<script type="text/javascript" '.
            //    'src="/media/share/js/jquery.fancybox-1.3.1/fancybox/jquery.fancybox-1.3.1.js"></script>';
        
        return $html;
    }
    
    protected function _node__get_menu() {
        $menu = array();
        
        if($_SESSION['authorized']) {
            $menu[] = array(
                'menu_name' => 'Начало',
                'menu_link' => '?'
            );
            
            if($this->_node_base__is_permitted('search_items')) {
                // меню для тех кому разрешено искать Элементы Данных
                
                $menu[] = array(
                    'menu_name' => 'Поиск Данных',
                    'menu_link' => '?node=search_items'
                );
                
                if($this->_node_base__is_permitted('new_items')) {
                    // меню для тех кому дополнительно разрешено и
                    //  создавать Новые Элементы Данных
                    
                    $menu[] = array(
                        'menu_name' => 'Новые Данные',
                        'menu_link' => '?node=new_items'
                    );
                }
            }
            
            $menu[] = array(
                'menu_name' => 'Выход ['.$_SESSION['reg_data']['login'].']',
                'menu_link' => sprintf(
                    '?node=exit&post_key=%s',
                    urlencode($_SESSION['post_key'])
                )
            );
        } else {
            $menu[] = array(
                'menu_name' => 'Вход',
                'menu_link' => '?node=auth'
            );
        }
        
        $menu[] = array('menu_name' => 'О Системе', 
            'menu_link' => '?node=about');
        
        return $menu;
    }
    
    protected function _node__get_menu_widget() {
        $menu = $this->_node__get_menu();
        
        $htmls = array();
        
        foreach($menu as $menu_item) {
            $htmls[] = 
                '<a href="'.htmlspecialchars($menu_item['menu_link']).'" >'.
                    htmlspecialchars($menu_item['menu_name']).
                '</a> ';
        }
        
        $html = '';
        
        $html .= join(' | ', $htmls);
        
        return $html;
    }
    
    protected function _node__get_aside() {
        throw new abstract_function_error__ns8054();
    }
    
    protected function _node__get_body() {
        $html = '';
        
        $html .=
            '<table class="Width100Per Height100Per">'.
                '<tr>'.
                    '<td class="Padding10Px MarginColor">'.
                        $this->_node__get_menu_widget().
                    '</td>'.
                '</tr>'.
                '<tr>'.
                    '<td class="Height100Per Padding10Px">'.
                        '<table class="MarginAuto">'.
                            '<tr>'.
                                '<td>'.
                                    $this->_node__get_aside().
                                '</td>'.
                            '</tr>'.
                        '</table>'.
                    '</td>'.
                '</tr>'.
                '<tr>'.
                    '<td class="Padding10Px MarginColor"></td>'.
                '</tr>'.
            '</table>';
        
        return $html;
    }
    
    protected function _node_base__get_html() {
        $html = '';
        
        $html .=
            '<!DOCTYPE html>'."\n".
            '<html>'.
            '<head>'.
                $this->_node__get_head().
            '</head>'.
            '<body>'.
                $this->_node__get_body().
            '</body>'.
            '</html>';
        
        return $html;
    }
}




