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
    var search_element_id_prefix = '_search_items_node__advanced_search_element__'
    var advanced_search_types_params_name =
            '/2010/07/07/List58/search_items_node/dynamic_fields/advanced_search_types_params'
    var advanced_search_ids_params_name =
            '/2010/07/07/List58/search_items_node/dynamic_fields/advanced_search_ids_params'
    
    var func_tools = window['/2010/07/07/List58/share/func_tools']
    var meta_module = window['/2010/07/07/List58/share/meta']
    
    function create_remove_button(search_element) {
        var button = document.createElementNS(html_ns, 'html:input')
        button.type = 'button'
        button.value = 'Удалить параметр'
        
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
        var ids = meta_module.get_json_params(advanced_search_ids_params_name)
        
        if(ids) {
            for(var i = 0; i < ids.length; ++i) {
                var search_element_id = search_element_id_prefix + 'div__' + ids[i]
                var remove_noscript_id = search_element_id_prefix + 'remove_noscript__' + ids[i]
                
                add_delete_button_to_id(search_element_id, remove_noscript_id)
            }
        }
    }
    
    function SearchElementFactory() {}
    
    function new_search_element_factory() {
        var search_element_factory = new SearchElementFactory
        search_element_factory.init()
        return search_element_factory
    }
    
    SearchElementFactory.prototype.init = function() {
        this._search_types = meta_module.get_json_params(advanced_search_types_params_name)
    }
    
    SearchElementFactory.prototype.create_search_element = function(name_postfix) {
        var search_element = document.createElementNS(html_ns, 'html:div')
        
        var select = document.createElementNS(html_ns, 'html:select')
        select.style.cssFloat = 'left'
        select.style.margin = '5px'
        select.style.width = '200px'
        select.name = 'search_type__' + name_postfix
        
        for(var i = 0; i < this._search_types.length; ++i) {
            var search_type = this._search_types[i]
            
            var option = document.createElementNS(html_ns, 'html:option')
            option.value = search_type
            option.appendChild(document.createTextNode(search_type))
            
            select.appendChild(option)
        }
        
        var input = document.createElementNS(html_ns, 'html:input')
        input.style.cssFloat = 'left'
        input.style.margin = '5px'
        input.style.width = '300px'
        input.name = 'search_value__' + name_postfix
        
        var clear_div = document.createElementNS(html_ns, 'html:clear')
        clear_div.style.clear = 'both'
        
        var remove_button = create_remove_button(search_element)
        
        search_element.appendChild(select)
        search_element.appendChild(input)
        search_element.appendChild(remove_button)
        search_element.appendChild(clear_div)
        
        return search_element
    }
    
    function DynamicButtons() {
        this._next_id = 0
    }
    
    function new_dynamic_buttons(add_noscript_id, kwargs) {
        var new_dynamic_buttons = new DynamicButtons
        new_dynamic_buttons.init(add_noscript_id, kwargs)
        return new_dynamic_buttons
    }
    
    DynamicButtons.prototype.init = function(add_noscript_id, kwargs) {
        if(!kwargs) {
            kwargs = {}
        }
        
        if(kwargs.create_search_element) {
            this._create_search_element = kwargs.create_search_element
        } else {
            var search_element_factory = new_search_element_factory()
            this._create_search_element = 
                    func_tools.func_bind(
                            search_element_factory.create_search_element,
                            search_element_factory)
        }
        
        this._add_noscript_id = add_noscript_id
        this._add_button = this._create_add_button()
        
        this._add_elements_panel = document.createElementNS(html_ns, 'html:div')
        this._add_button_panel = document.createElementNS(html_ns, 'html:div')
        this._add_panel = document.createElementNS(html_ns, 'html:div')
        
        this._add_button_panel.appendChild(this._add_button)
        this._add_panel.appendChild(this._add_elements_panel)
        this._add_panel.appendChild(this._add_button_panel)
    }
    
    DynamicButtons.prototype._new_id = function(add_noscript_id) {
        var id = this._next_id
        ++this._next_id
        return id
    }
    
    DynamicButtons.prototype._on_add_button_click = function() {
        var name_postfix = 'dynamic_' + this._new_id()
        var search_element = this._create_search_element(name_postfix)
        
        this._add_elements_panel.appendChild(search_element)
    }
    
    DynamicButtons.prototype._create_add_button = function() {
        var button = document.createElementNS(html_ns, 'html:input')
        button.style.margin = '5px'
        button.type = 'button'
        button.value = 'Добавить параметр'
        
        button.addEventListener(
                'click', func_tools.func_bind(this._on_add_button_click, this), false)
        
        return button
    }
    
   DynamicButtons.prototype.show = function() {
        var add_noscript = document.getElementById(this._add_noscript_id)
        
        if(add_noscript && add_noscript.parentNode) {
            add_noscript.parentNode.replaceChild(this._add_panel, add_noscript)
        }
    }
    
    function main(event) {
        var dynamic_buttons = new_dynamic_buttons(
                '_search_items_node__advanced_search_params_noscript')
        
        add_delete_button_to_last()
        dynamic_buttons.show()
    }
    
    addEventListener('load', main, false)
})()

