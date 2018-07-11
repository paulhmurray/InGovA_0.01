    function createCookie(name,value,days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            var expires = "; expires="+date.toGMTString();
        }
        else var expires = "";
        document.cookie = name+"="+value+expires+"; path=/";
    }

    function readCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }

    function eraseCookie(name) {
        createCookie(name,"",-1);
    }     
    
    getviewport = function() {
			var $n = jQuery.noConflict();  
			var ua = navigator.userAgent;
			var device = {
				iphone: ua.match(/(iPhone|iPod|iPad)/),
				android: ua.match(/Android/),
				blackberry: ua.match(/BlackBerry/)
			};
			
			if(device.iphone || device.android || device.blackberry) {
 
				var viewportwidth;
                var viewportheight;
 
                if (typeof window.innerWidth != 'undefined') {
                	viewportwidth = window.innerWidth - 40;
                	viewportheight = window.innerHeight - 40;
                } else if(typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0) {
                	viewportwidth = document.documentElement.clientWidth - 40;
                	viewportheight = document.documentElement.clientHeight - 40;
                } else {
                	viewportwidth = document.getElementsByTagName('body')[0].clientWidth - 40;
                	viewportheight = document.getElementsByTagName('body')[0].clientHeight - 40;
                }
                return [
                	viewportwidth - 40,
                	viewportheight - 40,
                	$n(document).scrollLeft() + 40,
                	$n(document).scrollTop() + 40
                ];
            } else {
                return [
                	$n(window).width() - (40 * 2),
					$n(window).height() - (40 * 2),
					$n(document).scrollLeft() + 40,
					$n(document).scrollTop() + 40
                ];
            }
		}