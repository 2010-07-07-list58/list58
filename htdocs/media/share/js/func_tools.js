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
    
    var func_tools_module_name = '/2010/07/07/List58/share/func_tools'
    
    if(!window[func_tools_module_name]) {
        var html_ns = 'http://www.w3.org/1999/xhtml'
        
        function FuncToolsModule() {}
        
        function new_func_tools_module() {
            var func_tools_module = new FuncToolsModule
            func_tools_module.init()
            return func_tools_module
        }
        
        FuncToolsModule.prototype.init = function() {}
        
        FuncToolsModule.prototype.args_array = function(raw_args) {
            var args = []
        
            for(var i = 0; i < raw_args.length; ++i) {
                args.push(raw_args[i])
            }
            
            return args
        }
        
        FuncToolsModule.prototype.func_bind = function(func, this_arg) {
            var args = this.args_array(arguments).slice(2)
            
            if(func.bind) {
                // using built 'func.bind()'. this is more effective way
                
                var bound = func.bind.apply(func, [this_arg].concat(args))
                
                return bound
            } else {
               // using emulation  of 'func.bind()'. this is less effective way
                
                var self_module = this
                
                var bound = function() {
                    var func_args = args.concat(self_module.args_array(arguments))
                    var func_res = func.apply(this_arg, func_args)
                    
                    return func_res
                }
                
                return bound
            }
        }
        
        window[func_tools_module_name] = new_func_tools_module()
    }
})()

