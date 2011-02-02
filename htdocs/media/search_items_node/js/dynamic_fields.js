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

(function() {
    'use strict'
    
    var html_ns = 'http://www.w3.org/1999/xhtml'
    var id_search_element_prefix = '_search_items_node__advanced_search_element__'
    var advanced_search_params_ids_params_name =
            '/2010/07/07/List58/search_items_node/dynamic_fields/advanced_search_params_ids'
    
    var func_tools = window['/2010/07/07/List58/share/func_tools']
    var meta_module = window['/2010/07/07/List58/share/meta']
    
    function create_remove_button(search_element) {
        var button = document.createElementNS(html_ns, 'html:input')
        button.type = 'button'
        button.value = 'Удалить'
        
        button.style.cssFloat = 'right'
        button.style.margin = '5px'
        
        function on_click() {
            if(search_element.parentNode) {
                search_element.parentNode.removeChild(search_element)
            }
        }
        
        button.addEventListener('click', on_click, false)
        
        return button
    }
    
    function add_delete_button_to_id(search_element_id, remove_noscript_id) {
        var search_element = document.getElementById(search_element_id)
        var remove_noscript = document.getElementById(remove_noscript_id)
        
        if(search_element && remove_noscript && remove_noscript.parentNode) {
            var remove_button = create_remove_button(search_element)
            
            remove_noscript.parentNode.replaceChild(remove_button, remove_noscript)
        }
    }
    
    function add_delete_button_to_last() {
        var ids = meta_module.get_json_params(advanced_search_params_ids_params_name)
        
        for(var i = 0; i < ids.length; ++i) {
            var search_element_id = id_search_element_prefix + 'div__' + ids[i]
            var remove_noscript_id = id_search_element_prefix + 'remove_noscript__' + ids[i]
            
            add_delete_button_to_id(search_element_id, remove_noscript_id)
        }
    }
    
    function add_add_button() {
        var add_noscript = document.getElementById('_search_items_node__advanced_search_params_noscript')
        
        if(add_noscript && add_noscript.parentNode) {
            var fragment = document.createDocumentFragment()
            fragment.appendChild(document.createTextNode('(тут будет кнопка "добавить")'))
            
            add_noscript.parentNode.replaceChild(fragment, add_noscript)
        }
    }
    
    function main(event) {
        add_delete_button_to_last()
        add_add_button()
    }
    
    addEventListener('load', main, false)
})()

