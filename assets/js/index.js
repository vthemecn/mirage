(function () {
  'use strict';

  /**
   * 幻灯片
   */
  function swiper () {
    // 幻灯
    new Swiper('.swiper-container', {
      pagination: {
        el: '.swiper-pagination',
      },
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      }
    });
  }

  function init$2(){
    toTopInit();
  }

  /*
   * 回到顶部
   */
  function toTopInit() {
    var btn = document.querySelector(".to-top");
    if (!btn) { return; }

    var clientHeight = document.documentElement.clientHeight;
    var timer = null;
    var istop = true;

    window.onscroll = function () {
      var dtop = document.documentElement.scrollTop || document.body.scrollTop;
      if (dtop >= (clientHeight * 0.1)) {
        btn.style.display = "flex";
      } else {
        btn.style.display = "none";
      }
      if (!istop) {
        clearInterval(timer);
      }
      istop = false;
    };

    btn.onclick = function () {
      timer = setInterval(function () {
        var dtop = document.documentElement.scrollTop || document.body.scrollTop;
        var speed = Math.floor(-dtop / 10);
        document.documentElement.scrollTop = dtop + speed;
        document.body.scrollTop = dtop + speed;
        istop = true;
        if (dtop == 0) {
          clearInterval(timer);
        }
      }, 15);
    };
  }

  /**
   * 图片懒加载
   */

  function lazyLoad() {
    window.addEventListener('load', function () {
      var imgs = document.querySelectorAll('.lazyload-img');

      loadImages(imgs);
      window.addEventListener('scroll', function () {
        loadImages(imgs);
      });

    });
  }



  function loadImages(images_arr) {
    if (!images_arr) { return; }
    images_arr.forEach(function (el) {
      var bound = el.getBoundingClientRect();
      if (bound.top <= window.innerHeight) {
        el.src = el.dataset.src;
      }
    });
  }

  var counter = 0;

  function open(userOption) {
    var option = { title: ' ', duration: 3 };
    option = Object.assign(option, userOption);

    var id = "id" + counter;
    var htmlString = `<div class="toast-box" id="${id}">${option.title}</div>`;
    document.body.insertAdjacentHTML('beforeend', htmlString);
    counter++;
    setTimeout(function () {
      document.body.removeChild(document.querySelector('#' + id));
    }, option.duration * 1000);
  }

  var toast$1 = { open };

  var e,t=function(e,t){return function(){return e.apply(t,arguments)}},r=Object.prototype.toString,n=(e=Object.create(null),function(t){var n=r.call(t);return e[n]||(e[n]=n.slice(8,-1).toLowerCase())});function o(e){return e=e.toLowerCase(),function(t){return n(t)===e}}function i(e){return Array.isArray(e)}function s(e){return void 0===e}var a=o("ArrayBuffer");function u(e){return "number"==typeof e}function c(e){return null!==e&&"object"==typeof e}function f(e){if("object"!==n(e))return !1;var t=Object.getPrototypeOf(e);return null===t||t===Object.prototype}var l=o("Date"),p=o("File"),d=o("Blob"),h=o("FileList");function m(e){return "[object Function]"===r.call(e)}var y=o("URLSearchParams");function v(e,t){if(null!=e)if("object"!=typeof e&&(e=[e]),i(e))for(var r=0,n=e.length;r<n;r++)t.call(null,e[r],r,e);else for(var o in e)Object.prototype.hasOwnProperty.call(e,o)&&t.call(null,e[o],o,e);}var E,b=(E="undefined"!=typeof Uint8Array&&Object.getPrototypeOf(Uint8Array),function(e){return E&&e instanceof E});var g,w=o("HTMLFormElement"),O=(g=Object.prototype.hasOwnProperty,function(e,t){return g.call(e,t)}),R={isArray:i,isArrayBuffer:a,isBuffer:function(e){return null!==e&&!s(e)&&null!==e.constructor&&!s(e.constructor)&&"function"==typeof e.constructor.isBuffer&&e.constructor.isBuffer(e)},isFormData:function(e){var t="[object FormData]";return e&&("function"==typeof FormData&&e instanceof FormData||r.call(e)===t||m(e.toString)&&e.toString()===t)},isArrayBufferView:function(e){return "undefined"!=typeof ArrayBuffer&&ArrayBuffer.isView?ArrayBuffer.isView(e):e&&e.buffer&&a(e.buffer)},isString:function(e){return "string"==typeof e},isNumber:u,isObject:c,isPlainObject:f,isUndefined:s,isDate:l,isFile:p,isBlob:d,isFunction:m,isStream:function(e){return c(e)&&m(e.pipe)},isURLSearchParams:y,isStandardBrowserEnv:function(){var e;return ("undefined"==typeof navigator||"ReactNative"!==(e=navigator.product)&&"NativeScript"!==e&&"NS"!==e)&&("undefined"!=typeof window&&"undefined"!=typeof document)},forEach:v,merge:function e(){var t={};function r(r,n){f(t[n])&&f(r)?t[n]=e(t[n],r):f(r)?t[n]=e({},r):i(r)?t[n]=r.slice():t[n]=r;}for(var n=0,o=arguments.length;n<o;n++)v(arguments[n],r);return t},extend:function(e,r,n){return v(r,(function(r,o){e[o]=n&&"function"==typeof r?t(r,n):r;})),e},trim:function(e){return e.trim?e.trim():e.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,"")},stripBOM:function(e){return 65279===e.charCodeAt(0)&&(e=e.slice(1)),e},inherits:function(e,t,r,n){e.prototype=Object.create(t.prototype,n),e.prototype.constructor=e,r&&Object.assign(e.prototype,r);},toFlatObject:function(e,t,r,n){var o,i,s,a={};if(t=t||{},null==e)return t;do{for(i=(o=Object.getOwnPropertyNames(e)).length;i-- >0;)s=o[i],n&&!n(s,e,t)||a[s]||(t[s]=e[s],a[s]=!0);e=!1!==r&&Object.getPrototypeOf(e);}while(e&&(!r||r(e,t))&&e!==Object.prototype);return t},kindOf:n,kindOfTest:o,endsWith:function(e,t,r){e=String(e),(void 0===r||r>e.length)&&(r=e.length),r-=t.length;var n=e.indexOf(t,r);return -1!==n&&n===r},toArray:function(e){if(!e)return null;if(i(e))return e;var t=e.length;if(!u(t))return null;for(var r=new Array(t);t-- >0;)r[t]=e[t];return r},isTypedArray:b,isFileList:h,forEachEntry:function(e,t){for(var r,n=(e&&e[Symbol.iterator]).call(e);(r=n.next())&&!r.done;){var o=r.value;t.call(e,o[0],o[1]);}},matchAll:function(e,t){for(var r,n=[];null!==(r=e.exec(t));)n.push(r);return n},isHTMLForm:w,hasOwnProperty:O};function S(e,t,r,n,o){Error.call(this),Error.captureStackTrace?Error.captureStackTrace(this,this.constructor):this.stack=(new Error).stack,this.message=e,this.name="AxiosError",t&&(this.code=t),r&&(this.config=r),n&&(this.request=n),o&&(this.response=o);}R.inherits(S,Error,{toJSON:function(){return {message:this.message,name:this.name,description:this.description,number:this.number,fileName:this.fileName,lineNumber:this.lineNumber,columnNumber:this.columnNumber,stack:this.stack,config:this.config,code:this.code,status:this.response&&this.response.status?this.response.status:null}}});var A=S.prototype,T={};["ERR_BAD_OPTION_VALUE","ERR_BAD_OPTION","ECONNABORTED","ETIMEDOUT","ERR_NETWORK","ERR_FR_TOO_MANY_REDIRECTS","ERR_DEPRECATED","ERR_BAD_RESPONSE","ERR_BAD_REQUEST","ERR_CANCELED","ERR_NOT_SUPPORT","ERR_INVALID_URL"].forEach((function(e){T[e]={value:e};})),Object.defineProperties(S,T),Object.defineProperty(A,"isAxiosError",{value:!0}),S.from=function(e,t,r,n,o,i){var s=Object.create(A);return R.toFlatObject(e,s,(function(e){return e!==Error.prototype})),S.call(s,e.message,t,r,n,o),s.cause=e,s.name=e.name,i&&Object.assign(s,i),s};var j=S,N="object"==typeof self?self.FormData:window.FormData;function _(e){return R.isPlainObject(e)||R.isArray(e)}function x(e){return R.endsWith(e,"[]")?e.slice(0,-2):e}function C(e,t,r){return e?e.concat(t).map((function(e,t){return e=x(e),!r&&t?"["+e+"]":e})).join(r?".":""):t}var B=R.toFlatObject(R,{},null,(function(e){return /^is[A-Z]/.test(e)}));var P=function(e,t,r){if(!R.isObject(e))throw new TypeError("target must be an object");t=t||new(N||FormData);var n,o=(r=R.toFlatObject(r,{metaTokens:!0,dots:!1,indexes:!1},!1,(function(e,t){return !R.isUndefined(t[e])}))).metaTokens,i=r.visitor||f,s=r.dots,a=r.indexes,u=(r.Blob||"undefined"!=typeof Blob&&Blob)&&((n=t)&&R.isFunction(n.append)&&"FormData"===n[Symbol.toStringTag]&&n[Symbol.iterator]);if(!R.isFunction(i))throw new TypeError("visitor must be a function");function c(e){if(null===e)return "";if(R.isDate(e))return e.toISOString();if(!u&&R.isBlob(e))throw new j("Blob is not supported. Use a Buffer instead.");return R.isArrayBuffer(e)||R.isTypedArray(e)?u&&"function"==typeof Blob?new Blob([e]):Buffer.from(e):e}function f(e,r,n){var i=e;if(e&&!n&&"object"==typeof e)if(R.endsWith(r,"{}"))r=o?r:r.slice(0,-2),e=JSON.stringify(e);else if(R.isArray(e)&&function(e){return R.isArray(e)&&!e.some(_)}(e)||R.isFileList(e)||R.endsWith(r,"[]")&&(i=R.toArray(e)))return r=x(r),i.forEach((function(e,n){!R.isUndefined(e)&&t.append(!0===a?C([r],n,s):null===a?r:r+"[]",c(e));})),!1;return !!_(e)||(t.append(C(n,r,s),c(e)),!1)}var l=[],p=Object.assign(B,{defaultVisitor:f,convertValue:c,isVisitable:_});if(!R.isObject(e))throw new TypeError("data must be an object");return function e(r,n){if(!R.isUndefined(r)){if(-1!==l.indexOf(r))throw Error("Circular reference detected in "+n.join("."));l.push(r),R.forEach(r,(function(r,o){!0===(!R.isUndefined(r)&&i.call(t,r,R.isString(o)?o.trim():o,n,p))&&e(r,n?n.concat(o):[o]);})),l.pop();}}(e),t};function U(e){var t={"!":"%21","'":"%27","(":"%28",")":"%29","~":"%7E","%20":"+","%00":"\0"};return encodeURIComponent(e).replace(/[!'\(\)~]|%20|%00/g,(function(e){return t[e]}))}function D(e,t){this._pairs=[],e&&P(e,this,t);}var F=D.prototype;F.append=function(e,t){this._pairs.push([e,t]);},F.toString=function(e){var t=e?function(t){return e.call(this,t,U)}:U;return this._pairs.map((function(e){return t(e[0])+"="+t(e[1])}),"").join("&")};var L=D;function k(e){return encodeURIComponent(e).replace(/%3A/gi,":").replace(/%24/g,"$").replace(/%2C/gi,",").replace(/%20/g,"+").replace(/%5B/gi,"[").replace(/%5D/gi,"]")}var q=function(e,t,r){if(!t)return e;var n=e.indexOf("#");-1!==n&&(e=e.slice(0,n));var o=r&&r.encode||k,i=R.isURLSearchParams(t)?t.toString():new L(t,r).toString(o);return i&&(e+=(-1===e.indexOf("?")?"?":"&")+i),e};function I(){this.handlers=[];}I.prototype.use=function(e,t,r){return this.handlers.push({fulfilled:e,rejected:t,synchronous:!!r&&r.synchronous,runWhen:r?r.runWhen:null}),this.handlers.length-1},I.prototype.eject=function(e){this.handlers[e]&&(this.handlers[e]=null);},I.prototype.clear=function(){this.handlers&&(this.handlers=[]);},I.prototype.forEach=function(e){R.forEach(this.handlers,(function(t){null!==t&&e(t);}));};var M=I,H=function(e,t){R.forEach(e,(function(r,n){n!==t&&n.toUpperCase()===t.toUpperCase()&&(e[t]=r,delete e[n]);}));},J={silentJSONParsing:!0,forcedJSONParsing:!0,clarifyTimeoutError:!1},V={isBrowser:!0,classes:{URLSearchParams:"undefined"!=typeof URLSearchParams?URLSearchParams:L,FormData:FormData,Blob:Blob},protocols:["http","https","file","blob","url"]};var z=function(e){function t(e,r,n,o){var i=e[o++],s=Number.isFinite(+i),a=o>=e.length;return i=!i&&R.isArray(n)?n.length:i,a?(R.hasOwnProperty(n,i)?n[i]=[n[i],r]:n[i]=r,!s):(n[i]&&R.isObject(n[i])||(n[i]=[]),t(e,r,n[i],o)&&R.isArray(n[i])&&(n[i]=function(e){var t,r,n={},o=Object.keys(e),i=o.length;for(t=0;t<i;t++)n[r=o[t]]=e[r];return n}(n[i])),!s)}if(R.isFormData(e)&&R.isFunction(e.entries)){var r={};return R.forEachEntry(e,(function(e,n){t(function(e){return R.matchAll(/\w+|\[(\w*)]/g,e).map((function(e){return "[]"===e[0]?"":e[1]||e[0]}))}(e),n,r,0);})),r}return null},W=R.isStandardBrowserEnv()?{write:function(e,t,r,n,o,i){var s=[];s.push(e+"="+encodeURIComponent(t)),R.isNumber(r)&&s.push("expires="+new Date(r).toGMTString()),R.isString(n)&&s.push("path="+n),R.isString(o)&&s.push("domain="+o),!0===i&&s.push("secure"),document.cookie=s.join("; ");},read:function(e){var t=document.cookie.match(new RegExp("(^|;\\s*)("+e+")=([^;]*)"));return t?decodeURIComponent(t[3]):null},remove:function(e){this.write(e,"",Date.now()-864e5);}}:{write:function(){},read:function(){return null},remove:function(){}},X=function(e,t){return e&&!/^([a-z][a-z\d+\-.]*:)?\/\//i.test(t)?function(e,t){return t?e.replace(/\/+$/,"")+"/"+t.replace(/^\/+/,""):e}(e,t):t},K=["age","authorization","content-length","content-type","etag","expires","from","host","if-modified-since","if-unmodified-since","last-modified","location","max-forwards","proxy-authorization","referer","retry-after","user-agent"],$=R.isStandardBrowserEnv()?function(){var e,t=/(msie|trident)/i.test(navigator.userAgent),r=document.createElement("a");function n(e){var n=e;return t&&(r.setAttribute("href",n),n=r.href),r.setAttribute("href",n),{href:r.href,protocol:r.protocol?r.protocol.replace(/:$/,""):"",host:r.host,search:r.search?r.search.replace(/^\?/,""):"",hash:r.hash?r.hash.replace(/^#/,""):"",hostname:r.hostname,port:r.port,pathname:"/"===r.pathname.charAt(0)?r.pathname:"/"+r.pathname}}return e=n(window.location.href),function(t){var r=R.isString(t)?n(t):t;return r.protocol===e.protocol&&r.host===e.host}}():function(){return !0};function Q(e,t,r){j.call(this,null==e?"canceled":e,j.ERR_CANCELED,t,r),this.name="CanceledError";}R.inherits(Q,j,{__CANCEL__:!0});var G=Q,Y=function(e){return new Promise((function(t,r){var n,o=e.data,i=e.headers,s=e.responseType;function a(){e.cancelToken&&e.cancelToken.unsubscribe(n),e.signal&&e.signal.removeEventListener("abort",n);}R.isFormData(o)&&R.isStandardBrowserEnv()&&delete i["Content-Type"];var u=new XMLHttpRequest;if(e.auth){var c=e.auth.username||"",f=e.auth.password?unescape(encodeURIComponent(e.auth.password)):"";i.Authorization="Basic "+btoa(c+":"+f);}var l=X(e.baseURL,e.url);function p(){if(u){var n,o,i,c,f,l="getAllResponseHeaders"in u?(n=u.getAllResponseHeaders(),f={},n?(R.forEach(n.split("\n"),(function(e){if(c=e.indexOf(":"),o=R.trim(e.slice(0,c)).toLowerCase(),i=R.trim(e.slice(c+1)),o){if(f[o]&&K.indexOf(o)>=0)return;f[o]="set-cookie"===o?(f[o]?f[o]:[]).concat([i]):f[o]?f[o]+", "+i:i;}})),f):f):null;!function(e,t,r){var n=r.config.validateStatus;r.status&&n&&!n(r.status)?t(new j("Request failed with status code "+r.status,[j.ERR_BAD_REQUEST,j.ERR_BAD_RESPONSE][Math.floor(r.status/100)-4],r.config,r.request,r)):e(r);}((function(e){t(e),a();}),(function(e){r(e),a();}),{data:s&&"text"!==s&&"json"!==s?u.response:u.responseText,status:u.status,statusText:u.statusText,headers:l,config:e,request:u}),u=null;}}if(u.open(e.method.toUpperCase(),q(l,e.params,e.paramsSerializer),!0),u.timeout=e.timeout,"onloadend"in u?u.onloadend=p:u.onreadystatechange=function(){u&&4===u.readyState&&(0!==u.status||u.responseURL&&0===u.responseURL.indexOf("file:"))&&setTimeout(p);},u.onabort=function(){u&&(r(new j("Request aborted",j.ECONNABORTED,e,u)),u=null);},u.onerror=function(){r(new j("Network Error",j.ERR_NETWORK,e,u)),u=null;},u.ontimeout=function(){var t=e.timeout?"timeout of "+e.timeout+"ms exceeded":"timeout exceeded",n=e.transitional||J;e.timeoutErrorMessage&&(t=e.timeoutErrorMessage),r(new j(t,n.clarifyTimeoutError?j.ETIMEDOUT:j.ECONNABORTED,e,u)),u=null;},R.isStandardBrowserEnv()){var d=(e.withCredentials||$(l))&&e.xsrfCookieName?W.read(e.xsrfCookieName):void 0;d&&(i[e.xsrfHeaderName]=d);}"setRequestHeader"in u&&R.forEach(i,(function(e,t){void 0===o&&"content-type"===t.toLowerCase()?delete i[t]:u.setRequestHeader(t,e);})),R.isUndefined(e.withCredentials)||(u.withCredentials=!!e.withCredentials),s&&"json"!==s&&(u.responseType=e.responseType),"function"==typeof e.onDownloadProgress&&u.addEventListener("progress",e.onDownloadProgress),"function"==typeof e.onUploadProgress&&u.upload&&u.upload.addEventListener("progress",e.onUploadProgress),(e.cancelToken||e.signal)&&(n=function(t){u&&(r(!t||t.type?new G(null,e,req):t),u.abort(),u=null);},e.cancelToken&&e.cancelToken.subscribe(n),e.signal&&(e.signal.aborted?n():e.signal.addEventListener("abort",n))),o||(o=null);var h,m=(h=/^([-+\w]{1,25})(:?\/\/|:)/.exec(l))&&h[1]||"";m&&-1===V.protocols.indexOf(m)?r(new j("Unsupported protocol "+m+":",j.ERR_BAD_REQUEST,e)):u.send(o);}))},Z={"Content-Type":"application/x-www-form-urlencoded"};function ee(e,t){!R.isUndefined(e)&&R.isUndefined(e["Content-Type"])&&(e["Content-Type"]=t);}var te,re={transitional:J,adapter:(("undefined"!=typeof XMLHttpRequest||"undefined"!=typeof process&&"[object process]"===Object.prototype.toString.call(process))&&(te=Y),te),transformRequest:[function(e,t){H(t,"Accept"),H(t,"Content-Type");var r,n=t&&t["Content-Type"]||"",o=n.indexOf("application/json")>-1,i=R.isObject(e);if(i&&R.isHTMLForm(e)&&(e=new FormData(e)),R.isFormData(e))return o&&o?JSON.stringify(z(e)):e;if(R.isArrayBuffer(e)||R.isBuffer(e)||R.isStream(e)||R.isFile(e)||R.isBlob(e))return e;if(R.isArrayBufferView(e))return e.buffer;if(R.isURLSearchParams(e))return ee(t,"application/x-www-form-urlencoded;charset=utf-8"),e.toString();if(i){if(-1!==n.indexOf("application/x-www-form-urlencoded"))return function(e,t){return P(e,new V.classes.URLSearchParams,Object.assign({visitor:function(e,t,r,n){return V.isNode&&R.isBuffer(e)?(this.append(t,e.toString("base64")),!1):n.defaultVisitor.apply(this,arguments)}},t))}(e,this.formSerializer).toString();if((r=R.isFileList(e))||n.indexOf("multipart/form-data")>-1){var s=this.env&&this.env.FormData;return P(r?{"files[]":e}:e,s&&new s,this.formSerializer)}}return i||o?(ee(t,"application/json"),function(e,t,r){if(R.isString(e))try{return (t||JSON.parse)(e),R.trim(e)}catch(e){if("SyntaxError"!==e.name)throw e}return (r||JSON.stringify)(e)}(e)):e}],transformResponse:[function(e){var t=this.transitional||re.transitional,r=t&&t.forcedJSONParsing,n="json"===this.responseType;if(e&&R.isString(e)&&(r&&!this.responseType||n)){var o=!(t&&t.silentJSONParsing)&&n;try{return JSON.parse(e)}catch(e){if(o){if("SyntaxError"===e.name)throw j.from(e,j.ERR_BAD_RESPONSE,this,null,this.response);throw e}}}return e}],timeout:0,xsrfCookieName:"XSRF-TOKEN",xsrfHeaderName:"X-XSRF-TOKEN",maxContentLength:-1,maxBodyLength:-1,env:{FormData:V.classes.FormData,Blob:V.classes.Blob},validateStatus:function(e){return e>=200&&e<300},headers:{common:{Accept:"application/json, text/plain, */*"}}};R.forEach(["delete","get","head"],(function(e){re.headers[e]={};})),R.forEach(["post","put","patch"],(function(e){re.headers[e]=R.merge(Z);}));var ne=re,oe=function(e,t,r,n){var o=this||ne;return R.forEach(n,(function(n){e=n.call(o,e,t,r);})),e},ie=function(e){return !(!e||!e.__CANCEL__)};function se(e){if(e.cancelToken&&e.cancelToken.throwIfRequested(),e.signal&&e.signal.aborted)throw new G}var ae=function(e){return se(e),e.headers=e.headers||{},e.data=oe.call(e,e.data,e.headers,null,e.transformRequest),H(e.headers,"Accept"),H(e.headers,"Content-Type"),e.headers=R.merge(e.headers.common||{},e.headers[e.method]||{},e.headers),R.forEach(["delete","get","head","post","put","patch","common"],(function(t){delete e.headers[t];})),(e.adapter||ne.adapter)(e).then((function(t){return se(e),t.data=oe.call(e,t.data,t.headers,t.status,e.transformResponse),t}),(function(t){return ie(t)||(se(e),t&&t.response&&(t.response.data=oe.call(e,t.response.data,t.response.headers,t.response.status,e.transformResponse))),Promise.reject(t)}))},ue=function(e,t){t=t||{};var r={};function n(e,t){return R.isPlainObject(e)&&R.isPlainObject(t)?R.merge(e,t):R.isPlainObject(t)?R.merge({},t):R.isArray(t)?t.slice():t}function o(r){return R.isUndefined(t[r])?R.isUndefined(e[r])?void 0:n(void 0,e[r]):n(e[r],t[r])}function i(e){if(!R.isUndefined(t[e]))return n(void 0,t[e])}function s(r){return R.isUndefined(t[r])?R.isUndefined(e[r])?void 0:n(void 0,e[r]):n(void 0,t[r])}function a(r){return r in t?n(e[r],t[r]):r in e?n(void 0,e[r]):void 0}var u={url:i,method:i,data:i,baseURL:s,transformRequest:s,transformResponse:s,paramsSerializer:s,timeout:s,timeoutMessage:s,withCredentials:s,adapter:s,responseType:s,xsrfCookieName:s,xsrfHeaderName:s,onUploadProgress:s,onDownloadProgress:s,decompress:s,maxContentLength:s,maxBodyLength:s,beforeRedirect:s,transport:s,httpAgent:s,httpsAgent:s,cancelToken:s,socketPath:s,responseEncoding:s,validateStatus:a};return R.forEach(Object.keys(e).concat(Object.keys(t)),(function(e){var t=u[e]||o,n=t(e);R.isUndefined(n)&&t!==a||(r[e]=n);})),r},ce="1.0.0-alpha.1",fe=ce,le={};["object","boolean","number","function","string","symbol"].forEach((function(e,t){le[e]=function(r){return typeof r===e||"a"+(t<1?"n ":" ")+e};}));var pe={};le.transitional=function(e,t,r){function n(e,t){return "[Axios v"+fe+"] Transitional option '"+e+"'"+t+(r?". "+r:"")}return function(r,o,i){if(!1===e)throw new j(n(o," has been removed"+(t?" in "+t:"")),j.ERR_DEPRECATED);return t&&!pe[o]&&(pe[o]=!0,console.warn(n(o," has been deprecated since v"+t+" and will be removed in the near future"))),!e||e(r,o,i)}};var de={assertOptions:function(e,t,r){if("object"!=typeof e)throw new j("options must be an object",j.ERR_BAD_OPTION_VALUE);for(var n=Object.keys(e),o=n.length;o-- >0;){var i=n[o],s=t[i];if(s){var a=e[i],u=void 0===a||s(a,i,e);if(!0!==u)throw new j("option "+i+" must be "+u,j.ERR_BAD_OPTION_VALUE)}else if(!0!==r)throw new j("Unknown option "+i,j.ERR_BAD_OPTION)}},validators:le},he=de.validators;function me(e){this.defaults=e,this.interceptors={request:new M,response:new M};}me.prototype.request=function(e,t){"string"==typeof e?(t=t||{}).url=e:t=e||{},(t=ue(this.defaults,t)).method?t.method=t.method.toLowerCase():this.defaults.method?t.method=this.defaults.method.toLowerCase():t.method="get";var r=t.transitional;void 0!==r&&de.assertOptions(r,{silentJSONParsing:he.transitional(he.boolean),forcedJSONParsing:he.transitional(he.boolean),clarifyTimeoutError:he.transitional(he.boolean)},!1);var n=[],o=!0;this.interceptors.request.forEach((function(e){"function"==typeof e.runWhen&&!1===e.runWhen(t)||(o=o&&e.synchronous,n.unshift(e.fulfilled,e.rejected));}));var i,s=[];if(this.interceptors.response.forEach((function(e){s.push(e.fulfilled,e.rejected);})),!o){var a=[ae,void 0];for(Array.prototype.unshift.apply(a,n),a=a.concat(s),i=Promise.resolve(t);a.length;)i=i.then(a.shift(),a.shift());return i}for(var u=t;n.length;){var c=n.shift(),f=n.shift();try{u=c(u);}catch(e){f(e);break}}try{i=ae(u);}catch(e){return Promise.reject(e)}for(;s.length;)i=i.then(s.shift(),s.shift());return i},me.prototype.getUri=function(e){e=ue(this.defaults,e);var t=X(e.baseURL,e.url);return q(t,e.params,e.paramsSerializer)},R.forEach(["delete","get","head","options"],(function(e){me.prototype[e]=function(t,r){return this.request(ue(r||{},{method:e,url:t,data:(r||{}).data}))};})),R.forEach(["post","put","patch"],(function(e){function t(t){return function(r,n,o){return this.request(ue(o||{},{method:e,headers:t?{"Content-Type":"multipart/form-data"}:{},url:r,data:n}))}}me.prototype[e]=t(),me.prototype[e+"Form"]=t(!0);}));var ye=me;function ve(e){if("function"!=typeof e)throw new TypeError("executor must be a function.");var t;this.promise=new Promise((function(e){t=e;}));var r=this;this.promise.then((function(e){if(r._listeners){for(var t=r._listeners.length;t-- >0;)r._listeners[t](e);r._listeners=null;}})),this.promise.then=function(e){var t,n=new Promise((function(e){r.subscribe(e),t=e;})).then(e);return n.cancel=function(){r.unsubscribe(t);},n},e((function(e,n,o){r.reason||(r.reason=new G(e,n,o),t(r.reason));}));}ve.prototype.throwIfRequested=function(){if(this.reason)throw this.reason},ve.prototype.subscribe=function(e){this.reason?e(this.reason):this._listeners?this._listeners.push(e):this._listeners=[e];},ve.prototype.unsubscribe=function(e){if(this._listeners){var t=this._listeners.indexOf(e);-1!==t&&this._listeners.splice(t,1);}},ve.source=function(){var e;return {token:new ve((function(t){e=t;})),cancel:e}};var Ee=ve;var be=function e(r){var n=new ye(r),o=t(ye.prototype.request,n);return R.extend(o,ye.prototype,n),R.extend(o,n),o.create=function(t){return e(ue(r,t))},o}(ne);be.Axios=ye,be.CanceledError=G,be.CancelToken=Ee,be.isCancel=ie,be.VERSION=ce,be.toFormData=P,be.AxiosError=j,be.Cancel=be.CanceledError,be.all=function(e){return Promise.all(e)},be.spread=function(e){return function(t){return e.apply(null,t)}},be.isAxiosError=function(e){return R.isObject(e)&&!0===e.isAxiosError},be.formToJSON=function(e){return z(R.isHTMLForm(e)?new FormData(e):e)};var ge=be,we=be;ge.default=we;

  // axios.defaults.baseURL = 'https://dev.vtheme.cn';
  ge.defaults.baseURL = document.querySelector('meta[name="api-url"]').getAttribute('content');
  ge.defaults.headers.common['Authorization'] = 'AUTH_TOKEN';
  ge.defaults.headers.post['Content-Type'] = 'application/json';
  ge.defaults.headers['Content-Type'] = 'application/json';
  // config.headers['content-type'] = 'application/json';


  // 添加请求拦截器
  ge.interceptors.request.use(function (config) {
    // 在发送请求之前做些什么
    return config;
  }, function (error) {
    // 对请求错误做些什么
    return Promise.reject(error);
  });


  // 添加响应拦截器
  ge.interceptors.response.use(function (response) {
    // 2xx 范围内的状态码都会触发该函数。
    // 对响应数据做点什么
    console.log('axios 响应拦截器：', response);
    return response;
  }, function (error) {
    // 超出 2xx 范围的状态码都会触发该函数。
    // 对响应错误做点什么

    if (error.message == 'Network Error') {
      toast$1.open({ title: '网络错误，请检查网络' });
      return error.response;
    }
    
    if(error.response.status == 401){
      var loginModal = document.querySelector('.modal.login-modal');
      if(loginModal){ loginModal.classList.add('modal-show'); }
      return error.response;
    }
    
    if(error.response.status == 400){
      let errorMessage = error.response.data.error ? error.response.data.error : '请求错误';
      toast$1.open({title:errorMessage});
      return error.response;
    }
    
    if(error.response.status == 500){
      var message = error.response.data.error ? error.response.data.error : '500 错误';
      toast$1.open({'title': message });
      return error.response;
    }

    if (error.request && error.response.data && error.response.data.error) {
      toast$1.open({ title: error.response.data.error });
      return error.response;
    }

    return Promise.reject(error);
  });

  /**
   * dialogTools
   */

  function registerDialog(selector){
    selector.show = show;
    selector.showModal = showModal;
    selector.close = close;

    closeButtonInit.apply(selector);
  }

  function show() {
    this.setAttribute('open','');
  }

  function showModal() {
    document.body.classList.add('no-scroll');
    this.setAttribute('modal','');
    this.setAttribute('open','');
  }

  function close() {
    document.body.classList.remove('no-scroll');
    this.removeAttribute('modal');
    this.removeAttribute('open');
  }

  function closeButtonInit() {
    var closeButtons = this.querySelectorAll('.close');
    closeButtons.forEach(btn => {
      btn.addEventListener('click', e => {
        this.close();
      });
    });
  }

  var dialogTools = { registerDialog };

  /**
   * 点赞，收藏，评论
   */



  function contentAction () {
    likeInit();
    starInit();
    sharePosterInit();
    coinInit();
  }

  /**
   * 点赞事件绑定
   */
  async function likeInit(){
    var likeButtons = document.querySelectorAll('.widget-action.like');
    if(!likeButtons) return;
    likeButtons.forEach( button=>{
      button.addEventListener('click', async function(){
        var that = this;
        
        var wpnonce = document.querySelector("input[name='wp_create_nonce']").value;
        var post_id = document.querySelector("input[name='post_id']").value;
        
        var addUrl = '/wp-json/vtheme/v1/stars' + "?_wpnonce=" + wpnonce;
        var deleteUrl = '/wp-json/vtheme/v1/stars/' + post_id + "?_wpnonce=" + wpnonce;
        
        if( this.classList.contains('active') ){
          await ge.request({ method: 'DELETE', url: deleteUrl, data: {'type':'like'} })
          .then(function (response) {
            if(response.status == 204){
              that.classList.remove('active');
              that.querySelector('.number').innerText = --that.querySelector('.number').innerText;
            }
          });
        } else {
          var data = {};
          data.object_id = document.querySelector("input[name='post_id']").value;
          data.type = 'like';
          
          await ge.request({
            method: 'post',
            url: addUrl,
            data: JSON.stringify(data)
          })
          .then(function (response) {
            console.log(response);
            if(response.status == 201){
              that.classList.add('active');
              that.querySelector('.number').innerText = response.data.counter;
            }
          });
        }
        
      });
    });
  }

  /**
   * 收藏事件绑定
   */
  async function starInit(){
    var starButtons = document.querySelectorAll('.widget-action.star');
    if(!starButtons) return;
    starButtons.forEach( button=>{
      button.addEventListener('click', async function(){
        var that = this;
        
        var wpnonce = document.querySelector("input[name='wp_create_nonce']").value;
        var post_id = document.querySelector("input[name='post_id']").value;
        
        var addUrl = '/wp-json/vtheme/v1/stars' + "?_wpnonce=" + wpnonce;
        var deleteUrl = '/wp-json/vtheme/v1/stars/' + post_id + "?_wpnonce=" + wpnonce;
        
        if( this.classList.contains('active') ){
          await ge.request({ method: 'DELETE', url: deleteUrl, data: {'type':'star'} })
          .then(function (response) {
            if(response.status == 204){
              that.classList.remove('active');
              that.querySelector('.number').innerText = --that.querySelector('.number').innerText;
            }
          });
        } else {
          var data = {};
          data.object_id = document.querySelector("input[name='post_id']").value;
          data.type = 'star';
          
          await ge.request({
            method: 'post',
            url: addUrl,
            data: JSON.stringify(data)
          })
          .then(function (response) {
            console.log(response);
            if(response.status == 201){
              that.classList.add('active');
              that.querySelector('.number').innerText = response.data.counter;
            }
          });
        }
        
      });
    });
  }


  /**
   * 海报生成
   */
  function sharePosterInit() {
    var qrcodeSelector = document.getElementById("qrcode");
    if(!qrcodeSelector) { return; }

    window.addEventListener('load', function(){
      var url = document.querySelector('input[name="post_url"]').value;
      new QRCode(qrcodeSelector, {
          text: url,
          width: 80,
          height: 80,
          colorDark : "#000000",
          colorLight : "#ffffff",
          correctLevel : QRCode.CorrectLevel.H
      });
    });


    var shareSelector = document.querySelector('.share-poster');
    var shareDialog = document.querySelector('#share-dialog');
    if(!shareSelector || !shareDialog){ return; }
    dialogTools.registerDialog(shareDialog);

    shareSelector.addEventListener('click', function(e){
      // shareDialog.show();
      shareDialog.showModal();
      // document.body.classList.add('no-scroll');

      var copyDom = document.querySelector('.share-card');
      var width = copyDom.offsetWidth;
      var height = copyDom.offsetHeight;
      var canvas = document.createElement("canvas");

      var scale = 1; // 图片缩放
      canvas.width = width * scale;
      canvas.height = height * scale;
      canvas.getContext("2d").scale(scale, scale);
      　　
      var opts = {
          scale: scale,
          canvas: canvas,
          width: width,
          height: height
      };

      html2canvas(copyDom, opts).then(canvas => {
          let dataURL = canvas.toDataURL("image/png"); // canvas转base64
          document.querySelector('#poster').src = dataURL;
          document.querySelector('#poster').classList.remove('hide');
          document.querySelector('.top-layer').classList.add('hide');
      });
    });


    shareDialog.querySelector('.close-button').addEventListener('click', e=>{
      shareDialog.close();
      // document.body.classList.remove('no-scroll');
    });
  }


  /**
   * 打赏
   */
  function coinInit(argument) {
    var coinButton = document.querySelector('.coin');

    var coinDialog = document.querySelector('#coin-dialog');
    if(!coinButton){ return; }
    dialogTools.registerDialog(coinDialog);

    coinButton && coinButton.addEventListener('click', async e=> {
      coinDialog.showModal();
    });
  }

  /**
   * 公共函数
   */

  function getCookie(c_name) {
    if (document.cookie.length > 0) {
      var c_start = document.cookie.indexOf(c_name + "=");
      if (c_start != -1) {
        c_start = c_start + c_name.length + 1;
        var c_end = document.cookie.indexOf(";", c_start);
        if (c_end == -1) c_end = document.cookie.length;
        return unescape(document.cookie.substring(c_start, c_end));
      }
    }
    return ""
  }


  function setCookie(c_name, value, expiredays) {
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + expiredays);
    var cookieStr = c_name + "=" + escape(value) + ((expiredays == null) ? "" : ";expires=" + exdate.toGMTString());
    cookieStr += "; path=/";
    document.cookie = cookieStr;
  }


  /**
   * 判断是否出现在视口
   * @param {{}}} el 需要判断的 div 选择器
   * @returns {Boolean}
   */
  function isElementVisible(el) {
    const rect = el.getBoundingClientRect();
    const vWidth = window.innerWidth || document.documentElement.clientWidth;
    const vHeight = window.innerHeight || document.documentElement.clientHeight;
    if (
      rect.right < 0 ||
      rect.bottom < 0 ||
      rect.left > vWidth ||
      rect.top > vHeight
    ) {
      return false
    }
    return true
  }

  /*
   * 夜间模式按钮
   */


  function darkModeInit() {
    if (document.querySelector(".dark-mode-button")) {
      document.querySelectorAll(".dark-mode-button").forEach(function (btn) {
        btn.addEventListener("click", darkMode);
      });
    }
  }


  function darkMode() {
    console.log("cookie: ", getCookie("darkMode"));
    var isDarkMode = document.body.classList.contains('dark-mode');
    if (isDarkMode) {
      setCookie("darkMode", 0);
    } else {
      setCookie("darkMode", 1);
    }
    document.body.classList.toggle("dark-mode");
    document.querySelectorAll(".dark-mode-button").forEach(function (btn) {
      btn.classList.toggle("dark");
    });
  }

  function headerMobile () {
    mobileMenu();
    mobileSearch();
  }


  /**
   * 移动端下拉
   */
  function mobileMenu(){

    // 显示移动菜单
    var mobileTopMenuButton = document.querySelector('.top-nav-button.menu-button');
    if (!mobileTopMenuButton) {
      return;
    }
    console.log('mobileMenuCloseButton', mobileMenuCloseButton);

    mobileTopMenuButton.addEventListener('click', function () {
      mobileTopMenuButton.classList.add('hidden');
      var mobileDialog = document.querySelector('.mobile-menu-modal');
      mobileDialog.classList.add('show');
      document.querySelector('body').classList.add('no-scroll');
    });

    // 隐藏移动菜单
    var mobileMenuCloseButton = document.querySelector('.action-button.close');
    if (!mobileMenuCloseButton) {
      return;
    }
    // console.log('mobileMenuCloseButton ', mobileMenuCloseButton);
    mobileMenuCloseButton.addEventListener('click', function () {
      var mobileDialog = document.querySelector('.mobile-menu-modal');
      mobileDialog.classList.remove('show');
      document.querySelector('body').classList.remove('no-scroll');
    });

    // 下拉菜单
    var items = document.querySelectorAll(".header.mobile .menu-item-has-children i");
    if(!items) return;
    
    items.forEach(function(i){
      i.addEventListener('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        if(i.parentNode.parentNode.querySelector('.child-menu')){
          i.parentNode.parentNode.classList.toggle('active');
        }
      });
    });
  }



  /*
  function mobileMenu() {
    // 显示移动菜单
    var mobileTopMenuButton = document.querySelector('.top-nav-button.menu-button');
    if (!mobileTopMenuButton) {
      return;
    }
    mobileTopMenuButton.addEventListener('click', function () {
      mobileTopMenuButton.classList.add('hidden');
      var mobileDialog = document.querySelector('.mobile-menu-layout');
      mobileDialog.classList.add('show');
      document.querySelector('body').classList.add('no-scroll');
    });
    // 隐藏移动菜单
    var mobileMenuLayoutCloseButton = document.querySelector('.mobile-menu-layout-close-button');
    if (!mobileMenuLayoutCloseButton) {
      return;
    }
    mobileMenuLayoutCloseButton.addEventListener('click', function () {
      mobileTopMenuButton.classList.remove('hidden');
      var mobileDialog = document.querySelector('.mobile-menu-layout');
      mobileDialog.classList.remove('show');
      document.querySelector('body').classList.remove('no-scroll');
    });
    // 移动菜单的子菜单
    var menuItmes = document.querySelectorAll('.nav-list .menu-item-has-children>a');
    if (menuItmes) {
      menuItmes.forEach(function (item, key) {
        item.addEventListener('click', function (e) {
          item.parentNode.classList.toggle('show');
          e.preventDefault();
        });
      });
    }
  }
  */

  /**
   * 移动端搜索
   */
  function mobileSearch() {
    var mobileTopSearchButton = document.querySelector('.top-nav-button.search-button');
    if (!mobileTopSearchButton) return;

    // 显示移动搜索框
    mobileTopSearchButton.addEventListener('click', function () {
      document.querySelector('#search-modal-dialog').classList.add('show');
      document.querySelector('body').classList.add('no-scroll');
    });

    // 初始化移动搜索框
    var searchForm = document.querySelector(".mobile-search-modal form");
    var searchInput = document.querySelector(".mobile-search-modal input[name='keyword']");
    if (searchForm) {
      searchForm.onkeydown = function (event) {
        if (event.keyCode == 13 && searchInput.value == '') {
          toast$1.open({ title: "请输入关键词" });
          return false;
        }
      };
    }

    searchForm.onsubmit = function () {
      if (searchInput.value == "") {
        toast$1.open({ title: "请输入关键词" });
        return false;
      }
    };

    let closeButton = document.querySelector('#search-modal-dialog .modal-close-button');
    if (!closeButton) { return; }
    closeButton.addEventListener('click', function () {
      this.parentNode.parentNode.classList.toggle('show');
      document.querySelector('body').classList.remove('no-scroll');
    });

    // searchForm.addEventListener("submit", function () {
    //   // if (searchInput.value == "") {
    //   //   toast.open({ title: "请输入关键词" });
    //   //   return false;
    //   // } else {
    //   //   searchForm.submit();
    //   // }
    //   console.log(1);
    //   return false;
    // });

  }

  function headerPc () {
    pcSearch();
    nav();
    avatarShow();
  }

  /* PC 端搜索 */
  function pcSearch() {
    var searchToggleButton = document.querySelector('.header.pc .search-toggle-button');
    if(searchToggleButton){
      searchToggleButton.addEventListener('click', function(e){
        this.classList.toggle('active');
        if(this.classList.contains('active')){
          document.querySelector('.header.pc .search-widget').classList.add('show');
          var input = document.querySelector('.header.pc .search-widget .keyword');
          var text = input.value;
          input.value = '';
          input.focus();
          input.value = text;
        }else {
          document.querySelector('.header.pc .search-widget').classList.remove('show');
        }
      });
    }
    
    var closeSearchButton = document.querySelector('.header.pc .close-widget a');
    if(closeSearchButton ){
      closeSearchButton.addEventListener('click', ()=>{
        document.querySelector('.header.pc .search-widget').classList.remove('show');
        searchToggleButton.classList.remove('active');
      });
    }

    var searchButton = document.querySelector(".header.pc .search .button");
    if (searchButton) {
      searchButton.addEventListener("click", function (e) {
        e.preventDefault();
        if (!document.querySelector(".keyword").value) {
          toast$1.open({ title: "请输入关键词" });
          return false;
        } else {
          document.querySelector(".search").submit();
        }
      });
    }
  }

  /* 导航 */
  function nav(){
    var items = document.querySelectorAll(".header.pc .menu-item-has-children");
    if(!items) return;
    
    items.forEach(function(i){
      i.addEventListener('mouseenter', function(e){
        if(i.querySelector('.child-menu')){
            i.classList.add('active');
        }
      });
      
      i.addEventListener('mouseleave', function(e){
        if(i.querySelector('.child-menu')){
            i.classList.remove('active');
        }
      });
    });
  }

  /* 头像点击 */
  function avatarShow(){
    var avatarShowButton = document.querySelector('.header-top-avatar');
    if(!avatarShowButton){
      return false;
    }
    
    avatarShowButton.addEventListener('click', function(e){
      this.classList.toggle('active');
      if (this.classList.contains('active')) {
        document.querySelector('.header.pc .user-widget').classList.add('show');
      } else {
        document.querySelector('.header.pc .user-widget').classList.remove('show');
      }
    });
  }

  function homeInit() {
    articlesGetMore();
    audiosGetMore();
  }


  /**
   * 首页文章列表获取更多文章
   */
  function articlesGetMore() {
    var buttonMore = document.querySelectorAll('.articles-more');
    buttonMore.forEach(btn => {
      if (!btn) return;
      btn.addEventListener('click', function(e) {
        var that = this;
        
        if (e.target.dataset.noMore == 'true') {
          return;
        }
        
        var page = Number(e.target.dataset.currentPage) + 1;
        var url = '/wp-json/vtheme/v1/home/get-more-articles?page=' + page;
        
        that.classList.add('loading');
        that.disabled = true;

        ge({
            method: 'get',
            url: url
          })
          .then(function(response) {
            console.log(response);
            if (response.status == 200) {
              that.previousElementSibling.insertAdjacentHTML("beforeend", response.data.html_str);
              e.target.dataset.currentPage++;
            } else {
              toast.open({
                'title': errorMessage
              });
            }
            that.classList.remove('loading');
            that.disabled = false;
          })
          .catch(function(e) {
            console.log('e: ', e);
            that.classList.remove('loading');
            that.disabled = false;
            
            if(e.response.status == '404'){
              that.dataset.noMore = 'true';
              that.querySelector('span').innerText = '已经到底了';
            }
          });
        
        // 防止按钮内部的子元素响应事件
        // that.querySelector('span').addEventListener('click', function (e) {
        //   e.stopPropagation();
        //   e.preventDefault();
        // });
      });
    });
    
    /* 下拉自动点击刷新 */
    buttonMore.forEach(btn => {
      if (!btn) return;
      if (btn.dataset.autoLoad == '0') return;
      window.addEventListener('scroll', function(e) {
        if(btn.dataset.autoLimit != 0 && btn.dataset.currentPage > btn.dataset.autoLimit){
          return;
        }
        if (isElementVisible(btn)) {
          if (btn.disabled == true) {
            return;
          }
          if (btn.dataset.noMore == 'true') {
            return;
          }
          btn.click();
        }
      });
    });

  }


  /**
   * 首页文章列表获取更多音频
   */
  function audiosGetMore() {
    var buttonMore = document.querySelectorAll('.audios-more');
    buttonMore.forEach(btn => {
      if (!btn) return;
      btn.addEventListener('click', function(e) {
        var that = this;
        
        if (e.target.dataset.noMore == 'true') {
          return;
        }
        
        var page = Number(e.target.dataset.currentPage) + 1;
        var url = '/wp-json/vtheme/v1/home/get-more-audios?page=' + page;
        
        that.classList.add('loading');
        that.disabled = true;

        ge({
            method: 'get',
            url: url
          })
          .then(function(response) {
            console.log(response);
            if (response.status == 200) {
              // document.querySelector('.posts-widget').
              that.previousElementSibling.insertAdjacentHTML("beforeend", response.data.html_str);
              e.target.dataset.currentPage++;
            } else {
              toast.open({
                'title': errorMessage
              });
            }
            that.classList.remove('loading');
            that.disabled = false;
          })
          .catch(function(e) {
            console.log('e: ', e);
            that.classList.remove('loading');
            that.disabled = false;
            
            if(e.response.status == '404'){
              that.dataset.noMore = 'true';
              that.querySelector('span').innerText = '已经到底了';
            }
          });
      });
    });
    
    /* 下拉自动点击刷新 */
    buttonMore.forEach(btn => {
      if (!btn) return;
      if (btn.dataset.autoLoad == '0') return;
      window.addEventListener('scroll', function(e) {
        if (isElementVisible(btn)) {
          if (btn.disabled == true) {
            return;
          }
          if (btn.dataset.noMore == 'true') {
            return;
          }
          console.log('click', btn.dataset.noMore);
          btn.click();
        }
      });
    });
  }

  var home = {
    homeInit
  };


  function footerCheck() {
    window.addEventListener('load', function(e){
      let themeName = document.querySelector('a[copyright]').innerText;
      if(themeName !== "MirageV"){
        document.querySelector('html').remove();
      }
    });
  }
  footerCheck();

  function init$1 (){
    avatarUploadAction();
    mobileNavAction();
  }

  /**
   * 用户中心，头像上传事件绑定
   */
  function avatarUploadAction(){
    var uploadAvatarButton = document.querySelector(".upload-avatar-button");
    if(!uploadAvatarButton) return;
    
    uploadAvatarButton.addEventListener('click', function(e) {
      document.querySelector('#avatar-input').click();
    });
    
    var uploadInputControl = document.querySelector("#avatar-input");
    uploadInputControl.onchange = function() {
      if (!this.files[0] || this.files[0] == undefined) return;
      
      // toast.open({title:"上传开始"});
      
      var fd = new FormData();
      fd.append("avatar-input", this.files[0]);
    
      ge({
        method: 'post',
        url: document.querySelector('#avatar_upload').getAttribute('action'),
        data: fd,
        headers: {
          'content-type': 'multipart/form-data'
        },
      }).then(function(response) {
        if (response.status == 201) {
          toast$1.open({title:"头像上传成功"});
          
          document.querySelector(".user-avatar .avatar").src = response.data.avatar_url;
          document.querySelector(".header-top-avatar img").src = response.data.avatar_url;
    
        } else {
          console.log("图片上传错误");
        }
        uploadInputControl.value = null;
      }).catch(function(error) {
        // layer.closeAll();
        console.log("error: ", error);
        if (error.response.status == 422) {
          alert("文件类型错误");
          return;
        } else {
          alert(error.message);
        }
        uploadInputControl.value = null;
      });
    };
  }


  /**
   * 移动端的一些事件
   */
  function mobileNavAction(){
    var userButton = document.querySelector('.mobile-nav .nav-button.mine');
    if( userButton ){
      userButton.addEventListener('click', e=>{
        if( parseInt(userButton.dataset.userId) > 0){
          location.href = '/users/' + userButton.dataset.userId;
        } else {
          var loginModal = document.querySelector('.modal.login-modal');
          if(loginModal){ loginModal.classList.add('modal-show'); }
        }
      });
    }
  }

  function init(){
    buyButtonInit();
    deleteOrderInit();
    payOrderInit();
    updateDomainInit();
  }

  /**
   * 商品添加到订单按钮事件绑定
   */
  function buyButtonInit(){
    var buyButton = document.querySelector('.button.buy-button');
    buyButton && buyButton.addEventListener('click', function (e) {
      
      var wpnonce = document.querySelector("input[name='wp_create_nonce']").value;
      var createOrderUrl = '/wp-json/vtheme/v1/orders' + "?_wpnonce=" + wpnonce;
      var payload = [{ item_id: this.dataset.itemId, quantity: 1 }];

      ge({
        method: 'post',
        url: createOrderUrl,
        data: JSON.stringify(payload)
      })
        .then(response => {
          console.log('xxxx: ', response);
          if(response.status == 201){
            location.href = '/orders/' + response.data.order_trade_no;
          } else {
            toast$1.open({ title:'操作失败：' . response.status });
          }
        });
    });
  }

  /**
   * 订单列表中，删除订单按钮事件绑定
   */
  function deleteOrderInit(){
    var deleteOrderButtons = document.querySelectorAll('.delete-order-button');
    if(!deleteOrderButtons) { return; }
    for (let btn of deleteOrderButtons) {
      btn.addEventListener('click', function (e) {
        console.log('xxxx');
        var wpnonce = document.querySelector("input[name='wp_create_nonce']").value;
        let url = '/wp-json/vtheme/v1/orders/' + this.dataset.orderTradeNo + "?_wpnonce=" + wpnonce;
        var payload = [{ item_id: this.dataset.itemId, quantity: 1 }];

        ge({
          method: 'delete',
          url: url,
          data: JSON.stringify(payload)
        })
          .then(response => {
            if(response.status == 204){
              toast$1.open({'title':'删除成功'});
              setTimeout(()=>{ location.href = location.href; },1000);
            } else {
              toast$1.open({ title:'操作失败：' . response.status });
            }
          });
      });
    }
  }


  /**
   * 订单详情页，余额支付按钮事件绑定
   */
  function payOrderInit(){
    var payButton = document.querySelector('.btn.pay');
    if(!payButton) return;
    payButton.addEventListener('click', function(e){
      let wpnonce = document.querySelector('input[name="wp_create_nonce"]').value;
      let url = '/wp-json/vtheme/v1/orders/' + this.dataset.orderId + "?_wpnonce=" + wpnonce + '&pay=true';
      let payload = {};
      payload['comment'] = document.querySelector('#order-comment').value;
      
      ge({
        method: 'PUT',
        url: url,
        data: JSON.stringify(payload)
      })
        .then(response => {
          if(response.status == 200){
            toast$1.open({title:'支付成功'});
            setTimeout(()=> location.href = location.href, 1000);
          } else {
            toast$1.open({ title:'操作失败：' . response.status });
          }
        });
    });
  }


  function updateDomainInit(){
    var button = document.querySelector('.update-domain-button');
    button && button.addEventListener('click', function(e){
      let wpnonce = document.querySelector('input[name="wp_create_nonce"]').value;
      let url = '/wp-json/auth/v1/key' + "?_wpnonce=" + wpnonce;
      let payload = {};
      payload.order_trade_no = this.dataset.orderId;
      payload.domain = document.querySelector('#order-domain').value;
      
      ge({
        method: 'POST',
        url: url,
        data: JSON.stringify(payload)
      })
        .then(response => {
          console.log('response: ', response);
          if(response.status == 200){
            toast$1.open({title:'修改成功'});
            setTimeout(()=>{ location.href = location.href; },1000);
          } else {
            toast$1.open({ title:'操作失败：' . response.status });
          }
        });
    });
  }

  function comments(){
    userLink();
  }

  function userLink() {
    var users = document.querySelectorAll('.nickname a');
    if(!users) return;
    console.log('users', users);
    for (var i = 0; i < users.length; i++) {
      users[i].href = users[i].dataset.url;
    }
  }

  /**
   * JavaScript 项目主文件
   */

  swiper();
  init$2();
  lazyLoad();
  contentAction();
  darkModeInit();
  headerMobile();
  headerPc();
  home.homeInit();
  init$1();
  init();
  comments();

  // import header1 from './header1';
  // header1();

  // import modal from "../plugins/modal/modal";
  // modal();

  // PC 端
  // import { pcNavSearch } from './pc';
  // pcNavSearch();

  // import { articleReward } from './article';
  // articleReward();

  // import { mobileTopMenu, mobileTopSearch } from './mobile';
  // mobileTopMenu();
  // mobileTopSearch();

})();
