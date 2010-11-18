// google-chrome-frame-for-microsoft-ie.js
//
// Authors:
//      Andrej A Antonov <polymorphm@gmail.com>
//
// Last Modified:
//      Fri 22 Oct 2010 17:56:11 MSD
//

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//


(function() {
    var add_event_listener = function(element, type, listener, use_capture) {
        // потомучто 'Microsoft IE' не поддерживает addEventListener()
        
        if(element.addEventListener !== undefined) {
            element.addEventListener(type, listener, use_capture)
        } else if(element.attachEvent !== undefined) {
            element.attachEvent('on' + type, listener)
        } else {
            throw "addEventListener() not implemented"
        }
    }
    
    var detect_msie = function() {
        // детектирование 'Microsoft IE' по характерным несоответствиям стандартам
        
        if( // детектирование версий: '6.0', '7.0', '8.0'
            window.addEventListener === undefined && // нет важной функции
            window.attachEvent !== undefined // но есть нестандартная альтернатива
        ) {
            return true
        }
        
        // <ЗДЕСЬ> в будущем возможно будет детектирование других версий
        
        return false
    }
    
    var make_clear_both_div = function() {
        var clearBothDiv = document.createElement('div')
        clearBothDiv.style.clear = 'both'
        
        return clearBothDiv
    }
    
    var make_google_chrome_frame_notify = function() {
        var install = document.createElement('input')
        install.type = 'button'
        install.value = 'Установить'
        install.style.cssFloat = 'right' // 'Microsoft IE' не поддерживает это
        install.style.styleFloat = 'right' // специально для 'Microsoft IE'
        add_event_listener(install, 'click', function(event) {
            location.assign('http://www.google.com/chromeframe/eula.html')
        }, false)
        
        var google_chrome_frame = document.createElement('span')
        google_chrome_frame.style.fontWeight = 'bold'
        google_chrome_frame.appendChild(
            document.createTextNode(
                'Chrome Frame'
            )
        )
        
        var learn_more = document.createElement('span')
        learn_more.style.cursor = 'pointer'
        learn_more.style.color = 'rgb(0,0,255)'
        learn_more.appendChild(
            document.createTextNode(
                'Узнать больше'
            )
        )
        add_event_listener(learn_more, 'click', function(event) {
            location.assign('http://code.google.com/intl/ru/chrome/chromeframe/')
        }, false)
        
        var text = document.createElement('div')
        text.style.padding = '5px'
        text.appendChild(
            document.createTextNode(
                'У Вас не установлен компонент '
            )
        )
        text.appendChild(google_chrome_frame)
        text.appendChild(
            document.createTextNode(
                ', необходимый для корректной работы Вашего броузера ('
            )
        )
        text.appendChild(learn_more)
        text.appendChild(
            document.createTextNode(')')
        )
        
        var notify = document.createElement('div')
        notify.style.padding = '3px'
        notify.style.font = '12px "DejaVu Sans", "Sans", sans-serif'
        notify.style.border = '1px rgb(245,245,181) outset'
        notify.style.background = 'rgb(245,245,181)'
        notify.style.color = 'rgb(0,0,0)'
        notify.appendChild(install)
        notify.appendChild(text)
        notify.appendChild(make_clear_both_div())
        
        return notify
    }
    
    var show_notify = function(notify) {
        document.body.style.margin = '0'
        if(document.body.firstChild) {
            document.body.insertBefore(notify, document.body.firstChild)
        } else {
            document.body.appendChild(notify)
        }
    }
    
    var main = function(event) {
        if(detect_msie()) {
            var notify = make_google_chrome_frame_notify()
            
            show_notify(notify)
        }
    }
    
    add_event_listener(window, 'load', main, false)
})()


