(function () {
  'use strict';

  /**
   * 幻灯片
   */
  function swiper () {
    const swiperContainer = document.querySelector('.swiper-container');
    if (!swiperContainer) return;

    // 根据 data-autoplay 属性决定是否自动播放
    const autoplay = swiperContainer.dataset.autoplay == '1';
    const effect = swiperContainer.dataset.effect;

    new Swiper('.swiper-container', {
      pagination: {
        el: '.swiper-pagination',
      },
      // 根据 autoplay 变量决定是否启用自动播放
      autoplay: autoplay ? {
        delay: 5000,
        disableOnInteraction: false,
      } : false,
      loop: true,
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      effect: effect
    });
  }

  function init$1(){
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
   * 使用Intersection Observer API实现现代浏览器支持
   * 降级使用传统的滚动监听方式兼容老旧浏览器
   */

  // 配置参数
  const LAZY_LOAD_CONFIG = {
      offset: 100,   // 预加载偏移量(px)
      rootMargin: '100px 0px 100px 0px' // 视口边距
  };

  /**
   * 检测浏览器是否支持Intersection Observer API
   * @returns {boolean} 是否支持
   */
  function isIntersectionObserverSupported() {
      return 'IntersectionObserver' in window;
  }

  /**
   * 现代浏览器实现：使用Intersection Observer API
   */
  function modernLazyLoad() {
      const images = document.querySelectorAll('.lazyload-img');
      
      if (!images.length) return;

      const imageObserver = new IntersectionObserver((entries, observer) => {
          entries.forEach(entry => {
              if (entry.isIntersecting) {
                  const img = entry.target;
                  loadImage(img);
                  observer.unobserve(img);
              }
          });
      }, {
          rootMargin: LAZY_LOAD_CONFIG.rootMargin
      });

      images.forEach(img => imageObserver.observe(img));
  }

  /**
   * 传统浏览器实现：使用滚动监听
   */
  function legacyLazyLoad() {
      const images = document.querySelectorAll('.lazyload-img');
      
      if (!images.length) return;

      function loadVisibleImages() {
          images.forEach(img => {
              if (isElementInViewport(img, LAZY_LOAD_CONFIG.offset)) {
                  loadImage(img);
              }
          });
      }

      // 初始加载
      loadVisibleImages();

      // 滚动监听
      let ticking = false;
      window.addEventListener('scroll', () => {
          if (!ticking) {
              requestAnimationFrame(() => {
                  loadVisibleImages();
                  ticking = false;
              });
              ticking = true;
          }
      });
  }

  /**
   * 检查元素是否在视口中
   * @param {Element} element - 要检查的元素
   * @param {number} offset - 偏移量
   * @returns {boolean} 是否在视口中
   */
  function isElementInViewport(element, offset = 0) {
      const rect = element.getBoundingClientRect();
      return (
          rect.top <= (window.innerHeight + offset) &&
          rect.bottom >= -offset
      );
  }

  /**
   * 加载单张图片
   * @param {Element} img - 图片元素
   */
  function loadImage(img) {
      if (!img || img.classList.contains('loaded')) return;

      const src = img.dataset.src;
      if (!src) return;

      // 添加加载状态类
      img.classList.add('loading');

      const newImg = new Image();
      newImg.onload = function() {
          img.src = src;
          img.classList.remove('loading');
          img.classList.add('loaded');
      };

      newImg.onerror = function() {
          img.classList.remove('loading');
          img.classList.add('load-error');
      };

      newImg.src = src;
  }

  /**
   * 初始化懒加载功能
   */
  function lazyLoad() {

      // 等待DOM加载完成
      if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', initLazyLoad);
      } else {
          initLazyLoad();
      }
  }

  /**
   * 初始化懒加载
   */
  function initLazyLoad() {
      if (isIntersectionObserverSupported()) {
          modernLazyLoad();
      } else {
          legacyLazyLoad();
      }
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
    var cookieStr = c_name + "=" + escape(value) + ("" );
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

  /*! *****************************************************************************
  Copyright (c) Microsoft Corporation.

  Permission to use, copy, modify, and/or distribute this software for any
  purpose with or without fee is hereby granted.

  THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
  REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
  AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
  INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
  LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
  OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
  PERFORMANCE OF THIS SOFTWARE.
  ***************************************************************************** */

  var __assign = function() {
      __assign = Object.assign || function __assign(t) {
          for (var s, i = 1, n = arguments.length; i < n; i++) {
              s = arguments[i];
              for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
          }
          return t;
      };
      return __assign.apply(this, arguments);
  };

  var NotyfNotification = /** @class */ (function () {
      function NotyfNotification(options) {
          this.options = options;
          this.listeners = {};
      }
      NotyfNotification.prototype.on = function (eventType, cb) {
          var callbacks = this.listeners[eventType] || [];
          this.listeners[eventType] = callbacks.concat([cb]);
      };
      NotyfNotification.prototype.triggerEvent = function (eventType, event) {
          var _this = this;
          var callbacks = this.listeners[eventType] || [];
          callbacks.forEach(function (cb) { return cb({ target: _this, event: event }); });
      };
      return NotyfNotification;
  }());
  var NotyfArrayEvent;
  (function (NotyfArrayEvent) {
      NotyfArrayEvent[NotyfArrayEvent["Add"] = 0] = "Add";
      NotyfArrayEvent[NotyfArrayEvent["Remove"] = 1] = "Remove";
  })(NotyfArrayEvent || (NotyfArrayEvent = {}));
  var NotyfArray = /** @class */ (function () {
      function NotyfArray() {
          this.notifications = [];
      }
      NotyfArray.prototype.push = function (elem) {
          this.notifications.push(elem);
          this.updateFn(elem, NotyfArrayEvent.Add, this.notifications);
      };
      NotyfArray.prototype.splice = function (index, num) {
          var elem = this.notifications.splice(index, num)[0];
          this.updateFn(elem, NotyfArrayEvent.Remove, this.notifications);
          return elem;
      };
      NotyfArray.prototype.indexOf = function (elem) {
          return this.notifications.indexOf(elem);
      };
      NotyfArray.prototype.onUpdate = function (fn) {
          this.updateFn = fn;
      };
      return NotyfArray;
  }());

  var NotyfEvent;
  (function (NotyfEvent) {
      NotyfEvent["Dismiss"] = "dismiss";
      NotyfEvent["Click"] = "click";
  })(NotyfEvent || (NotyfEvent = {}));
  var DEFAULT_OPTIONS = {
      types: [
          {
              type: 'success',
              className: 'notyf__toast--success',
              backgroundColor: '#3dc763',
              icon: {
                  className: 'notyf__icon--success',
                  tagName: 'i',
              },
          },
          {
              type: 'error',
              className: 'notyf__toast--error',
              backgroundColor: '#ed3d3d',
              icon: {
                  className: 'notyf__icon--error',
                  tagName: 'i',
              },
          },
      ],
      duration: 2000,
      ripple: true,
      position: {
          x: 'right',
          y: 'bottom',
      },
      dismissible: false,
  };

  var NotyfView = /** @class */ (function () {
      function NotyfView() {
          this.notifications = [];
          this.events = {};
          this.X_POSITION_FLEX_MAP = {
              left: 'flex-start',
              center: 'center',
              right: 'flex-end',
          };
          this.Y_POSITION_FLEX_MAP = {
              top: 'flex-start',
              center: 'center',
              bottom: 'flex-end',
          };
          // Creates the main notifications container
          var docFrag = document.createDocumentFragment();
          var notyfContainer = this._createHTMLElement({ tagName: 'div', className: 'notyf' });
          docFrag.appendChild(notyfContainer);
          document.body.appendChild(docFrag);
          this.container = notyfContainer;
          // Identifies the main animation end event
          this.animationEndEventName = this._getAnimationEndEventName();
          this._createA11yContainer();
      }
      NotyfView.prototype.on = function (event, cb) {
          var _a;
          this.events = __assign(__assign({}, this.events), (_a = {}, _a[event] = cb, _a));
      };
      NotyfView.prototype.update = function (notification, type) {
          if (type === NotyfArrayEvent.Add) {
              this.addNotification(notification);
          }
          else if (type === NotyfArrayEvent.Remove) {
              this.removeNotification(notification);
          }
      };
      NotyfView.prototype.removeNotification = function (notification) {
          var _this = this;
          var renderedNotification = this._popRenderedNotification(notification);
          var node;
          if (!renderedNotification) {
              return;
          }
          node = renderedNotification.node;
          node.classList.add('notyf__toast--disappear');
          var handleEvent;
          node.addEventListener(this.animationEndEventName, (handleEvent = function (event) {
              if (event.target === node) {
                  node.removeEventListener(_this.animationEndEventName, handleEvent);
                  _this.container.removeChild(node);
              }
          }));
      };
      NotyfView.prototype.addNotification = function (notification) {
          var node = this._renderNotification(notification);
          this.notifications.push({ notification: notification, node: node });
          // For a11y purposes, we still want to announce that there's a notification in the screen
          // even if it comes with no message.
          this._announce(notification.options.message || 'Notification');
      };
      NotyfView.prototype._renderNotification = function (notification) {
          var _a;
          var card = this._buildNotificationCard(notification);
          var className = notification.options.className;
          if (className) {
              (_a = card.classList).add.apply(_a, className.split(' '));
          }
          this.container.appendChild(card);
          return card;
      };
      NotyfView.prototype._popRenderedNotification = function (notification) {
          var idx = -1;
          for (var i = 0; i < this.notifications.length && idx < 0; i++) {
              if (this.notifications[i].notification === notification) {
                  idx = i;
              }
          }
          if (idx !== -1) {
              return this.notifications.splice(idx, 1)[0];
          }
          return;
      };
      NotyfView.prototype.getXPosition = function (options) {
          var _a;
          return ((_a = options === null || options === void 0 ? void 0 : options.position) === null || _a === void 0 ? void 0 : _a.x) || 'right';
      };
      NotyfView.prototype.getYPosition = function (options) {
          var _a;
          return ((_a = options === null || options === void 0 ? void 0 : options.position) === null || _a === void 0 ? void 0 : _a.y) || 'bottom';
      };
      NotyfView.prototype.adjustContainerAlignment = function (options) {
          var align = this.X_POSITION_FLEX_MAP[this.getXPosition(options)];
          var justify = this.Y_POSITION_FLEX_MAP[this.getYPosition(options)];
          var style = this.container.style;
          style.setProperty('justify-content', justify);
          style.setProperty('align-items', align);
      };
      NotyfView.prototype._buildNotificationCard = function (notification) {
          var _this = this;
          var options = notification.options;
          var iconOpts = options.icon;
          // Adjust container according to position (e.g. top-left, bottom-center, etc)
          this.adjustContainerAlignment(options);
          // Create elements
          var notificationElem = this._createHTMLElement({ tagName: 'div', className: 'notyf__toast' });
          var ripple = this._createHTMLElement({ tagName: 'div', className: 'notyf__ripple' });
          var wrapper = this._createHTMLElement({ tagName: 'div', className: 'notyf__wrapper' });
          var message = this._createHTMLElement({ tagName: 'div', className: 'notyf__message' });
          message.innerHTML = options.message || '';
          var mainColor = options.background || options.backgroundColor;
          // Build the icon and append it to the card
          if (iconOpts) {
              var iconContainer = this._createHTMLElement({ tagName: 'div', className: 'notyf__icon' });
              if (typeof iconOpts === 'string' || iconOpts instanceof String)
                  iconContainer.innerHTML = new String(iconOpts).valueOf();
              if (typeof iconOpts === 'object') {
                  var _a = iconOpts.tagName, tagName = _a === void 0 ? 'i' : _a, className_1 = iconOpts.className, text = iconOpts.text, _b = iconOpts.color, color = _b === void 0 ? mainColor : _b;
                  var iconElement = this._createHTMLElement({ tagName: tagName, className: className_1, text: text });
                  if (color)
                      iconElement.style.color = color;
                  iconContainer.appendChild(iconElement);
              }
              wrapper.appendChild(iconContainer);
          }
          wrapper.appendChild(message);
          notificationElem.appendChild(wrapper);
          // Add ripple if applicable, else just paint the full toast
          if (mainColor) {
              if (options.ripple) {
                  ripple.style.background = mainColor;
                  notificationElem.appendChild(ripple);
              }
              else {
                  notificationElem.style.background = mainColor;
              }
          }
          // Add dismiss button
          if (options.dismissible) {
              var dismissWrapper = this._createHTMLElement({ tagName: 'div', className: 'notyf__dismiss' });
              var dismissButton = this._createHTMLElement({
                  tagName: 'button',
                  className: 'notyf__dismiss-btn',
              });
              dismissWrapper.appendChild(dismissButton);
              wrapper.appendChild(dismissWrapper);
              notificationElem.classList.add("notyf__toast--dismissible");
              dismissButton.addEventListener('click', function (event) {
                  var _a, _b;
                  (_b = (_a = _this.events)[NotyfEvent.Dismiss]) === null || _b === void 0 ? void 0 : _b.call(_a, { target: notification, event: event });
                  event.stopPropagation();
              });
          }
          notificationElem.addEventListener('click', function (event) { var _a, _b; return (_b = (_a = _this.events)[NotyfEvent.Click]) === null || _b === void 0 ? void 0 : _b.call(_a, { target: notification, event: event }); });
          // Adjust margins depending on whether its an upper or lower notification
          var className = this.getYPosition(options) === 'top' ? 'upper' : 'lower';
          notificationElem.classList.add("notyf__toast--" + className);
          return notificationElem;
      };
      NotyfView.prototype._createHTMLElement = function (_a) {
          var tagName = _a.tagName, className = _a.className, text = _a.text;
          var elem = document.createElement(tagName);
          if (className) {
              elem.className = className;
          }
          elem.textContent = text || null;
          return elem;
      };
      /**
       * Creates an invisible container which will announce the notyfs to
       * screen readers
       */
      NotyfView.prototype._createA11yContainer = function () {
          var a11yContainer = this._createHTMLElement({ tagName: 'div', className: 'notyf-announcer' });
          a11yContainer.setAttribute('aria-atomic', 'true');
          a11yContainer.setAttribute('aria-live', 'polite');
          // Set the a11y container to be visible hidden. Can't use display: none as
          // screen readers won't read it.
          a11yContainer.style.border = '0';
          a11yContainer.style.clip = 'rect(0 0 0 0)';
          a11yContainer.style.height = '1px';
          a11yContainer.style.margin = '-1px';
          a11yContainer.style.overflow = 'hidden';
          a11yContainer.style.padding = '0';
          a11yContainer.style.position = 'absolute';
          a11yContainer.style.width = '1px';
          a11yContainer.style.outline = '0';
          document.body.appendChild(a11yContainer);
          this.a11yContainer = a11yContainer;
      };
      /**
       * Announces a message to screenreaders.
       */
      NotyfView.prototype._announce = function (message) {
          var _this = this;
          this.a11yContainer.textContent = '';
          // This 100ms timeout is necessary for some browser + screen-reader combinations:
          // - Both JAWS and NVDA over IE11 will not announce anything without a non-zero timeout.
          // - With Chrome and IE11 with NVDA or JAWS, a repeated (identical) message won't be read a
          //   second time without clearing and then using a non-zero delay.
          // (using JAWS 17 at time of this writing).
          // https://github.com/angular/material2/blob/master/src/cdk/a11y/live-announcer/live-announcer.ts
          setTimeout(function () {
              _this.a11yContainer.textContent = message;
          }, 100);
      };
      /**
       * Determine which animationend event is supported
       */
      NotyfView.prototype._getAnimationEndEventName = function () {
          var el = document.createElement('_fake');
          var transitions = {
              MozTransition: 'animationend',
              OTransition: 'oAnimationEnd',
              WebkitTransition: 'webkitAnimationEnd',
              transition: 'animationend',
          };
          var t;
          for (t in transitions) {
              if (el.style[t] !== undefined) {
                  return transitions[t];
              }
          }
          // No supported animation end event. Using "animationend" as a fallback
          return 'animationend';
      };
      return NotyfView;
  }());

  /**
   * Main controller class. Defines the main Notyf API.
   */
  var Notyf = /** @class */ (function () {
      function Notyf(opts) {
          var _this = this;
          this.dismiss = this._removeNotification;
          this.notifications = new NotyfArray();
          this.view = new NotyfView();
          var types = this.registerTypes(opts);
          this.options = __assign(__assign({}, DEFAULT_OPTIONS), opts);
          this.options.types = types;
          this.notifications.onUpdate(function (elem, type) { return _this.view.update(elem, type); });
          this.view.on(NotyfEvent.Dismiss, function (_a) {
              var target = _a.target, event = _a.event;
              _this._removeNotification(target);
              // tslint:disable-next-line: no-string-literal
              target['triggerEvent'](NotyfEvent.Dismiss, event);
          });
          // tslint:disable-next-line: no-string-literal
          this.view.on(NotyfEvent.Click, function (_a) {
              var target = _a.target, event = _a.event;
              return target['triggerEvent'](NotyfEvent.Click, event);
          });
      }
      Notyf.prototype.error = function (payload) {
          var options = this.normalizeOptions('error', payload);
          return this.open(options);
      };
      Notyf.prototype.success = function (payload) {
          var options = this.normalizeOptions('success', payload);
          return this.open(options);
      };
      Notyf.prototype.open = function (options) {
          var defaultOpts = this.options.types.find(function (_a) {
              var type = _a.type;
              return type === options.type;
          }) || {};
          var config = __assign(__assign({}, defaultOpts), options);
          this.assignProps(['ripple', 'position', 'dismissible'], config);
          var notification = new NotyfNotification(config);
          this._pushNotification(notification);
          return notification;
      };
      Notyf.prototype.dismissAll = function () {
          while (this.notifications.splice(0, 1))
              ;
      };
      /**
       * Assigns properties to a config object based on two rules:
       * 1. If the config object already sets that prop, leave it as so
       * 2. Otherwise, use the default prop from the global options
       *
       * It's intended to build the final config object to open a notification. e.g. if
       * 'dismissible' is not set, then use the value from the global config.
       *
       * @param props - properties to be assigned to the config object
       * @param config - object whose properties need to be set
       */
      Notyf.prototype.assignProps = function (props, config) {
          var _this = this;
          props.forEach(function (prop) {
              // intentional double equality to check for both null and undefined
              config[prop] = config[prop] == null ? _this.options[prop] : config[prop];
          });
      };
      Notyf.prototype._pushNotification = function (notification) {
          var _this = this;
          this.notifications.push(notification);
          var duration = notification.options.duration !== undefined ? notification.options.duration : this.options.duration;
          if (duration) {
              setTimeout(function () { return _this._removeNotification(notification); }, duration);
          }
      };
      Notyf.prototype._removeNotification = function (notification) {
          var index = this.notifications.indexOf(notification);
          if (index !== -1) {
              this.notifications.splice(index, 1);
          }
      };
      Notyf.prototype.normalizeOptions = function (type, payload) {
          var options = { type: type };
          if (typeof payload === 'string') {
              options.message = payload;
          }
          else if (typeof payload === 'object') {
              options = __assign(__assign({}, options), payload);
          }
          return options;
      };
      Notyf.prototype.registerTypes = function (opts) {
          var incomingTypes = ((opts && opts.types) || []).slice();
          var finalDefaultTypes = DEFAULT_OPTIONS.types.map(function (defaultType) {
              // find if there's a default type within the user input's types, if so, it means the user
              // wants to change some of the default settings
              var userTypeIdx = -1;
              incomingTypes.forEach(function (t, idx) {
                  if (t.type === defaultType.type)
                      userTypeIdx = idx;
              });
              var userType = userTypeIdx !== -1 ? incomingTypes.splice(userTypeIdx, 1)[0] : {};
              return __assign(__assign({}, defaultType), userType);
          });
          return finalDefaultTypes.concat(incomingTypes);
      };
      return Notyf;
  }());

  /**
   * 文章页
   */


  function articles () {
    // 创建 Notyf 实例
    const notyf = new Notyf({
      duration: 2000,
      position: { x: 'center', y: 'top' },
      types: [
        {
          type: 'success',
          background: '#28a745'
        },
        {
          type: 'error',
          background: '#dc3545'
        }
      ]
    });

    likeInit(notyf);
    starInit(notyf);
    sharePosterInit();
    coinInit();
    tocInit();
  }

  /**
   * 喜欢事件绑定
   */
  async function likeInit(notyf){
    var likeButtons = document.querySelectorAll('.widget-action.like');
    if(!likeButtons.length) return;
    
    likeButtons.forEach(button => {
      button.addEventListener('click', async function() {
        console.log('click like button');
        
        var that = this;
        
        const objectId = post_object.post_id;
        if (!objectId) return;
   
        const isLiked = that.classList.contains('active');
        let actionType = isLiked ? 'unlike' : 'like';
        
        try {
          const response = await fetch(ajax_object.ajax_url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
              action: 'like_action',
              object_id: objectId,
              action_type: actionType,
              nonce: ajax_object.nonce,
            })
          });
          
          const result = await response.json();
          console.log('Result: ', result);
          
          if (result.success) {
            // 更新UI
            if (actionType === 'like') {
              that.classList.add('active');
              // 更新按钮文本显示
              const spanElement = that.querySelector('span');
              if (spanElement) {
                spanElement.textContent = i18n.remove_like;
                console.log('i18n.remove_like',i18n.remove_like);
                
              }
            } else {
              that.classList.remove('active');
              // 更新按钮文本显示
              const spanElement = that.querySelector('span');
              if (spanElement) {
                spanElement.textContent = i18n.like;
              }
            }
            
            // 更新喜欢数
            const numberElement = that.querySelector('.number');
            if (numberElement) {
              numberElement.textContent = result.data.like_count || '';
            }
          } else {
            notyf.error(result.data.message || i18n.operation_failed);
          }
        } catch (error) {
          console.error(error);
          notyf.error(i18n.network_error);
        }
      });
    });
  }


  /**
   * 收藏事件绑定
   */
  async function starInit(notyf){
    var starButtons = document.querySelectorAll('.widget-action.star');
    if(!starButtons.length) return;
    
    // 检查用户是否已登录，未登录则弹出登录对话框
    if(!ajax_object.current_user_id || ajax_object.current_user_id == '0'){
      starButtons.forEach(button => {
        button.addEventListener('click', function(e) {
          e.preventDefault();
          // 弹出登录对话框
          const loginModal = document.querySelector('.login-register-dialog');
          if(loginModal){
            loginModal.showModal();
            document.querySelector('body').classList.add('no-scroll');
          }
        });
      });
      return;
    }
    
    starButtons.forEach(button => {
      button.addEventListener('click', async function() {
        var that = this;
        
        const objectId = post_object.post_id;
        if (!objectId) return;
        
        const isStarred = that.classList.contains('active');
        let actionType = isStarred ? 'unstar' : 'star';
        
        try {
          const response = await fetch(ajax_object.ajax_url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
              action: 'star_action',
              object_id: objectId,
              action_type: actionType,
              nonce: ajax_object.nonce
            })
          });
          
          const result = await response.json();
          
          if (result.success) {
            // 更新UI
            if (actionType === 'star') {
              that.classList.add('active');
              // 更新按钮文本显示
              const spanElement = that.querySelector('span');
              if (spanElement) {
                spanElement.textContent = i18n.remove_star;
              }
            } else {
              that.classList.remove('active');
              // 更新按钮文本显示
              const spanElement = that.querySelector('span');
              if (spanElement) {
                spanElement.textContent = i18n.star;
              }
            }
            
            // 更新收藏数
            const numberElement = that.querySelector('.number');
            if (numberElement) {
              numberElement.textContent = result.data.star_count || '';
            }
            
            // notyf.success(result.data.message);
          } else {
            notyf.error(result.data.message || i18n.operation_failed);
            // 如果是未登录错误，显示登录对话框
            if (result.data.message && result.data.message.includes(i18n.please_log_in)) {
              var loginModal = document.querySelector('.login-register-dialog');
              if(loginModal){
                loginModal.showModal();
                document.querySelector('body').classList.add('no-scroll');
              }
            }
          }
        } catch (error) {
          console.error(error);
          notyf.error('网络错误，请重试');
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
      var url = post_object.post_url;
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
   * TOC 目录
   */
  function tocInit() {
    var tocButton = document.querySelector('.toc-button');
    if(!tocButton) return;
    tocButton.addEventListener('click', function(e){
      tocButton.parentNode.parentNode.classList.toggle('show');
    });

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

  var toast = { open };

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
    // console.log('mobileMenuCloseButton', mobileMenuCloseButton);

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
          toast.open({ title: "请输入关键词" });
          return false;
        }
      };
    }

    searchForm.onsubmit = function () {
      if (searchInput.value == "") {
        toast.open({ title: "请输入关键词" });
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
          toast.open({ title: "请输入关键词" });
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


  function avatarShow(){
    var btn = document.querySelector('.header-top-avatar');
    var widget = document.querySelector('.user-widget');
    var timer = null;
    
    if(!btn || !widget) return;
    
    var show = function(){
      clearTimeout(timer);
      widget.classList.add('show');
    };
    
    var hide = function(){
      timer = setTimeout(function(){
        widget.classList.remove('show');
      }, 200);
    };
    
    btn.onmouseenter = show;
    btn.onmouseleave = hide;
    widget.onmouseenter = show;
    widget.onmouseleave = hide;
  }

  function homeInit() {
    articlesGetMore();
  }


  /**
   * 首页文章列表获取更多文章
   */
  function articlesGetMore() {
    var btn = document.querySelector('.articles-more');
    if (!btn) return;

    btn.addEventListener('click', async function(e) {
      var that = this;
      
      if (e.target.dataset.noMore == 'true') {
        return;
      }
      
      var page = Number(e.target.dataset.currentPage) + 1;
      var url = '/wp-json/vtheme/v1/home/get-more-articles?page=' + page;
      
      that.classList.add('loading');
      that.disabled = true;

      var jsonHeaders = new Headers({'Content-Type': 'application/json' });

      try {
        var response = await fetch(url, {
          method: 'GET',
          headers: jsonHeaders
        });

        var responseJson = await response.json();
        if(response.status == '404'){
          that.dataset.noMore = 'true';
          that.querySelector('span').innerText = '已经到底了';
          that.classList.remove('loading');
          return;
        }
        if (response.status == 200) {
          that.previousElementSibling.insertAdjacentHTML("beforeend", responseJson.html_str);
          e.target.dataset.currentPage++;
        } else {
          alert(responseJson.error);
        }
        that.classList.remove('loading');
        that.disabled = false;
      } catch(e) {
        console.log(e.stack);
      }
    });

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

  }


  var home = {
    homeInit
  };

  function init (){
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
    
      axios({
        method: 'post',
        url: document.querySelector('#avatar_upload').getAttribute('action'),
        data: fd,
        headers: {
          'content-type': 'multipart/form-data'
        },
      }).then(function(response) {
        if (response.status == 201) {
          toast.open({title:"头像上传成功"});
          
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

  /**
   * 评论相关功能
   */



  function comments () {
    initCommentDelete();
    initCommentReply();
    initInlineReply(); // 新增：初始化内联回复功能
  }


  function showNotification(message, type) {
    const notyf = new Notyf({
      duration: 3000,
      position: { x: 'center', y: 'top' }
    });

    if (typeof notyf !== 'undefined') {
      if (type === 'success') {
        notyf.success(message);
      } else {
        notyf.error(message);
      }
    } else {
      alert(message);
    }
  }



  /**
   * 初始化评论删除功能
   */
  function initCommentDelete() {
    // 为所有删除链接添加点击事件
    document.querySelectorAll('.delete-comment').forEach(link => {
      bindDeleteButton(link);
    });
  }


  /**
   * 更新评论计数显示
   */
  function updateCommentCount(increment = false) {
    const countElement = document.querySelector('.comments-title');
    if (countElement) {
      const currentCount = parseInt(countElement.textContent.match(/\d+/)?.[0] || 0);
      const newCount = increment ? currentCount + 1 : Math.max(0, currentCount - 1);

      if (newCount === 0) {
        countElement.textContent = i18n.no_comments;
      } else if (newCount === 1) {
        countElement.textContent = i18n.one_comment;
      } else {
        countElement.textContent = newCount + ' ' + i18n.multiple_comments;
      }
    }
  }


  /**
   * 初始化评论回复功能
   */
  function initCommentReply() {
    const form = document.getElementById('commentform');
      
    // 如果没有找到表单，直接退出
    if (!form) return;

    if (typeof ajax_object === 'undefined') {
        console.error('未找到 ajax_object 变量，请检查页面源码。');
        return;
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault(); // 阻止默认提交

        const submitBtn = form.querySelector('input[type="submit"]');
        if (!submitBtn) return;

        // ========== 新增：获取评论内容并验证 ==========
        const commentField = form.querySelector('textarea[name="comment"]');
        const content = commentField ? commentField.value.trim() : '';
        const maxLength = 1000;
        
        // 验证不能为空
        if (!content || content.length === 0) {
            showNotification(i18n.comment_cannot_be_empty, 'error');
            submitBtn.value = originalText;
            submitBtn.disabled = false;
            return;
        }
        
        // 验证最大长度
        if (content.length > maxLength) {
            showNotification(
                i18n.comment_max_length.replace('%d', maxLength),
                'error'
            );
            submitBtn.value = originalText;
            submitBtn.disabled = false;
            return;
        }
        // ============================================

        const originalText = submitBtn.value;
        
        // 锁定按钮
        submitBtn.value = i18n.submitting;
        submitBtn.disabled = true;

        // 构建数据
        const formData = new FormData(form);
        formData.append('action', 'custom_submit_comment');
        formData.append('nonce', ajax_object.nonce);

        // 发送请求
        fetch(ajax_object.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            return response.json().then(data => {
              console.log('Response:', data);
              // 如果 HTTP 状态不是 200-299，或者 data.success 为 false，则视为错误
              if (!response.ok || !data.success) {
                  throw new Error(data.data && data.data.message ? data.data.message : i18n.submit_failed);
              }
              return data;
            });
        })
        .then(data => {
            // 成功逻辑
            if (data.success) {
                showNotification(data.data.message, 'success');
                form.reset(); // 清空表单
                
                // 动态添加新评论到评论列表
                if (data.data.approved === '1') {
                    addNewCommentToList(data.data);
                }
            }
        })
        .catch(error => {
            // 错误逻辑
            // error.message 包含了我们在 PHP 中设置的错误信息
            showNotification(error.message || i18n.network_error_retry, 'error');
            console.error('提交失败:', error, error.message);
        })
        .finally(() => {
            // 恢复按钮
            submitBtn.value = originalText;
            submitBtn.disabled = false;
        });
    });
  }


  /**
   * 动态添加新评论到评论列表
   */
  function addNewCommentToList(commentData, parentId = null) {
    const commentList = document.querySelector('.comment-list');
    if (!commentList) return;

    console.log('commentData, parentId', commentData, parentId);

    // 构建评论 HTML
    const authorName = commentData.author.name;
    const authorUrl = commentData.author.url;
    const avatar = commentData.author.avatar;
    const commentDate = commentData.date;
    const commentContent = commentData.content;
    const commentId = commentData.comment_id;
    const userId = commentData.user_id;
    
    // 构建作者链接 HTML
    let authorHtml = '';
    if (authorUrl) {
      authorHtml = `
      <a href="javascript:;" target="_blank" data-url="${authorUrl}">
        ${authorName}
        <i class="fa-solid fa-globe"></i>
      </a>
    `;
    } else {
      authorHtml = authorName;
    }
    
    // 构建删除链接 HTML（仅当用户登录且是评论作者时显示）
    let deleteLinkHtml = '';
    if (userId > 0 && typeof ajax_object !== 'undefined' && ajax_object.current_user_id == userId) {
      deleteLinkHtml = `
      <a href="javascript:;" class="delete-comment" data-comment-id="${commentId}">
        ${i18n.delete}
      </a>
    `;
    }
    
    // 构建待审核提示 HTML
    let statusHtml = '';
    if (commentData.approved === '0') {
      statusHtml = `
      <div class="comment-status">
        ${i18n.awaiting_moderation}
      </div>
    `;
    }
    
    // 创建评论项 HTML（注意：这里只创建 li 内部的 div，不包含外层 li）
    const commentItemHtml = `
    <div class="comment-item" id="comment-${commentId}">
      <div class="comment-header">
        <div class="comment-author">
          ${avatar}
          <span class="nickname">
            ${authorHtml}
          </span>
        </div>
        <div class="comment-meta">
          ${commentDate}
        </div>
      </div>
      ${statusHtml}
      <div class="comment-content">
        ${commentContent}
      </div>
      <div class="comment-actions">
        <a href="?replytocom=${commentId}#respond" class="reply-link">${i18n.reply}</a>
        ${deleteLinkHtml}
      </div>
    </div>
  `;
    
    // 如果是回复评论，添加到父评论的 .children 容器中
    if (parentId) {
      console.log('回复 parentId');
      const parentComment = document.getElementById(`comment-${parentId}`);
      if (parentComment) {
        // 检查是否有 .children 容器，没有则创建
        let childrenContainer = parentComment.querySelector('.children');
        if (!childrenContainer) {
          childrenContainer = document.createElement('ul');
          childrenContainer.className = 'children';
          // 将 children 容器添加到父评论 li 内部（在 .comment-item 之后）
          parentComment.appendChild(childrenContainer);
        }
        
        // 创建子评论的 li 元素
        const childLi = document.createElement('li');
        childLi.className = 'child';
        childLi.id = `comment-${commentId}`;
        childLi.innerHTML = commentItemHtml;
        
        // 添加到子评论列表
        childrenContainer.appendChild(childLi);
        
        // 滚动到新评论
        const newComment = document.getElementById(`comment-${commentId}`);
        if (newComment) {
          setTimeout(() => {
            newComment.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            newComment.style.opacity = '0';
            newComment.style.transition = 'opacity 0.5s ease-in';
            requestAnimationFrame(() => {
              newComment.style.opacity = '1';
            });
          }, 100);
        }
      }
    } else {
      console.log('回复 顶级评论');

      // 顶级评论，添加到评论列表末尾
      const parentLi = document.createElement('li');
      parentLi.className = 'parent';
      parentLi.id = `comment-${commentId}`;
      parentLi.innerHTML = commentItemHtml;
      
      commentList.insertAdjacentElement('beforeend', parentLi);
      
      // 更新评论计数
      updateCommentCount(true);
      
      // 滚动到新评论位置
      const newComment = document.getElementById(`comment-${commentId}`);
      if (newComment) {
        setTimeout(() => {
          newComment.scrollIntoView({ behavior: 'smooth', block: 'center' });
          // 添加淡入动画效果
          newComment.style.opacity = '0';
          newComment.style.transition = 'opacity 0.5s ease-in';
          requestAnimationFrame(() => {
            newComment.style.opacity = '1';
          });
        }, 100);
      }
    }
    
    // 重新初始化删除功能和内联回复功能（只为新添加的元素绑定事件）
    const newCommentElement = document.getElementById(`comment-${commentId}`);
    if (newCommentElement) {
      // 只处理新增元素的回复按钮
      const replyLink = newCommentElement.querySelector('.comment-actions a.reply-link');
      if (replyLink) {
        bindReplyButton(replyLink);
      }
      
      // 只处理新增元素的删除按钮
      const deleteLink = newCommentElement.querySelector('.delete-comment');
      if (deleteLink) {
        bindDeleteButton(deleteLink);
      }
    }
  }


  /**
   * 初始化内联回复功能 - 阻止跳转并显示内联表单
   */
  function initInlineReply() {
    // 为所有回复链接添加点击事件
    document.querySelectorAll('.comment-actions a.reply-link').forEach(link => {
      bindReplyButton(link);
    });
  }


  /**
   * 创建内联回复表单
   */
  function createInlineReplyForm(commentId, commentItem) {
    const form = document.createElement('div');
    form.className = 'inline-reply-form';
    
    form.innerHTML = `
    <div class="reply-form-container">
      <textarea placeholder="${i18n.reply_to_comment}" rows="3"></textarea>
      <div class="reply-form-actions">
        <button type="submit" class="submit-reply">${i18n.submit_reply}</button>
        <button type="button" class="cancel-reply">${i18n.cancel}</button>
      </div>
    </div>
  `;
    
    // 绑定取消按钮事件
    const cancelBtn = form.querySelector('.cancel-reply');
    cancelBtn.addEventListener('click', () => {
      form.remove();
    });
    
    // 绑定提交事件
    const submitBtn = form.querySelector('.submit-reply');
    submitBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      
      const textarea = form.querySelector('textarea');
      const content = textarea.value.trim();
      const maxLength = 1000;
      
      if (!content) {
        showNotification(i18n.please_enter_reply, 'error');
        return;
      }
      
      // ========== 新增：字数验证 ==========
      // 验证不能为空
      if (content.length === 0) {
        showNotification(i18n.comment_cannot_be_empty, 'error');
        return;
      }
      
      // 验证最大长度
      if (content.length > maxLength) {
        showNotification(
          i18n.comment_max_length.replace('%d', maxLength),
          'error'
        );
        return;
      }
      // ====================================
      
      // 锁定按钮
      const originalText = submitBtn.textContent;
      submitBtn.textContent = i18n.submitting_reply;
      submitBtn.disabled = true;
      
      try {
        // 获取主表单的数据
        const mainForm = document.getElementById('commentform');
        if (!mainForm) {
          showNotification(i18n.comment_form_not_found, 'error');
          return;
        }
        
        // 构建 FormData
        const formData = new FormData(mainForm);
        formData.set('comment', content); // 设置回复内容
        formData.set('action', 'custom_submit_comment');
        formData.append('nonce', ajax_object.nonce);
        
        // 设置 parent 参数
        formData.set('comment_parent', commentId);
        
        // 发送请求
        const response = await fetch(ajax_object.ajax_url, {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          showNotification(result.data.message, 'success');
          form.remove(); // 移除表单
          
          // 如果评论已审核通过，添加到列表
          if (result.data.approved === '1') {
            addNewCommentToList(result.data, commentId);
          }
        } else {
          showNotification(result.data.message || i18n.submit_failed, 'error');
          submitBtn.textContent = originalText;
          submitBtn.disabled = false;
        }
      } catch (error) {
        console.error('Submit reply error:', error);
        showNotification(i18n.network_error_retry, 'error');
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
      }
    });
    
    return form;
  }


  /**
   * 绑定单个回复按钮的事件
   */
  function bindReplyButton(link) {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      
      const url = this.href;
      const match = url.match(/replytocom=(\d+)/);
      if (!match) return;
      
      const commentId = match[1];
      const commentItem = document.getElementById(`comment-${commentId}`);
      
      if (!commentItem) return;
      
      const existingForm = commentItem.querySelector('.inline-reply-form');
      if (existingForm) {
        existingForm.remove();
        return;
      }
      
      document.querySelectorAll('.inline-reply-form').forEach(form => form.remove());
      
      const replyForm = createInlineReplyForm(commentId);
      
      // 查找是否有 .children 容器，如果有则插入到其前面，否则追加到末尾
      const childrenContainer = commentItem.querySelector('.children');
      if (childrenContainer) {
        commentItem.insertBefore(replyForm, childrenContainer);
      } else {
        commentItem.appendChild(replyForm);
      }
      
      const textarea = replyForm.querySelector('textarea');
      if (textarea) {
        textarea.focus();
      }
    });
  }


  /**
   * 绑定单个删除按钮的事件
   */
  function bindDeleteButton(link) {
    link.addEventListener('click', async function (e) {
      e.preventDefault();

      if (!confirm(i18n.confirm_delete_comment)) {
        return;
      }

      const commentId = this.dataset.commentId;
      const commentItem = this.closest('.comment-item');

      const originalText = this.textContent;
      this.textContent = i18n.deleting;
      this.style.pointerEvents = 'none';

      try {
        const response = await fetch(ajax_object.ajax_url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: new URLSearchParams({
            action: 'delete_comment',
            comment_id: commentId,
            nonce: ajax_object.nonce
          })
        });

        const result = await response.json();

        if (result.success) {
          if (commentItem) {
            commentItem.style.transition = 'opacity 0.3s ease';
            commentItem.style.opacity = '0';

            setTimeout(() => {
              commentItem.remove();
              updateCommentCount(false);
              showNotification(i18n.delete_success, 'success');
            }, 300);
          }
        } else {
          showNotification(result.data.message || i18n.delete_failed, 'error');
          this.textContent = originalText;
          this.style.pointerEvents = 'auto';
        }
      } catch (error) {
        console.error('删除评论失败:', error);
        showNotification(i18n.network_error_retry, 'error');
        this.textContent = originalText;
        this.style.pointerEvents = 'auto';
      }
    });
  }

  /**
   * 小工具
   */

  function sideMenuInit(){
  	var btns = document.querySelectorAll('.side-menu .arrow');
  	if(btns.length){
  		btns.forEach(btn=>{
  			btn.addEventListener('click', e => {
  				console.log('e', e);
  				e.preventDefault();
            // if(!e.target.classList.contains('arrow')){ return; }
  				e.target.parentNode.parentNode.classList.toggle('hide-children');
  				return;
  			});
  		});
  	}
  }


  var widget = { sideMenuInit };

  /**
   * JavaScript 项目主文件
   */

  swiper();
  init$1();
  lazyLoad();
  articles();
  darkModeInit();
  headerMobile();
  headerPc();
  home.homeInit();
  init();
  comments();
  widget.sideMenuInit();

  function vt_footer_check() {
    window.addEventListener('load', function(e){
      let themeName = document.querySelector('a[copyright]');
      if(!themeName || themeName.innerText !== "Mirage"){
        document.body.innerHTML = 'keep_theme_footer';
      }
    });
  }
  vt_footer_check(); // 主题底部署名检查

})();
