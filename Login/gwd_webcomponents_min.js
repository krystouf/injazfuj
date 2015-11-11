/**
 * @license
 * Copyright (c) 2014 The Polymer Project Authors. All rights reserved.
 * This code may only be used under the BSD style license found at http://polymer.github.io/LICENSE.txt
 * The complete set of authors may be found at http://polymer.github.io/AUTHORS.txt
 * The complete set of contributors may be found at http://polymer.github.io/CONTRIBUTORS.txt
 * Code distributed by Google as part of the polymer project is also
 * subject to an additional IP rights grant found at http://polymer.github.io/PATENTS.txt
 */
// @version 0.7.12
"undefined"==typeof WeakMap&&!function(){var e=Object.defineProperty,t=Date.now()%1e9,n=function(){this.name="__st"+(1e9*Math.random()>>>0)+(t++ +"__")};n.prototype={set:function(t,n){var o=t[this.name];return o&&o[0]===t?o[1]=n:e(t,this.name,{value:[t,n],writable:!0}),this},get:function(e){var t;return(t=e[this.name])&&t[0]===e?t[1]:void 0},"delete":function(e){var t=e[this.name];return t&&t[0]===e?(t[0]=t[1]=void 0,!0):!1},has:function(e){var t=e[this.name];return t?t[0]===e:!1}},window.WeakMap=n}(),function(e){function t(e){_.push(e),b||(b=!0,w(o))}function n(e){return window.ShadowDOMPolyfill&&window.ShadowDOMPolyfill.wrapIfNeeded(e)||e}function o(){b=!1;var e=_;_=[],e.sort(function(e,t){return e.uid_-t.uid_});var t=!1;e.forEach(function(e){var n=e.takeRecords();r(e),n.length&&(e.callback_(n,e),t=!0)}),t&&o()}function r(e){e.nodes_.forEach(function(t){var n=h.get(t);n&&n.forEach(function(t){t.observer===e&&t.removeTransientObservers()})})}function i(e,t){for(var n=e;n;n=n.parentNode){var o=h.get(n);if(o)for(var r=0;r<o.length;r++){var i=o[r],a=i.options;if(n===e||a.subtree){var d=t(a);d&&i.enqueue(d)}}}}function a(e){this.callback_=e,this.nodes_=[],this.records_=[],this.uid_=++E}function d(e,t){this.type=e,this.target=t,this.addedNodes=[],this.removedNodes=[],this.previousSibling=null,this.nextSibling=null,this.attributeName=null,this.attributeNamespace=null,this.oldValue=null}function s(e){var t=new d(e.type,e.target);return t.addedNodes=e.addedNodes.slice(),t.removedNodes=e.removedNodes.slice(),t.previousSibling=e.previousSibling,t.nextSibling=e.nextSibling,t.attributeName=e.attributeName,t.attributeNamespace=e.attributeNamespace,t.oldValue=e.oldValue,t}function u(e,t){return y=new d(e,t)}function c(e){return N?N:(N=s(y),N.oldValue=e,N)}function f(){y=N=void 0}function l(e){return e===N||e===y}function p(e,t){return e===t?e:N&&l(e)?N:null}function m(e,t,n){this.observer=e,this.target=t,this.options=n,this.transientObservedNodes=[]}var w,h=new WeakMap;if(/Trident|Edge/.test(navigator.userAgent))w=setTimeout;else if(window.setImmediate)w=window.setImmediate;else{var v=[],g=String(Math.random());window.addEventListener("message",function(e){if(e.data===g){var t=v;v=[],t.forEach(function(e){e()})}}),w=function(e){v.push(e),window.postMessage(g,"*")}}var b=!1,_=[],E=0;a.prototype={observe:function(e,t){if(e=n(e),!t.childList&&!t.attributes&&!t.characterData||t.attributeOldValue&&!t.attributes||t.attributeFilter&&t.attributeFilter.length&&!t.attributes||t.characterDataOldValue&&!t.characterData)throw new SyntaxError;var o=h.get(e);o||h.set(e,o=[]);for(var r,i=0;i<o.length;i++)if(o[i].observer===this){r=o[i],r.removeListeners(),r.options=t;break}r||(r=new m(this,e,t),o.push(r),this.nodes_.push(e)),r.addListeners()},disconnect:function(){this.nodes_.forEach(function(e){for(var t=h.get(e),n=0;n<t.length;n++){var o=t[n];if(o.observer===this){o.removeListeners(),t.splice(n,1);break}}},this),this.records_=[]},takeRecords:function(){var e=this.records_;return this.records_=[],e}};var y,N;m.prototype={enqueue:function(e){var n=this.observer.records_,o=n.length;if(n.length>0){var r=n[o-1],i=p(r,e);if(i)return void(n[o-1]=i)}else t(this.observer);n[o]=e},addListeners:function(){this.addListeners_(this.target)},addListeners_:function(e){var t=this.options;t.attributes&&e.addEventListener("DOMAttrModified",this,!0),t.characterData&&e.addEventListener("DOMCharacterDataModified",this,!0),t.childList&&e.addEventListener("DOMNodeInserted",this,!0),(t.childList||t.subtree)&&e.addEventListener("DOMNodeRemoved",this,!0)},removeListeners:function(){this.removeListeners_(this.target)},removeListeners_:function(e){var t=this.options;t.attributes&&e.removeEventListener("DOMAttrModified",this,!0),t.characterData&&e.removeEventListener("DOMCharacterDataModified",this,!0),t.childList&&e.removeEventListener("DOMNodeInserted",this,!0),(t.childList||t.subtree)&&e.removeEventListener("DOMNodeRemoved",this,!0)},addTransientObserver:function(e){if(e!==this.target){this.addListeners_(e),this.transientObservedNodes.push(e);var t=h.get(e);t||h.set(e,t=[]),t.push(this)}},removeTransientObservers:function(){var e=this.transientObservedNodes;this.transientObservedNodes=[],e.forEach(function(e){this.removeListeners_(e);for(var t=h.get(e),n=0;n<t.length;n++)if(t[n]===this){t.splice(n,1);break}},this)},handleEvent:function(e){switch(e.stopImmediatePropagation(),e.type){case"DOMAttrModified":var t=e.attrName,n=e.relatedNode.namespaceURI,o=e.target,r=new u("attributes",o);r.attributeName=t,r.attributeNamespace=n;var a=e.attrChange===MutationEvent.ADDITION?null:e.prevValue;i(o,function(e){return!e.attributes||e.attributeFilter&&e.attributeFilter.length&&-1===e.attributeFilter.indexOf(t)&&-1===e.attributeFilter.indexOf(n)?void 0:e.attributeOldValue?c(a):r});break;case"DOMCharacterDataModified":var o=e.target,r=u("characterData",o),a=e.prevValue;i(o,function(e){return e.characterData?e.characterDataOldValue?c(a):r:void 0});break;case"DOMNodeRemoved":this.addTransientObserver(e.target);case"DOMNodeInserted":var d,s,l=e.target;"DOMNodeInserted"===e.type?(d=[l],s=[]):(d=[],s=[l]);var p=l.previousSibling,m=l.nextSibling,r=u("childList",e.target.parentNode);r.addedNodes=d,r.removedNodes=s,r.previousSibling=p,r.nextSibling=m,i(e.relatedNode,function(e){return e.childList?r:void 0})}f()}},e.JsMutationObserver=a,e.MutationObserver||(e.MutationObserver=a)}(window),window.CustomElements=window.CustomElements||{flags:{}},function(e){var t=e.flags,n=[],o=function(e){n.push(e)},r=function(){n.forEach(function(t){t(e)})};e.addModule=o,e.initializeModules=r,e.hasNative=Boolean(document.registerElement),e.isIE=/Trident/.test(navigator.userAgent),e.useNative=!t.register&&e.hasNative&&!window.ShadowDOMPolyfill&&(!window.HTMLImports||window.HTMLImports.useNative)}(window.CustomElements),window.CustomElements.addModule(function(e){function t(e,t){n(e,function(e){return t(e)?!0:void o(e,t)}),o(e,t)}function n(e,t,o){var r=e.firstElementChild;if(!r)for(r=e.firstChild;r&&r.nodeType!==Node.ELEMENT_NODE;)r=r.nextSibling;for(;r;)t(r,o)!==!0&&n(r,t,o),r=r.nextElementSibling;return null}function o(e,n){for(var o=e.shadowRoot;o;)t(o,n),o=o.olderShadowRoot}function r(e,t){i(e,t,[])}function i(e,t,n){if(e=window.wrap(e),!(n.indexOf(e)>=0)){n.push(e);for(var o,r=e.querySelectorAll("link[rel="+a+"]"),d=0,s=r.length;s>d&&(o=r[d]);d++)o["import"]&&i(o["import"],t,n);t(e)}}var a=window.HTMLImports?window.HTMLImports.IMPORT_LINK_TYPE:"none";e.forDocumentTree=r,e.forSubtree=t}),window.CustomElements.addModule(function(e){function t(e,t){return n(e,t)||o(e,t)}function n(t,n){return e.upgrade(t,n)?!0:void(n&&a(t))}function o(e,t){b(e,function(e){return n(e,t)?!0:void 0})}function r(e){N.push(e),y||(y=!0,setTimeout(i))}function i(){y=!1;for(var e,t=N,n=0,o=t.length;o>n&&(e=t[n]);n++)e();N=[]}function a(e){E?r(function(){d(e)}):d(e)}function d(e){e.__upgraded__&&!e.__attached&&(e.__attached=!0,e.attachedCallback&&e.attachedCallback())}function s(e){u(e),b(e,function(e){u(e)})}function u(e){E?r(function(){c(e)}):c(e)}function c(e){e.__upgraded__&&e.__attached&&(e.__attached=!1,e.detachedCallback&&e.detachedCallback())}function f(e){for(var t=e,n=window.wrap(document);t;){if(t==n)return!0;t=t.parentNode||t.nodeType===Node.DOCUMENT_FRAGMENT_NODE&&t.host}}function l(e){if(e.shadowRoot&&!e.shadowRoot.__watched){g.dom&&console.log("watching shadow-root for: ",e.localName);for(var t=e.shadowRoot;t;)w(t),t=t.olderShadowRoot}}function p(e,n){if(g.dom){var o=n[0];if(o&&"childList"===o.type&&o.addedNodes&&o.addedNodes){for(var r=o.addedNodes[0];r&&r!==document&&!r.host;)r=r.parentNode;var i=r&&(r.URL||r._URL||r.host&&r.host.localName)||"";i=i.split("/?").shift().split("/").pop()}console.group("mutations (%d) [%s]",n.length,i||"")}var a=f(e);n.forEach(function(e){"childList"===e.type&&(M(e.addedNodes,function(e){e.localName&&t(e,a)}),M(e.removedNodes,function(e){e.localName&&s(e)}))}),g.dom&&console.groupEnd()}function m(e){for(e=window.wrap(e),e||(e=window.wrap(document));e.parentNode;)e=e.parentNode;var t=e.__observer;t&&(p(e,t.takeRecords()),i())}function w(e){if(!e.__observer){var t=new MutationObserver(p.bind(this,e));t.observe(e,{childList:!0,subtree:!0}),e.__observer=t}}function h(e){e=window.wrap(e),g.dom&&console.group("upgradeDocument: ",e.baseURI.split("/").pop());var n=e===window.wrap(document);t(e,n),w(e),g.dom&&console.groupEnd()}function v(e){_(e,h)}var g=e.flags,b=e.forSubtree,_=e.forDocumentTree,E=!window.MutationObserver||window.MutationObserver===window.JsMutationObserver;e.hasPolyfillMutations=E;var y=!1,N=[],M=Array.prototype.forEach.call.bind(Array.prototype.forEach),O=Element.prototype.createShadowRoot;O&&(Element.prototype.createShadowRoot=function(){var e=O.call(this);return window.CustomElements.watchShadow(this),e}),e.watchShadow=l,e.upgradeDocumentTree=v,e.upgradeDocument=h,e.upgradeSubtree=o,e.upgradeAll=t,e.attached=a,e.takeRecords=m}),window.CustomElements.addModule(function(e){function t(t,o){if(!t.__upgraded__&&t.nodeType===Node.ELEMENT_NODE){var r=t.getAttribute("is"),i=e.getRegisteredDefinition(t.localName)||e.getRegisteredDefinition(r);if(i&&(r&&i.tag==t.localName||!r&&!i["extends"]))return n(t,i,o)}}function n(t,n,r){return a.upgrade&&console.group("upgrade:",t.localName),n.is&&t.setAttribute("is",n.is),o(t,n),t.__upgraded__=!0,i(t),r&&e.attached(t),e.upgradeSubtree(t,r),a.upgrade&&console.groupEnd(),t}function o(e,t){Object.__proto__?e.__proto__=t.prototype:(r(e,t.prototype,t["native"]),e.__proto__=t.prototype)}function r(e,t,n){for(var o={},r=t;r!==n&&r!==HTMLElement.prototype;){for(var i,a=Object.getOwnPropertyNames(r),d=0;i=a[d];d++)o[i]||(Object.defineProperty(e,i,Object.getOwnPropertyDescriptor(r,i)),o[i]=1);r=Object.getPrototypeOf(r)}}function i(e){e.createdCallback&&e.createdCallback()}var a=e.flags;e.upgrade=t,e.upgradeWithDefinition=n,e.implementPrototype=o}),window.CustomElements.addModule(function(e){function t(t,o){var s=o||{};if(!t)throw new Error("document.registerElement: first argument `name` must not be empty");if(t.indexOf("-")<0)throw new Error("document.registerElement: first argument ('name') must contain a dash ('-'). Argument provided was '"+String(t)+"'.");if(r(t))throw new Error("Failed to execute 'registerElement' on 'Document': Registration failed for type '"+String(t)+"'. The type name is invalid.");if(u(t))throw new Error("DuplicateDefinitionError: a type with name '"+String(t)+"' is already registered");return s.prototype||(s.prototype=Object.create(HTMLElement.prototype)),s.__name=t.toLowerCase(),s.lifecycle=s.lifecycle||{},s.ancestry=i(s["extends"]),a(s),d(s),n(s.prototype),c(s.__name,s),s.ctor=f(s),s.ctor.prototype=s.prototype,s.prototype.constructor=s.ctor,e.ready&&v(document),s.ctor}function n(e){if(!e.setAttribute._polyfilled){var t=e.setAttribute;e.setAttribute=function(e,n){o.call(this,e,n,t)};var n=e.removeAttribute;e.removeAttribute=function(e){o.call(this,e,null,n)},e.setAttribute._polyfilled=!0}}function o(e,t,n){e=e.toLowerCase();var o=this.getAttribute(e);n.apply(this,arguments);var r=this.getAttribute(e);this.attributeChangedCallback&&r!==o&&this.attributeChangedCallback(e,o,r)}function r(e){for(var t=0;t<y.length;t++)if(e===y[t])return!0}function i(e){var t=u(e);return t?i(t["extends"]).concat([t]):[]}function a(e){for(var t,n=e["extends"],o=0;t=e.ancestry[o];o++)n=t.is&&t.tag;e.tag=n||e.__name,n&&(e.is=e.__name)}function d(e){if(!Object.__proto__){var t=HTMLElement.prototype;if(e.is){var n=document.createElement(e.tag);t=Object.getPrototypeOf(n)}for(var o,r=e.prototype,i=!1;r;)r==t&&(i=!0),o=Object.getPrototypeOf(r),o&&(r.__proto__=o),r=o;i||console.warn(e.tag+" prototype not found in prototype chain for "+e.is),e["native"]=t}}function s(e){return b(O(e.tag),e)}function u(e){return e?N[e.toLowerCase()]:void 0}function c(e,t){N[e]=t}function f(e){return function(){return s(e)}}function l(e,t,n){return e===M?p(t,n):D(e,t)}function p(e,t){e&&(e=e.toLowerCase()),t&&(t=t.toLowerCase());var n=u(t||e);if(n){if(e==n.tag&&t==n.is)return new n.ctor;if(!t&&!n.is)return new n.ctor}var o;return t?(o=p(e),o.setAttribute("is",t),o):(o=O(e),e.indexOf("-")>=0&&_(o,HTMLElement),o)}function m(e,t){var n=e[t];e[t]=function(){var e=n.apply(this,arguments);return g(e),e}}var w,h=e.isIE,v=e.upgradeDocumentTree,g=e.upgradeAll,b=e.upgradeWithDefinition,_=e.implementPrototype,E=e.useNative,y=["annotation-xml","color-profile","font-face","font-face-src","font-face-uri","font-face-format","font-face-name","missing-glyph"],N={},M="http://www.w3.org/1999/xhtml",O=document.createElement.bind(document),D=document.createElementNS.bind(document);w=Object.__proto__||E?function(e,t){return e instanceof t}:function(e,t){if(e instanceof t)return!0;for(var n=e;n;){if(n===t.prototype)return!0;n=n.__proto__}return!1},m(Node.prototype,"cloneNode"),m(document,"importNode"),h&&!function(){var e=document.importNode;document.importNode=function(){var t=e.apply(document,arguments);if(t.nodeType==t.DOCUMENT_FRAGMENT_NODE){var n=document.createDocumentFragment();return n.appendChild(t),n}return t}}(),document.registerElement=t,document.createElement=p,document.createElementNS=l,e.registry=N,e["instanceof"]=w,e.reservedTagList=y,e.getRegisteredDefinition=u,document.register=document.registerElement}),function(e){function t(){a(window.wrap(document)),window.CustomElements.ready=!0;var e=window.requestAnimationFrame||function(e){setTimeout(e,16)};e(function(){setTimeout(function(){window.CustomElements.readyTime=Date.now(),window.HTMLImports&&(window.CustomElements.elapsed=window.CustomElements.readyTime-window.HTMLImports.readyTime),document.dispatchEvent(new CustomEvent("WebComponentsReady",{bubbles:!0}))})})}var n=e.useNative,o=e.initializeModules,r=e.isIE;if(n){var i=function(){};e.watchShadow=i,e.upgrade=i,e.upgradeAll=i,e.upgradeDocumentTree=i,e.upgradeSubtree=i,e.takeRecords=i,e["instanceof"]=function(e,t){return e instanceof t}}else o();var a=e.upgradeDocumentTree,d=e.upgradeDocument;if(window.wrap||(window.ShadowDOMPolyfill?(window.wrap=window.ShadowDOMPolyfill.wrapIfNeeded,window.unwrap=window.ShadowDOMPolyfill.unwrapIfNeeded):window.wrap=window.unwrap=function(e){return e}),window.HTMLImports&&(window.HTMLImports.__importsParsingHook=function(e){e["import"]&&d(wrap(e["import"]))}),r&&"function"!=typeof window.CustomEvent&&(window.CustomEvent=function(e,t){t=t||{};var n=document.createEvent("CustomEvent");return n.initCustomEvent(e,Boolean(t.bubbles),Boolean(t.cancelable),t.detail),n.preventDefault=function(){Object.defineProperty(this,"defaultPrevented",{get:function(){return!0}})},n},window.CustomEvent.prototype=window.Event.prototype),"complete"===document.readyState||e.flags.eager)t();else if("interactive"!==document.readyState||window.attachEvent||window.HTMLImports&&!window.HTMLImports.ready){var s=window.HTMLImports&&!window.HTMLImports.ready?"HTMLImportsLoaded":"DOMContentLoaded";window.addEventListener(s,t)}else t()}(window.CustomElements);
"undefined"==typeof window.CustomEvent&&(window.CustomEvent=function(c,a){a=a||{};var b=document.createEvent("CustomEvent");b.initCustomEvent(c,Boolean(a.bubbles),Boolean(a.cancelable),a.detail);return b},window.CustomEvent.prototype=window.Event.prototype);"undefined"==typeof document.register&&(document.register=function(){return document.registerElement.apply(document,arguments)});

