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

class item_list_widget__ns28376 {
    protected $_items;
    
    public function __construct($items) {
        $this->_items = $items;
    }
    
    protected function _item_list_widget__get_detail_link($item_id, $label) {
        $link = '?'.http_build_query(array(
            'node' => 'item_detail_frame',
            'item_id' => $item_id,
        ));
        
        $html = sprintf('<a href="%s" rel="big_frame_fancybox">%s</a>', htmlspecialchars($link), htmlspecialchars($label));
        
        return $html;
    }
    
    public function get_widget() {
        $html = '';
        
        $html .= '<table class="ItemTable MinWidth700Px Width100Per">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Id</th>';
        $html .= '<th>Фамилия И О</th>';
        $html .= '<th>Дата Рождения</th>';
        $html .= '<th>Телефон</th>';
        $html .= '<th>Доп. Описание</th>';
        $html .= '<th>Действия</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        
        $html .= '<tbody>';
        
        if($this->_items) {
            foreach($this->_items as $item) {
                $item_id = $item['id'];
                
                $html .= '<tr>';
                $html .= sprintf('<td>%s</td>', $this->_item_list_widget__get_detail_link(
                    $item_id,
                    $item_id
                ));
                $html .= sprintf('<td>%s</td>', $this->_item_list_widget__get_detail_link(
                    $item_id,
                    ($item['family_name']?$item['family_name']:'••••••••••').' '.
                    ($item['given_name']?mb_substr($item['given_name'], 0, 1, 'utf-8'):'•').' '.
                    ($item['patronymic_name']?mb_substr($item['patronymic_name'], 0, 1, 'utf-8'):'•')
                ));
                $html .= sprintf('<td>%s</td>', $this->_item_list_widget__get_detail_link(
                    $item_id,
                    ($item['birth_year'] || $item['birth_month'] || $item['birth_day'])?
                    sprintf('%02s.%02s.%s', $item['birth_day'], $item['birth_month'], $item['birth_year']):''
                ));
                $html .= sprintf('<td>%s</td>', $this->_item_list_widget__get_detail_link(
                    $item_id,
                    $item['phone']?$item['phone']:$item['phone2']
                ));
                $html .= sprintf('<td>%s</td>', str_replace(
                    "\n", '<br />',
                    htmlspecialchars(
                        mb_strlen($item['about'], 'utf-8') < 100?$item['about']:mb_substr($item['about'], 0, 100, 'utf-8').'...'
                    )
                ));
                $html .= '<td><!-- в разработке /--></td>';
                $html .= '</tr>';
            }
            
            # DEBUG:
            #$html .= '<tr>';
            #$html .= '<td colspan="6"><pre>'.htmlspecialchars(print_r($this->_items, TRUE)).'</pre></td>';
            #$html .= '</tr>';
        } else {
            $html .= '<tr>';
            $html .= '<td colspan="6"><p class="TextAlignCenter Padding20Px">(Пусто)</p></td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        
        return $html;
    }
}

