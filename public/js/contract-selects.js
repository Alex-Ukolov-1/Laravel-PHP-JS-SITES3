!function(e){var t={};function n(s){if(t[s])return t[s].exports;var c=t[s]={i:s,l:!1,exports:{}};return e[s].call(c.exports,c,c.exports,n),c.l=!0,c.exports}n.m=e,n.c=t,n.d=function(e,t,s){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:s})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var s=Object.create(null);if(n.r(s),Object.defineProperty(s,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var c in e)n.d(s,c,function(t){return e[t]}.bind(null,c));return s},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="/",n(n.s=2)}({2:function(e,t,n){e.exports=n("VbGR")},VbGR:function(e,t){$(document).ready((function(){$(".cargo-type-select").select2({ajax:{url:"/cargo_types/json",dataType:"json",delay:250,processResults:function(e){return{results:e}}},escapeMarkup:function(e){return e}}),$(".cargo-type-select").on("select2:open",(function(e){$(".btn-cargo-add").length||($(".select2-search__field").css("width","calc(100% - 35px)"),$(".select2-search--dropdown").append("<button onclick=\"Modal.showModal('.cargo-type-modal', function() { $('.cargo-type-select').select2('close'); }, function() { $('.cargo-type-select').select2('open'); })\" type=\"button\" class=\"btn btn-sm btn-success btn-cargo-add\" title=\"Добавить тип груза\"><i class=\"fa fa-plus\"></i></button>"))})),$(".cargo-type-select").on("select2:select",(function(e){var t=e.params.data;$('input[name="conversion_factor"]').val(t.ratio_ton_cubic)})),$(".departure_point").select2({ajax:{url:"/departure_points/json",dataType:"json",delay:250,processResults:function(e){return{results:e}}},escapeMarkup:function(e){return e}}),$(".departure_point").on("select2:open",(function(e){$(".btn-departure_point-add").length||($(".select2-search__field").css("width","calc(100% - 35px)"),$(".select2-search--dropdown").append("<button onclick=\"Modal.showModal('.departure-point-modal', function() { $('.departure_point').select2('close'); }, function() { $('.departure_point').select2('open'); })\" type=\"button\" class=\"btn btn-sm btn-success btn-departure_point-add\" title=\"Добавить пункт погрузки\"><i class=\"fa fa-plus\"></i></button>"))})),$(".supplier-select").select2({ajax:{url:"/suppliers/json",dataType:"json",delay:250,processResults:function(e){return{results:e}}},escapeMarkup:function(e){return e}}),$(".supplier-select").on("select2:open",(function(e){$(".btn-supplier-add").length||($(".select2-search__field").css("width","calc(100% - 35px)"),$(".select2-search--dropdown").append("<button onclick=\"Modal.showModal('.supplier-modal', function() { $('.supplier-select').select2('close'); }, function() { $('.supplier-select').select2('open'); })\" type=\"button\" class=\"btn btn-sm btn-success btn-supplier-add\" title=\"Добавить поставщика\"><i class=\"fa fa-plus\"></i></button>"))})),$(".destination-select").select2({ajax:{url:"/destinations/json",dataType:"json",delay:250,processResults:function(e){return{results:e}}},escapeMarkup:function(e){return e}}),$(".destination-select").on("select2:open",(function(e){$(".btn-destination-add").length||($(".select2-search__field").css("width","calc(100% - 35px)"),$(".select2-search--dropdown").append("<button onclick=\"Modal.showModal('.destination-modal', function() { $('.destination-select').select2('close'); }, function() { $('.destination-select').select2('open'); })\" type=\"button\" class=\"btn btn-sm btn-success btn-destination-add\" title=\"Добавить пункт разгрузки\"><i class=\"fa fa-plus\"></i></button>"))})),$(".customer-select").select2({ajax:{url:"/customers/json",dataType:"json",delay:250,processResults:function(e){return{results:e}}},escapeMarkup:function(e){return e}}),$(".customer-select").on("select2:open",(function(e){$(".btn-customer-add").length||($(".select2-search__field").css("width","calc(100% - 35px)"),$(".select2-search--dropdown").append("<button onclick=\"Modal.showModal('.customer-modal', function() { $('.customer-select').select2('close'); }, function() { $('.customer-select').select2('open'); })\" type=\"button\" class=\"btn btn-sm btn-success btn-customer-add\" title=\"Добавить заказчика\"><i class=\"fa fa-plus\"></i></button>"))})),$(".contractor-select").select2({ajax:{url:"/contractors/json",dataType:"json",delay:250,processResults:function(e){return{results:e}}},escapeMarkup:function(e){return e}}),$(".contractor-select").on("select2:open",(function(e){$(".btn-contractor-add").length||($(".select2-search__field").css("width","calc(100% - 35px)"),$(".select2-search--dropdown").append("<button onclick=\"Modal.showModal('.contractor-modal', function() { $('.contractor-select').select2('close'); }, function() { $('.contractor-select').select2('open'); })\" type=\"button\" class=\"btn btn-sm btn-success btn-contractor-add\" title=\"Добавить исполнителя\"><i class=\"fa fa-plus\"></i></button>"))}))}))}});