window.Modernizr=function(e,t,n){function r(e){y.cssText=e}function o(e,t){return r(b.join(e+";")+(t||""))}function i(e,t){return typeof e===t}function a(e,t){return!!~(""+e).indexOf(t)}function c(e,t){for(var r in e){var o=e[r];if(!a(o,"-")&&y[o]!==n)return"pfx"==t?o:!0}return!1}function l(e,t,r){for(var o in e){var a=t[e[o]];if(a!==n)return r===!1?e[o]:i(a,"function")?a.bind(r||t):a}return!1}function s(e,t,n){var r=e.charAt(0).toUpperCase()+e.slice(1),o=(e+" "+w.join(r+" ")+r).split(" ");return i(t,"string")||i(t,"undefined")?c(o,t):(o=(e+" "+S.join(r+" ")+r).split(" "),l(o,t,n))}var u="2.8.3",d={},f=!0,p=t.documentElement,h="modernizr",m=t.createElement(h),y=m.style,v,g={}.toString,b=" -webkit- -moz- -o- -ms- ".split(" "),E="Webkit Moz O ms",w=E.split(" "),S=E.toLowerCase().split(" "),C={},j={},x={},z=[],M=z.slice,N,F=function(e,n,r,o){var i,a,c,l,s=t.createElement("div"),u=t.body,d=u||t.createElement("body");if(parseInt(r,10))for(;r--;)c=t.createElement("div"),c.id=o?o[r]:h+(r+1),s.appendChild(c);return i=["&#173;",'<style id="s',h,'">',e,"</style>"].join(""),s.id=h,(u?s:d).innerHTML+=i,d.appendChild(s),u||(d.style.background="",d.style.overflow="hidden",l=p.style.overflow,p.style.overflow="hidden",p.appendChild(d)),a=n(s,e),u?s.parentNode.removeChild(s):(d.parentNode.removeChild(d),p.style.overflow=l),!!a},T={}.hasOwnProperty,k;k=i(T,"undefined")||i(T.call,"undefined")?function(e,t){return t in e&&i(e.constructor.prototype[t],"undefined")}:function(e,t){return T.call(e,t)},Function.prototype.bind||(Function.prototype.bind=function(e){var t=this;if("function"!=typeof t)throw new TypeError;var n=M.call(arguments,1),r=function(){if(this instanceof r){var o=function(){};o.prototype=t.prototype;var i=new o,a=t.apply(i,n.concat(M.call(arguments)));return Object(a)===a?a:i}return t.apply(e,n.concat(M.call(arguments)))};return r}),C.flexbox=function(){return s("flexWrap")},C.backgroundsize=function(){return s("backgroundSize")};for(var O in C)k(C,O)&&(N=O.toLowerCase(),d[N]=C[O](),z.push((d[N]?"":"no-")+N));return d.addTest=function(e,t){if("object"==typeof e)for(var r in e)k(e,r)&&d.addTest(r,e[r]);else{if(e=e.toLowerCase(),d[e]!==n)return d;t="function"==typeof t?t():t,"undefined"!=typeof f&&f&&(p.className+=" "+(t?"":"no-")+e),d[e]=t}return d},r(""),m=v=null,function(e,t){function n(e,t){var n=e.createElement("p"),r=e.getElementsByTagName("head")[0]||e.documentElement;return n.innerHTML="x<style>"+t+"</style>",r.insertBefore(n.lastChild,r.firstChild)}function r(){var e=g.elements;return"string"==typeof e?e.split(" "):e}function o(e){var t=y[e[h]];return t||(t={},m++,e[h]=m,y[m]=t),t}function i(e,n,r){if(n||(n=t),v)return n.createElement(e);r||(r=o(n));var i;return i=r.cache[e]?r.cache[e].cloneNode():f.test(e)?(r.cache[e]=r.createElem(e)).cloneNode():r.createElem(e),!i.canHaveChildren||d.test(e)||i.tagUrn?i:r.frag.appendChild(i)}function a(e,n){if(e||(e=t),v)return e.createDocumentFragment();n=n||o(e);for(var i=n.frag.cloneNode(),a=0,c=r(),l=c.length;l>a;a++)i.createElement(c[a]);return i}function c(e,t){t.cache||(t.cache={},t.createElem=e.createElement,t.createFrag=e.createDocumentFragment,t.frag=t.createFrag()),e.createElement=function(n){return g.shivMethods?i(n,e,t):t.createElem(n)},e.createDocumentFragment=Function("h,f","return function(){var n=f.cloneNode(),c=n.createElement;h.shivMethods&&("+r().join().replace(/[\w\-]+/g,function(e){return t.createElem(e),t.frag.createElement(e),'c("'+e+'")'})+");return n}")(g,t.frag)}function l(e){e||(e=t);var r=o(e);return g.shivCSS&&!p&&!r.hasCSS&&(r.hasCSS=!!n(e,"article,aside,dialog,figcaption,figure,footer,header,hgroup,main,nav,section{display:block}mark{background:#FF0;color:#000}template{display:none}")),v||c(e,r),e}var s="3.7.0",u=e.html5||{},d=/^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i,f=/^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i,p,h="_html5shiv",m=0,y={},v;!function(){try{var e=t.createElement("a");e.innerHTML="<xyz></xyz>",p="hidden"in e,v=1==e.childNodes.length||function(){t.createElement("a");var e=t.createDocumentFragment();return"undefined"==typeof e.cloneNode||"undefined"==typeof e.createDocumentFragment||"undefined"==typeof e.createElement}()}catch(n){p=!0,v=!0}}();var g={elements:u.elements||"abbr article aside audio bdi canvas data datalist details dialog figcaption figure footer header hgroup main mark meter nav output progress section summary template time video",version:s,shivCSS:u.shivCSS!==!1,supportsUnknownElements:v,shivMethods:u.shivMethods!==!1,type:"default",shivDocument:l,createElement:i,createDocumentFragment:a};e.html5=g,l(t)}(this,t),d._version=u,d._prefixes=b,d._domPrefixes=S,d._cssomPrefixes=w,d.testProp=function(e){return c([e])},d.testAllProps=s,d.testStyles=F,p.className=p.className.replace(/(^|\s)no-js(\s|$)/,"$1$2")+(f?" js "+z.join(" "):""),d}(this,this.document),function(e,t,n){function r(e){return"[object Function]"==h.call(e)}function o(e){return"string"==typeof e}function i(){}function a(e){return!e||"loaded"==e||"complete"==e||"uninitialized"==e}function c(){var e=m.shift();y=1,e?e.t?f(function(){("c"==e.t?M.injectCss:M.injectJs)(e.s,0,e.a,e.x,e.e,1)},0):(e(),c()):y=0}function l(e,n,r,o,i,l,s){function u(t){if(!h&&a(d.readyState)&&(E.r=h=1,!y&&c(),d.onload=d.onreadystatechange=null,t)){"img"!=e&&f(function(){b.removeChild(d)},50);for(var r in j[n])j[n].hasOwnProperty(r)&&j[n][r].onload()}}var s=s||M.errorTimeout,d=t.createElement(e),h=0,v=0,E={t:r,s:n,e:i,a:l,x:s};1===j[n]&&(v=1,j[n]=[]),"object"==e?d.data=n:(d.src=n,d.type=e),d.width=d.height="0",d.onerror=d.onload=d.onreadystatechange=function(){u.call(this,v)},m.splice(o,0,E),"img"!=e&&(v||2===j[n]?(b.insertBefore(d,g?null:p),f(u,s)):j[n].push(d))}function s(e,t,n,r,i){return y=0,t=t||"j",o(e)?l("c"==t?w:E,e,t,this.i++,n,r,i):(m.splice(this.i++,0,e),1==m.length&&c()),this}function u(){var e=M;return e.loader={load:s,i:0},e}var d=t.documentElement,f=e.setTimeout,p=t.getElementsByTagName("script")[0],h={}.toString,m=[],y=0,v="MozAppearance"in d.style,g=v&&!!t.createRange().compareNode,b=g?d:p.parentNode,d=e.opera&&"[object Opera]"==h.call(e.opera),d=!!t.attachEvent&&!d,E=v?"object":d?"script":"img",w=d?"script":E,S=Array.isArray||function(e){return"[object Array]"==h.call(e)},C=[],j={},x={timeout:function(e,t){return t.length&&(e.timeout=t[0]),e}},z,M;M=function(e){function t(e){var e=e.split("!"),t=C.length,n=e.pop(),r=e.length,n={url:n,origUrl:n,prefixes:e},o,i,a;for(i=0;r>i;i++)a=e[i].split("="),(o=x[a.shift()])&&(n=o(n,a));for(i=0;t>i;i++)n=C[i](n);return n}function a(e,o,i,a,c){var l=t(e),s=l.autoCallback;l.url.split(".").pop().split("?").shift(),l.bypass||(o&&(o=r(o)?o:o[e]||o[a]||o[e.split("/").pop().split("?")[0]]),l.instead?l.instead(e,o,i,a,c):(j[l.url]?l.noexec=!0:j[l.url]=1,i.load(l.url,l.forceCSS||!l.forceJS&&"css"==l.url.split(".").pop().split("?").shift()?"c":n,l.noexec,l.attrs,l.timeout),(r(o)||r(s))&&i.load(function(){u(),o&&o(l.origUrl,c,a),s&&s(l.origUrl,c,a),j[l.url]=2})))}function c(e,t){function n(e,n){if(e){if(o(e))n||(s=function(){var e=[].slice.call(arguments);u.apply(this,e),d()}),a(e,s,t,0,c);else if(Object(e)===e)for(p in f=function(){var t=0,n;for(n in e)e.hasOwnProperty(n)&&t++;return t}(),e)e.hasOwnProperty(p)&&(!n&&!--f&&(r(s)?s=function(){var e=[].slice.call(arguments);u.apply(this,e),d()}:s[p]=function(e){return function(){var t=[].slice.call(arguments);e&&e.apply(this,t),d()}}(u[p])),a(e[p],s,t,p,c))}else!n&&d()}var c=!!e.test,l=e.load||e.both,s=e.callback||i,u=s,d=e.complete||i,f,p;n(c?e.yep:e.nope,!!l),l&&n(l)}var l,s,d=this.yepnope.loader;if(o(e))a(e,0,d,0);else if(S(e))for(l=0;l<e.length;l++)s=e[l],o(s)?a(s,0,d,0):S(s)?M(s):Object(s)===s&&c(s,d);else Object(e)===e&&c(e,d)},M.addPrefix=function(e,t){x[e]=t},M.addFilter=function(e){C.push(e)},M.errorTimeout=1e4,null==t.readyState&&t.addEventListener&&(t.readyState="loading",t.addEventListener("DOMContentLoaded",z=function(){t.removeEventListener("DOMContentLoaded",z,0),t.readyState="complete"},0)),e.yepnope=u(),e.yepnope.executeStack=c,e.yepnope.injectJs=function(e,n,r,o,l,s){var u=t.createElement("script"),d,h,o=o||M.errorTimeout;u.src=e;for(h in r)u.setAttribute(h,r[h]);n=s?c:n||i,u.onreadystatechange=u.onload=function(){!d&&a(u.readyState)&&(d=1,n(),u.onload=u.onreadystatechange=null)},f(function(){d||(d=1,n(1))},o),l?u.onload():p.parentNode.insertBefore(u,p)},e.yepnope.injectCss=function(e,n,r,o,a,l){var o=t.createElement("link"),s,n=l?c:n||i;o.href=e,o.rel="stylesheet",o.type="text/css";for(s in r)o.setAttribute(s,r[s]);a||(p.parentNode.insertBefore(o,p),f(n,0))}}(this,document),Modernizr.load=function(){yepnope.apply(window,[].slice.call(arguments,0))},Modernizr.addTest("cssvhunit",function(){var e;return Modernizr.testStyles("#modernizr { height: 50vh; }",function(t,n){var r=parseInt(window.innerHeight/2,10),o=parseInt((window.getComputedStyle?getComputedStyle(t,null):t.currentStyle).height,10);e=o==r}),e}),Modernizr.addTest("hiddenscroll",function(){return Modernizr.testStyles("#modernizr {width:100px;height:100px;overflow:scroll}",function(e){return e.offsetWidth===e.clientWidth})});