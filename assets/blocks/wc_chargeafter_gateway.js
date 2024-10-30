(()=>{"use strict";var e={20:(e,t,n)=>{var r=n(609),o=Symbol.for("react.element"),a=Symbol.for("react.fragment"),i=Object.prototype.hasOwnProperty,c=r.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,s={key:!0,ref:!0,__self:!0,__source:!0};function u(e,t,n){var r,a={},u=null,l=null;for(r in void 0!==n&&(u=""+n),void 0!==t.key&&(u=""+t.key),void 0!==t.ref&&(l=t.ref),t)i.call(t,r)&&!s.hasOwnProperty(r)&&(a[r]=t[r]);if(e&&e.defaultProps)for(r in t=e.defaultProps)void 0===a[r]&&(a[r]=t[r]);return{$$typeof:o,type:e,key:u,ref:l,props:a,_owner:c.current}}t.Fragment=a,t.jsx=u,t.jsxs=u},848:(e,t,n)=>{e.exports=n(20)},609:e=>{e.exports=window.React},809:(e,t,n)=>{t.Pt=void 0;var r=n(635),o=(n(359),n(336));t.Pt=function(e){var t=e.config,n=r.__rest(e,["config"]);return(0,o.launchChargeafterUi)(r.__assign({config:t,checkout:!0},n))}},280:(e,t)=>{Object.defineProperty(t,"__esModule",{value:!0}),t.initScript=t.checkoutId=void 0,t.checkoutId="chargeafter-checkout-finance-sdk",t.initScript=function(e,n){var r=document.createElement("script");r.id=t.checkoutId,r.onload=n,r.src="".concat(e,"?t=").concat(Date.now()),r.async=!0,document.body.appendChild(r)}},359:(e,t,n)=>{Object.defineProperty(t,"__esModule",{value:!0}),t.initialize=void 0;var r=n(635),o=n(280),a={production:"https://cdn.chargeafter.com/web/v2/chargeafter.min.js",qa:"https://cdn-qa.ca-dev.co/web/v2/chargeafter.min.js",sandbox:"https://cdn-sandbox.ca-dev.co/web/v2/chargeafter.min.js",demo:"https://cdn-demo.ca-dev.co/web/v2/chargeafter.min.js",develop:"https://cdn-develop.ca-dev.co/web/v2/chargeafter.min.js",autoqa:"https://cdn-autoqa.ca-dev.co/web/v2/chargeafter.min.js",uat:"https://cdn-uat.ca-dev.co/web/v2/chargeafter.min.js"};t.initialize=function(e,t){return void 0===t&&(t="production"),r.__awaiter(void 0,void 0,void 0,(function(){var n,i,c,s,u,l;return r.__generator(this,(function(f){return n=a[t],window.document.getElementById(o.checkoutId)&&window.ChargeAfter?(null===(l=e.onLoaded)||void 0===l||l.call(e),[2,window.ChargeAfter]):(window.caConfig=e,window.ChargeAfter=r.__assign(r.__assign({},window.ChargeAfter||{}),{cfg:e}),s=new Promise((function(e,t){i=e,c=t})),u=function(){return r.__awaiter(void 0,void 0,void 0,(function(){var t;return r.__generator(this,(function(n){switch(n.label){case 0:return window.ChargeAfter?[4,null===(t=window.ChargeAfter)||void 0===t?void 0:t.init(e)]:[3,2];case 1:return n.sent(),i(window.ChargeAfter),[3,3];case 2:c(new Error("ChargeAfter not initialized")),n.label=3;case 3:return[2]}}))}))},(0,o.initScript)(n,u),[2,s])}))}))}},336:(e,t,n)=>{Object.defineProperty(t,"__esModule",{value:!0}),t.launchChargeafterUi=void 0;var r=n(635),o=n(359);t.launchChargeafterUi=function(e){var t=e.config,n=e.onConfirm,a=e.onApprovalStatusChange,i=e.checkout,c=e.applicationId,s=e.cartDetails,u=e.onDataUpdate,l=e.onModalOpen,f=r.__rest(e,["config","onConfirm","onApprovalStatusChange","checkout","applicationId","cartDetails","onDataUpdate","onModalOpen"]);return new Promise((function(e,d){var p=r.__assign({channel:t.channel,preferences:t.preferences,onConfirm:n,onApprovalStatusChange:a,browserSessionId:t.env.browserSessionId},f),y=r.__assign(r.__assign({},p),{cartDetails:s,applicationId:c,onDataUpdate:u?function(e,t){var n=u(e);n?n.then((function(e){return t(e)})):t({})}:void 0,callback:function(t,n,o){o?(console.info("[ChargeAfterSDK]payments status: ".concat(JSON.stringify(o))),d(r.__assign(r.__assign({},o),{data:n}))):e(r.__assign({token:t},n))}}),_=r.__assign(r.__assign({},p),{callback:function(t,n){n?(console.info("[ChargeAfterSDK]payments status: ".concat(JSON.stringify(n))),d(r.__assign(r.__assign({},n),{data:t}))):e(t)}}),h={apiKey:t.env.apiKey,delegatedMerchantId:t.env.delegatedMerchantId,storeId:t.storeId,onLoaded:l};(0,o.initialize)(h,t.env.name).then((function(e){var t=i?"checkout":"apply";console.log("Calling SDK for ".concat(t).concat(i?", resume token: '".concat(y.applicationId):"","'")),i?null==e||e.payments.present("checkout",y):null==e||e.payments.present("apply",_)}))}))}},635:(e,t,n)=>{n.r(t),n.d(t,{__addDisposableResource:()=>D,__assign:()=>a,__asyncDelegator:()=>P,__asyncGenerator:()=>S,__asyncValues:()=>E,__await:()=>j,__awaiter:()=>y,__classPrivateFieldGet:()=>T,__classPrivateFieldIn:()=>A,__classPrivateFieldSet:()=>R,__createBinding:()=>h,__decorate:()=>c,__disposeResources:()=>N,__esDecorate:()=>u,__exportStar:()=>w,__extends:()=>o,__generator:()=>_,__importDefault:()=>k,__importStar:()=>I,__makeTemplateObject:()=>x,__metadata:()=>p,__param:()=>s,__propKey:()=>f,__read:()=>g,__rest:()=>i,__runInitializers:()=>l,__setFunctionName:()=>d,__spread:()=>m,__spreadArray:()=>O,__spreadArrays:()=>b,__values:()=>v,default:()=>F});var r=function(e,t){return r=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(e,t){e.__proto__=t}||function(e,t){for(var n in t)Object.prototype.hasOwnProperty.call(t,n)&&(e[n]=t[n])},r(e,t)};function o(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Class extends value "+String(t)+" is not a constructor or null");function __(){this.constructor=e}r(e,t),e.prototype=null===t?Object.create(t):(__.prototype=t.prototype,new __)}var a=function(){return a=Object.assign||function(e){for(var t,n=1,r=arguments.length;n<r;n++)for(var o in t=arguments[n])Object.prototype.hasOwnProperty.call(t,o)&&(e[o]=t[o]);return e},a.apply(this,arguments)};function i(e,t){var n={};for(var r in e)Object.prototype.hasOwnProperty.call(e,r)&&t.indexOf(r)<0&&(n[r]=e[r]);if(null!=e&&"function"==typeof Object.getOwnPropertySymbols){var o=0;for(r=Object.getOwnPropertySymbols(e);o<r.length;o++)t.indexOf(r[o])<0&&Object.prototype.propertyIsEnumerable.call(e,r[o])&&(n[r[o]]=e[r[o]])}return n}function c(e,t,n,r){var o,a=arguments.length,i=a<3?t:null===r?r=Object.getOwnPropertyDescriptor(t,n):r;if("object"==typeof Reflect&&"function"==typeof Reflect.decorate)i=Reflect.decorate(e,t,n,r);else for(var c=e.length-1;c>=0;c--)(o=e[c])&&(i=(a<3?o(i):a>3?o(t,n,i):o(t,n))||i);return a>3&&i&&Object.defineProperty(t,n,i),i}function s(e,t){return function(n,r){t(n,r,e)}}function u(e,t,n,r,o,a){function i(e){if(void 0!==e&&"function"!=typeof e)throw new TypeError("Function expected");return e}for(var c,s=r.kind,u="getter"===s?"get":"setter"===s?"set":"value",l=!t&&e?r.static?e:e.prototype:null,f=t||(l?Object.getOwnPropertyDescriptor(l,r.name):{}),d=!1,p=n.length-1;p>=0;p--){var y={};for(var _ in r)y[_]="access"===_?{}:r[_];for(var _ in r.access)y.access[_]=r.access[_];y.addInitializer=function(e){if(d)throw new TypeError("Cannot add initializers after decoration has completed");a.push(i(e||null))};var h=(0,n[p])("accessor"===s?{get:f.get,set:f.set}:f[u],y);if("accessor"===s){if(void 0===h)continue;if(null===h||"object"!=typeof h)throw new TypeError("Object expected");(c=i(h.get))&&(f.get=c),(c=i(h.set))&&(f.set=c),(c=i(h.init))&&o.unshift(c)}else(c=i(h))&&("field"===s?o.unshift(c):f[u]=c)}l&&Object.defineProperty(l,r.name,f),d=!0}function l(e,t,n){for(var r=arguments.length>2,o=0;o<t.length;o++)n=r?t[o].call(e,n):t[o].call(e);return r?n:void 0}function f(e){return"symbol"==typeof e?e:"".concat(e)}function d(e,t,n){return"symbol"==typeof t&&(t=t.description?"[".concat(t.description,"]"):""),Object.defineProperty(e,"name",{configurable:!0,value:n?"".concat(n," ",t):t})}function p(e,t){if("object"==typeof Reflect&&"function"==typeof Reflect.metadata)return Reflect.metadata(e,t)}function y(e,t,n,r){return new(n||(n=Promise))((function(o,a){function i(e){try{s(r.next(e))}catch(e){a(e)}}function c(e){try{s(r.throw(e))}catch(e){a(e)}}function s(e){var t;e.done?o(e.value):(t=e.value,t instanceof n?t:new n((function(e){e(t)}))).then(i,c)}s((r=r.apply(e,t||[])).next())}))}function _(e,t){var n,r,o,a,i={label:0,sent:function(){if(1&o[0])throw o[1];return o[1]},trys:[],ops:[]};return a={next:c(0),throw:c(1),return:c(2)},"function"==typeof Symbol&&(a[Symbol.iterator]=function(){return this}),a;function c(c){return function(s){return function(c){if(n)throw new TypeError("Generator is already executing.");for(;a&&(a=0,c[0]&&(i=0)),i;)try{if(n=1,r&&(o=2&c[0]?r.return:c[0]?r.throw||((o=r.return)&&o.call(r),0):r.next)&&!(o=o.call(r,c[1])).done)return o;switch(r=0,o&&(c=[2&c[0],o.value]),c[0]){case 0:case 1:o=c;break;case 4:return i.label++,{value:c[1],done:!1};case 5:i.label++,r=c[1],c=[0];continue;case 7:c=i.ops.pop(),i.trys.pop();continue;default:if(!((o=(o=i.trys).length>0&&o[o.length-1])||6!==c[0]&&2!==c[0])){i=0;continue}if(3===c[0]&&(!o||c[1]>o[0]&&c[1]<o[3])){i.label=c[1];break}if(6===c[0]&&i.label<o[1]){i.label=o[1],o=c;break}if(o&&i.label<o[2]){i.label=o[2],i.ops.push(c);break}o[2]&&i.ops.pop(),i.trys.pop();continue}c=t.call(e,i)}catch(e){c=[6,e],r=0}finally{n=o=0}if(5&c[0])throw c[1];return{value:c[0]?c[1]:void 0,done:!0}}([c,s])}}}var h=Object.create?function(e,t,n,r){void 0===r&&(r=n);var o=Object.getOwnPropertyDescriptor(t,n);o&&!("get"in o?!t.__esModule:o.writable||o.configurable)||(o={enumerable:!0,get:function(){return t[n]}}),Object.defineProperty(e,r,o)}:function(e,t,n,r){void 0===r&&(r=n),e[r]=t[n]};function w(e,t){for(var n in e)"default"===n||Object.prototype.hasOwnProperty.call(t,n)||h(t,e,n)}function v(e){var t="function"==typeof Symbol&&Symbol.iterator,n=t&&e[t],r=0;if(n)return n.call(e);if(e&&"number"==typeof e.length)return{next:function(){return e&&r>=e.length&&(e=void 0),{value:e&&e[r++],done:!e}}};throw new TypeError(t?"Object is not iterable.":"Symbol.iterator is not defined.")}function g(e,t){var n="function"==typeof Symbol&&e[Symbol.iterator];if(!n)return e;var r,o,a=n.call(e),i=[];try{for(;(void 0===t||t-- >0)&&!(r=a.next()).done;)i.push(r.value)}catch(e){o={error:e}}finally{try{r&&!r.done&&(n=a.return)&&n.call(a)}finally{if(o)throw o.error}}return i}function m(){for(var e=[],t=0;t<arguments.length;t++)e=e.concat(g(arguments[t]));return e}function b(){for(var e=0,t=0,n=arguments.length;t<n;t++)e+=arguments[t].length;var r=Array(e),o=0;for(t=0;t<n;t++)for(var a=arguments[t],i=0,c=a.length;i<c;i++,o++)r[o]=a[i];return r}function O(e,t,n){if(n||2===arguments.length)for(var r,o=0,a=t.length;o<a;o++)!r&&o in t||(r||(r=Array.prototype.slice.call(t,0,o)),r[o]=t[o]);return e.concat(r||Array.prototype.slice.call(t))}function j(e){return this instanceof j?(this.v=e,this):new j(e)}function S(e,t,n){if(!Symbol.asyncIterator)throw new TypeError("Symbol.asyncIterator is not defined.");var r,o=n.apply(e,t||[]),a=[];return r={},i("next"),i("throw"),i("return"),r[Symbol.asyncIterator]=function(){return this},r;function i(e){o[e]&&(r[e]=function(t){return new Promise((function(n,r){a.push([e,t,n,r])>1||c(e,t)}))})}function c(e,t){try{(n=o[e](t)).value instanceof j?Promise.resolve(n.value.v).then(s,u):l(a[0][2],n)}catch(e){l(a[0][3],e)}var n}function s(e){c("next",e)}function u(e){c("throw",e)}function l(e,t){e(t),a.shift(),a.length&&c(a[0][0],a[0][1])}}function P(e){var t,n;return t={},r("next"),r("throw",(function(e){throw e})),r("return"),t[Symbol.iterator]=function(){return this},t;function r(r,o){t[r]=e[r]?function(t){return(n=!n)?{value:j(e[r](t)),done:!1}:o?o(t):t}:o}}function E(e){if(!Symbol.asyncIterator)throw new TypeError("Symbol.asyncIterator is not defined.");var t,n=e[Symbol.asyncIterator];return n?n.call(e):(e=v(e),t={},r("next"),r("throw"),r("return"),t[Symbol.asyncIterator]=function(){return this},t);function r(n){t[n]=e[n]&&function(t){return new Promise((function(r,o){!function(e,t,n,r){Promise.resolve(r).then((function(t){e({value:t,done:n})}),t)}(r,o,(t=e[n](t)).done,t.value)}))}}}function x(e,t){return Object.defineProperty?Object.defineProperty(e,"raw",{value:t}):e.raw=t,e}var C=Object.create?function(e,t){Object.defineProperty(e,"default",{enumerable:!0,value:t})}:function(e,t){e.default=t};function I(e){if(e&&e.__esModule)return e;var t={};if(null!=e)for(var n in e)"default"!==n&&Object.prototype.hasOwnProperty.call(e,n)&&h(t,e,n);return C(t,e),t}function k(e){return e&&e.__esModule?e:{default:e}}function T(e,t,n,r){if("a"===n&&!r)throw new TypeError("Private accessor was defined without a getter");if("function"==typeof t?e!==t||!r:!t.has(e))throw new TypeError("Cannot read private member from an object whose class did not declare it");return"m"===n?r:"a"===n?r.call(e):r?r.value:t.get(e)}function R(e,t,n,r,o){if("m"===r)throw new TypeError("Private method is not writable");if("a"===r&&!o)throw new TypeError("Private accessor was defined without a setter");if("function"==typeof t?e!==t||!o:!t.has(e))throw new TypeError("Cannot write private member to an object whose class did not declare it");return"a"===r?o.call(e,n):o?o.value=n:t.set(e,n),n}function A(e,t){if(null===t||"object"!=typeof t&&"function"!=typeof t)throw new TypeError("Cannot use 'in' operator on non-object");return"function"==typeof e?t===e:e.has(t)}function D(e,t,n){if(null!=t){if("object"!=typeof t&&"function"!=typeof t)throw new TypeError("Object expected.");var r;if(n){if(!Symbol.asyncDispose)throw new TypeError("Symbol.asyncDispose is not defined.");r=t[Symbol.asyncDispose]}if(void 0===r){if(!Symbol.dispose)throw new TypeError("Symbol.dispose is not defined.");r=t[Symbol.dispose]}if("function"!=typeof r)throw new TypeError("Object not disposable.");e.stack.push({value:t,dispose:r,async:n})}else n&&e.stack.push({async:!0});return t}var M="function"==typeof SuppressedError?SuppressedError:function(e,t,n){var r=new Error(n);return r.name="SuppressedError",r.error=e,r.suppressed=t,r};function N(e){function t(t){e.error=e.hasError?new M(t,e.error,"An error was suppressed during disposal."):t,e.hasError=!0}return function n(){for(;e.stack.length;){var r=e.stack.pop();try{var o=r.dispose&&r.dispose.call(r.value);if(r.async)return Promise.resolve(o).then(n,(function(e){return t(e),n()}))}catch(e){t(e)}}if(e.hasError)throw e.error}()}const F={__extends:o,__assign:a,__rest:i,__decorate:c,__param:s,__metadata:p,__awaiter:y,__generator:_,__createBinding:h,__exportStar:w,__values:v,__read:g,__spread:m,__spreadArrays:b,__spreadArray:O,__await:j,__asyncGenerator:S,__asyncDelegator:P,__asyncValues:E,__makeTemplateObject:x,__importStar:I,__importDefault:k,__classPrivateFieldGet:T,__classPrivateFieldSet:R,__classPrivateFieldIn:A,__addDisposableResource:D,__disposeResources:N}}},t={};function n(r){var o=t[r];if(void 0!==o)return o.exports;var a=t[r]={exports:{}};return e[r](a,a.exports,n),a.exports}n.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return n.d(t,{a:t}),t},n.d=(e,t)=>{for(var r in t)n.o(t,r)&&!n.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},n.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),n.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},(()=>{var e=n(848);const t=window.wc.wcBlocksRegistry,r=window.wp.i18n;const o=t=>{var{RenderedComponent:n}=t,r=function(e,t){var n={};for(var r in e)Object.prototype.hasOwnProperty.call(e,r)&&t.indexOf(r)<0&&(n[r]=e[r]);if(null!=e&&"function"==typeof Object.getOwnPropertySymbols){var o=0;for(r=Object.getOwnPropertySymbols(e);o<r.length;o++)t.indexOf(r[o])<0&&Object.prototype.propertyIsEnumerable.call(e,r[o])&&(n[r[o]]=e[r[o]])}return n}(t,["RenderedComponent"]);return(0,e.jsx)(n,Object.assign({},r))},a=window.wp.hooks,i=window.wp.data,c=window.wp.element,s=window.wc.wcBlocksData;function u(){const{isIdle:e,isCalculating:t,isBeforeProcessing:n,isProcessing:r,isAfterProcessing:o,isComplete:u,hasError:l}=(0,i.useSelect)((e=>{const t=e(s.CHECKOUT_STORE_KEY);return{isIdle:t.isIdle(),isCalculating:t.isCalculating(),isBeforeProcessing:t.isBeforeProcessing(),isProcessing:t.isProcessing(),isAfterProcessing:t.isAfterProcessing(),isComplete:t.isComplete(),hasError:t.hasError()}}),[]);return(0,c.useEffect)((()=>{(0,a.doAction)("ca_update_checkout_payment_method")}),[e,t,n,r,o,u,l]),null}function l(t){return(0,e.jsxs)("span",{className:"wc-chargeafter-components-payment-method",children:[(0,e.jsx)("span",{className:"wc-chargeafter-components-payment-method-label",children:t.label}),(0,e.jsx)("span",{className:"wc-chargeafter-components-payment-method-icon",children:(0,e.jsx)("span",{className:"ca-checkout-button","data-button-type":t.buttonType})})]})}const f=window.wp.htmlEntities,d=window.wc.wcSettings,p=()=>(0,d.getSetting)("wc_chargeafter_gateway_data",{}),y=(e,t,n)=>{const r=t||new FormData;for(let t in e){if(!e.hasOwnProperty(t)||!e[t])continue;const o=n?`${n}[${t}]`:t;e[t]instanceof Date?r.append(o,e[t].toISOString()):"object"!=typeof e[t]||e[t]instanceof File?r.append(o,e[t]):y(e[t],r,o)}return r},_=window.wp.apiFetch;var h=n.n(_);const w=window.wp.url,v=(e,t,n)=>{if(t&&"object"==typeof t)Object.keys(t).forEach((r=>{v(e,t[r],n?`${n}[${r}]`:r)}));else{const r=null===t?"":t;n&&e.append(n,r)}},g=e=>{const t=new FormData;return v(t,e),t};var m=n(809),b=function(e,t,n,r){return new(n||(n=Promise))((function(o,a){function i(e){try{s(r.next(e))}catch(e){a(e)}}function c(e){try{s(r.throw(e))}catch(e){a(e)}}function s(e){var t;e.done?o(e.value):(t=e.value,t instanceof n?t:new n((function(e){e(t)}))).then(i,c)}s((r=r.apply(e,t||[])).next())}))};const O=({eventRegistration:e,emitResponse:t})=>{const{description:n}=p();return((e,t)=>{((e,t)=>{const{onCheckoutSuccess:n}=e,{responseTypes:o,noticeContexts:a}=t;(0,c.useEffect)((()=>{const{publicKey:e,environment:t,withDataUpdate:i}=p(),c=n((n=>b(void 0,void 0,void 0,(function*(){const{orderId:c,processingResponse:{paymentDetails:s}}=n,{confirmation_endpoint:u,checkout_data_endpoint:l,data_update_endpoint:f,order_nonce:d}=s;if(c&&d&&u&&l){const n=yield(({path:e,orderId:t,orderNonce:n})=>h()({path:(0,w.addQueryArgs)(e,{orderId:t,nonce:n})}))({path:l,orderId:c,orderNonce:d}),s={config:{env:{name:t,apiKey:e}},consumerDetails:{},cartDetails:{items:[],taxAmount:0,shippingAmount:0,totalAmount:0},callback:()=>{}};i&&(s.onDataUpdate=e=>{(({path:e,orderNonce:t,orderId:n,data:r})=>h()({path:(0,w.addQueryArgs)(e,{nonce:t,orderId:n}),method:"POST",body:g(r)}))({path:f,orderId:c,orderNonce:d,data:e}).then((()=>{}))});try{const e=yield(0,m.Pt)(Object.assign(Object.assign({},s),n)),{token:t}=e;if(t){const n=new Promise(((n,r)=>{const o=setTimeout((()=>b(void 0,void 0,void 0,(function*(){clearTimeout(o);try{const{redirect:r}=yield(({path:e,orderId:t,orderNonce:n,confirmationToken:r,data:o})=>{const a=new FormData;return a.append("confirmationToken",r),o&&y(o,a,"data"),h()({path:(0,w.addQueryArgs)(e,{orderId:t,nonce:n}),method:"POST",body:a})})({path:u,orderId:c,orderNonce:d,confirmationToken:t,data:e});n(r)}catch(e){r(new Error("Redirect URL wasn't provided."))}}))),500)})),r=yield n;return{type:o.SUCCESS,redirectUrl:r}}}catch(e){let{code:t,message:n}=e;return t||(n=(0,r.__)("Unable to processing payment. Please try again.","chargeafter-extension")),{type:o.ERROR,messageContext:a.PAYMENTS,message:n}}}return{type:o.ERROR}}))));return()=>{c()}}),[n,o.SUCCESS,o.ERROR,a.PAYMENTS])})(e,t)})(e,t),(0,f.decodeEntities)(null!=n?n:"")},j="wc_chargeafter_gateway",S=(0,r.__)("Consumer Financing","chargeafter-extension"),{title:P,buttonType:E,supports:x}=p(),C=(0,f.decodeEntities)(P)||S,I=(0,f.decodeEntities)(E)||"default",k={features:null!=x?x:[]};(0,t.registerPaymentMethod)({name:j,label:(0,e.jsx)((t=>(0,e.jsxs)(e.Fragment,{children:[(0,e.jsx)(l,Object.assign({},t)),(0,e.jsx)(u,{})]})),{label:C,buttonType:I}),content:(0,e.jsx)(o,{RenderedComponent:O}),edit:(0,e.jsx)(o,{RenderedComponent:O}),canMakePayment:()=>!0,ariaLabel:C,placeOrderButtonLabel:(0,r.__)("Proceed to Checkout","chargeafter-extension"),paymentMethodId:j,supports:k})})()})();