/*
	todo autoComplete
*/
(function($) {

	"use strict";

	var defaults = {
		method: 'GET',
		icon: 'arrow-r',
		cancelRequests: false,
		target: $(),
    delay: 100,
		source: null,
    onPush: null,
		link: null,
		minLength: 0,
		transition: 'fade',
		matchFromStart: true,
    cache: {},
    getRequestData: function($target){
      return {
        'term': $target.val()
      };
    },
    injectChoice: function(index, value){
      return $('<li data-icon=' + this.icon + '><a href="' + this.link + encodeURIComponent(value) + '" data-transition="' + this.transition + '">' + value + '</a></li>');
    }
	},
	openXHR = {},
	buildItems = function($this, data, settings) {
		var str = [];
		if (data) {
			$.each(data, function(index, value) {
				// are we working with objects or strings?
        str.push(settings.injectChoice(index, value).attr("data-autocomplete", JSON.stringify(value)));
			});
		}
    $(settings.target).empty();
    for(var key in str){
      $(settings.target).append(str[key]);
    }
    try {
     $(settings.target).listview("refresh");
    } catch (e){

    }
		// is there a callback?
		if (settings.onPush !== null && $.isFunction(settings.onPush)) {
			attachCallback(settings);
		}

		if (str.length > 0) {
			$this.trigger("targetUpdated.autocomplete");
		} else {
			$this.trigger("targetCleared.autocomplete");
		}
	},
	attachCallback = function(settings) {
		$('li', $(settings.target)).bind('vclick.autocomplete',function(e){
			e.stopPropagation();
			e.preventDefault();
			settings.onPush(e, $.parseJSON($(this).attr('data-autocomplete')));
		});
	},
	clearTarget = function($this, $target) {

		$target.html('');
    try {
      $target.listview('refresh');
    } catch (e){

    }
    $target.closest("fieldset").removeClass("ui-search-active");
		$this.trigger("targetCleared.autocomplete");
	},
	handleInput = function(e) {

		var $this = $(this),
			id = $this.attr("id"),
			text,
			data,
			settings = $this.jqmData("autocomplete"),
			element_text,
			re;
    clearTimeout(window.acdelayto);
    window.acdelayto = window.setTimeout(function(){
      window.acdelayallow = true;
    }, settings.delay);
    if(!window.acdelayallow){
      window.acdelayallow = false;
      return;
    }
    window.acdelayallow = false;
		if (settings) {
			// get the current text of the input field
			text = $this.val();
			// if we don't have enough text zero out the target
			if (text.length < settings.minLength) {
				clearTarget($this, $(settings.target));
			} else {
				// are we looking at a source array or remote data?
				if ($.isArray(settings.source)) {
					data = settings.source.sort().filter(function(element) {
						// matching from start, or anywhere in the string?
						if (settings.matchFromStart) {
							// from start
              re = new RegExp('^' + text, 'i');
						} else {
							// anywhere
              re = new RegExp(text, 'i');
						}
						if ($.isPlainObject(element)) {
							element_text = element.label;
						} else {
							element_text = element;
						}
						return re.test(element_text);
					});
					buildItems($this, data, settings);
				}
				// Accept a function as source.
				// Function needs to call the callback, which is the first parameter.
				// source:function(text,callback) { mydata = [1,2]; callback(mydata); }
				else if (typeof settings.source === 'function') {
					settings.source(text,function(data){
						buildItems($this, data, settings);
					});
				} else {
          var cachedData = settings.cache[$this.val()?$this.val():0 ];
          if(cachedData){
            buildItems($this, cachedData, settings);
          } else
            $.ajax({
              type: settings.method,
              url: settings.source,
              data: settings.getRequestData($this)/*{ term: text }*/,
              beforeSend: function(jqXHR) {
                if (settings.cancelRequests) {
                  if (openXHR[id]) {
                    // If we have an open XML HTTP Request for this autoComplete ID, abort it
                    openXHR[id].abort();
                  } else {
                    // Set a loading indicator as a temporary stop-gap to the response time issue
//                    settings.target.html('<li data-icon="none"><a href="#">Searching...</a></li>');
                    $.mobile.showPageLoadingMsg();
                    try {
                      settings.target.listview('refresh');
                    } catch (e){

                    }
                    settings.target.closest("fieldset").addClass("ui-search-active");
                  }
                  // Set this request to the open XML HTTP Request list for this ID
                  openXHR[id] = jqXHR;
                }
              },
              success: function(data) {
                $.mobile.hidePageLoadingMsg();
                settings.cache[$this.val()?$this.val():0 ] = data;
                buildItems($this, data, settings);
              },
              complete: function (jqXHR, textStatus) {
                // Clear this ID's open XML HTTP Request from the list
                if (settings.cancelRequests) {
                  openXHR[id] = null;
                }
              },
              dataType: 'json'
            });
				}
			}
		}
	},
	methods = {
		init: function(options) {
			var el = this;
      defaults.cache = {};
			el.jqmData("autocomplete", $.extend({}, defaults, options));
			var settings = el.jqmData("autocomplete");
			return el.unbind("keyup.autocomplete")
						.bind("keyup.autocomplete", handleInput)
						.next('.ui-input-clear')
						.bind('vclick', function(e){
							clearTarget(el, $(settings.target));
						});
		},
		// Allow dynamic update of source and link
		update: function(options) {
			var settings = this.jqmData("autocomplete");
			if (settings) {
				this.jqmData("autocomplete", $.extend(settings, options));
			}
			return this;
		},
		// Method to forcibly clear our target
		clear: function() {
			var settings = this.jqmData("autocomplete");
			if (settings) {
				clearTarget(this, $(settings.target));
			}
			return this;
		},
		// Method to destroy (cleanup) plugin
		destroy: function() {
			var settings = this.jqmData("autocomplete");
			if (settings) {
				clearTarget(this, $(settings.target));
				this.jqmRemoveData("autocomplete");
				this.unbind(".autocomplete");
			}
			return this;
		}
	};

	$.fn.autocomplete = function(method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		}
	};

})(jQuery);

/*!
 * jQuery UI 1.8.5
 *
 * Copyright 2010, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI
 */
(function (c, j) {
  function k(a) {
    return!c(a).parents().andSelf().filter(
      function () {
        return c.curCSS(this, "visibility") === "hidden" || c.expr.filters.hidden(this)
      }).length
  }

  c.ui = c.ui || {};
  if (!c.ui.version) {
    c.extend(c.ui, {version:"1.8.5", keyCode:{ALT:18, BACKSPACE:8, CAPS_LOCK:20, COMMA:188, COMMAND:91, COMMAND_LEFT:91, COMMAND_RIGHT:93, CONTROL:17, DELETE:46, DOWN:40, END:35, ENTER:13, ESCAPE:27, HOME:36, INSERT:45, LEFT:37, MENU:93, NUMPAD_ADD:107, NUMPAD_DECIMAL:110, NUMPAD_DIVIDE:111, NUMPAD_ENTER:108, NUMPAD_MULTIPLY:106,
      NUMPAD_SUBTRACT:109, PAGE_DOWN:34, PAGE_UP:33, PERIOD:190, RIGHT:39, SHIFT:16, SPACE:32, TAB:9, UP:38, WINDOWS:91}});
    c.fn.extend({_focus:c.fn.focus, focus:function (a, b) {
      return typeof a === "number" ? this.each(function () {
        var d = this;
        setTimeout(function () {
          c(d).focus();
          b && b.call(d)
        }, a)
      }) : this._focus.apply(this, arguments)
    }, scrollParent:function () {
      var a;
      a = c.browser.msie && /(static|relative)/.test(this.css("position")) || /absolute/.test(this.css("position")) ? this.parents().filter(
        function () {
          return/(relative|absolute|fixed)/.test(c.curCSS(this,
            "position", 1)) && /(auto|scroll)/.test(c.curCSS(this, "overflow", 1) + c.curCSS(this, "overflow-y", 1) + c.curCSS(this, "overflow-x", 1))
        }).eq(0) : this.parents().filter(
        function () {
          return/(auto|scroll)/.test(c.curCSS(this, "overflow", 1) + c.curCSS(this, "overflow-y", 1) + c.curCSS(this, "overflow-x", 1))
        }).eq(0);
      return/fixed/.test(this.css("position")) || !a.length ? c(document) : a
    }, zIndex:function (a) {
      if (a !== j)return this.css("zIndex", a);
      if (this.length) {
        a = c(this[0]);
        for (var b; a.length && a[0] !== document;) {
          b = a.css("position");
          if (b === "absolute" || b === "relative" || b === "fixed") {
            b = parseInt(a.css("zIndex"));
            if (!isNaN(b) && b != 0)return b
          }
          a = a.parent()
        }
      }
      return 0
    }, disableSelection:function () {
      return this.bind("mousedown.ui-disableSelection selectstart.ui-disableSelection", function (a) {
        a.preventDefault()
      })
    }, enableSelection:function () {
      return this.unbind(".ui-disableSelection")
    }});
    c.each(["Width", "Height"], function (a, b) {
      function d(f, g, l, m) {
        c.each(e, function () {
          g -= parseFloat(c.curCSS(f, "padding" + this, true)) || 0;
          if (l)g -= parseFloat(c.curCSS(f,
            "border" + this + "Width", true)) || 0;
          if (m)g -= parseFloat(c.curCSS(f, "margin" + this, true)) || 0
        });
        return g
      }

      var e = b === "Width" ? ["Left", "Right"] : ["Top", "Bottom"], h = b.toLowerCase(), i = {innerWidth:c.fn.innerWidth, innerHeight:c.fn.innerHeight, outerWidth:c.fn.outerWidth, outerHeight:c.fn.outerHeight};
      c.fn["inner" + b] = function (f) {
        if (f === j)return i["inner" + b].call(this);
        return this.each(function () {
          c.style(this, h, d(this, f) + "px")
        })
      };
      c.fn["outer" + b] = function (f, g) {
        if (typeof f !== "number")return i["outer" + b].call(this, f);
        return this.each(function () {
          c.style(this,
            h, d(this, f, true, g) + "px")
        })
      }
    });
    c.extend(c.expr[":"], {data:function (a, b, d) {
      return!!c.data(a, d[3])
    }, focusable:function (a) {
      var b = a.nodeName.toLowerCase(), d = c.attr(a, "tabindex");
      if ("area" === b) {
        b = a.parentNode;
        d = b.name;
        if (!a.href || !d || b.nodeName.toLowerCase() !== "map")return false;
        a = c("img[usemap=#" + d + "]")[0];
        return!!a && k(a)
      }
      return(/input|select|textarea|button|object/.test(b) ? !a.disabled : "a" == b ? a.href || !isNaN(d) : !isNaN(d)) && k(a)
    }, tabbable:function (a) {
      var b = c.attr(a, "tabindex");
      return(isNaN(b) || b >= 0) && c(a).is(":focusable")
    }});
    c(function () {
      var a = document.createElement("div"), b = document.body;
      c.extend(a.style, {minHeight:"100px", height:"auto", padding:0, borderWidth:0});
      c.support.minHeight = b.appendChild(a).offsetHeight === 100;
      b.removeChild(a).style.display = "none"
    });
    c.extend(c.ui, {plugin:{add:function (a, b, d) {
      a = c.ui[a].prototype;
      for (var e in d) {
        a.plugins[e] = a.plugins[e] || [];
        a.plugins[e].push([b, d[e]])
      }
    }, call:function (a, b, d) {
      if ((b = a.plugins[b]) && a.element[0].parentNode)for (var e = 0; e < b.length; e++)a.options[b[e][0]] && b[e][1].apply(a.element,
        d)
    }}, contains:function (a, b) {
      return document.compareDocumentPosition ? a.compareDocumentPosition(b) & 16 : a !== b && a.contains(b)
    }, hasScroll:function (a, b) {
      if (c(a).css("overflow") === "hidden")return false;
      b = b && b === "left" ? "scrollLeft" : "scrollTop";
      var d = false;
      if (a[b] > 0)return true;
      a[b] = 1;
      d = a[b] > 0;
      a[b] = 0;
      return d
    }, isOverAxis:function (a, b, d) {
      return a > b && a < b + d
    }, isOver:function (a, b, d, e, h, i) {
      return c.ui.isOverAxis(a, d, h) && c.ui.isOverAxis(b, e, i)
    }})
  }
})(jQuery);
;
/*
 * jQuery UI Datepicker 1.8.5
 *
 * Copyright 2010, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Datepicker
 *
 * Depends:
 *	jquery.ui.core.js
 * todo Datepicker
 */
(function (d, G) {
  function L() {
    this.debug = false;
    this._curInst = null;
    this._keyEvent = false;
    this._disabledInputs = [];
    this._inDialog = this._datepickerShowing = false;
    this._mainDivId = "ui-datepicker-div";
    this._inlineClass = "ui-datepicker-inline";
    this._appendClass = "ui-datepicker-append";
    this._triggerClass = "ui-datepicker-trigger";
    this._dialogClass = "ui-datepicker-dialog";
    this._disableClass = "ui-datepicker-disabled";
    this._unselectableClass = "ui-datepicker-unselectable";
    this._currentClass = "ui-datepicker-current-day";
    this._dayOverClass =
      "ui-datepicker-days-cell-over";
    this.regional = [];
    this.regional[""] = {closeText:"Done", prevText:"Prev", nextText:"Next", currentText:"Today", monthNames:["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"], monthNamesShort:["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"], dayNames:["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"], dayNamesShort:["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"], dayNamesMin:["Su",
      "Mo", "Tu", "We", "Th", "Fr", "Sa"], weekHeader:"Wk", dateFormat:'mm/dd/yy', firstDay:0, isRTL:false, showMonthAfterYear:false, yearSuffix:""};
    this._defaults = {showOn:"focus", showAnim:"fadeIn", showOptions:{}, defaultDate:null, appendText:"", buttonText:"...", buttonImage:"", buttonImageOnly:false, hideIfNoPrevNext:false, navigationAsDateFormat:false, gotoCurrent:false, changeMonth:false, changeYear:false, yearRange:"c-10:c+10", showOtherMonths:false, selectOtherMonths:false, showWeek:false, calculateWeek:this.iso8601Week, shortYearCutoff:"+10",
      minDate:null, maxDate:null, duration:"fast", beforeShowDay:null, beforeShow:null, onSelect:null, onChangeMonthYear:null, onClose:null, numberOfMonths:1, showCurrentAtPos:0, stepMonths:1, stepBigMonths:12, altField:"", altFormat:"", constrainInput:true, showButtonPanel:false, autoSize:false};
    d.extend(this._defaults, this.regional[""]);
    this.dpDiv = d('<div id="' + this._mainDivId + '" class="ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all ui-helper-hidden-accessible"></div>')
  }

  function E(a, b) {
    d.extend(a,
      b);
    for (var c in b)if (b[c] == null || b[c] == G)a[c] = b[c];
    return a
  }

  d.extend(d.ui, {datepicker:{version:"1.8.5"}});
  var y = (new Date).getTime();
  d.extend(L.prototype, {markerClassName:"hasDatepicker", log:function () {
    this.debug && console.log.apply("", arguments)
  }, _widgetDatepicker:function () {
    return this.dpDiv
  }, setDefaults:function (a) {
    E(this._defaults, a || {});
    return this
  }, _attachDatepicker:function (a, b) {
    var c = null;
    for (var e in this._defaults) {
      var f = a.getAttribute("date:" + e);
      if (f) {
        c = c || {};
        try {
          c[e] = eval(f)
        } catch (h) {
          c[e] =
            f
        }
      }
    }
    e = a.nodeName.toLowerCase();
    f = e == "div" || e == "span";
    if (!a.id) {
      this.uuid += 1;
      a.id = "dp" + this.uuid
    }
    var i = this._newInst(d(a), f);
    i.settings = d.extend({}, b || {}, c || {});
    if (e == "input")this._connectDatepicker(a, i); else f && this._inlineDatepicker(a, i)
  }, _newInst:function (a, b) {
    return{id:a[0].id.replace(/([^A-Za-z0-9_])/g, "\\\\$1"), input:a, selectedDay:0, selectedMonth:0, selectedYear:0, drawMonth:0, drawYear:0, inline:b, dpDiv:!b ? this.dpDiv : d('<div class="' + this._inlineClass + ' ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all"></div>')}
  },
    _connectDatepicker:function (a, b) {
      var c = d(a);
      b.append = d([]);
      b.trigger = d([]);
      if (!c.hasClass(this.markerClassName)) {
        this._attachments(c, b);
        c.addClass(this.markerClassName).keydown(this._doKeyDown).keypress(this._doKeyPress).keyup(this._doKeyUp).bind("setData.datepicker",
          function (e, f, h) {
            b.settings[f] = h
          }).bind("getData.datepicker", function (e, f) {
          return this._get(b, f)
        });
        this._autoSize(b);
        d.data(a, "datepicker", b)
      }
    }, _attachments:function (a, b) {
      var c = this._get(b, "appendText"), e = this._get(b, "isRTL");
      b.append &&
      b.append.remove();
      if (c) {
        b.append = d('<span class="' + this._appendClass + '">' + c + "</span>");
        a[e ? "before" : "after"](b.append)
      }
      a.unbind("focus", this._showDatepicker);
      b.trigger && b.trigger.remove();
      c = this._get(b, "showOn");
      if (c == "focus" || c == "both")a.focus(this._showDatepicker);
      if (c == "button" || c == "both") {
        c = this._get(b, "buttonText");
        var f = this._get(b, "buttonImage");
        b.trigger = d(this._get(b, "buttonImageOnly") ? d("<img/>").addClass(this._triggerClass).attr({src:f, alt:c, title:c}) : d('<button type="button"></button>').addClass(this._triggerClass).html(f ==
          "" ? c : d("<img/>").attr({src:f, alt:c, title:c})));
        a[e ? "before" : "after"](b.trigger);
        b.trigger.click(function () {
          d.datepicker._datepickerShowing && d.datepicker._lastInput == a[0] ? d.datepicker._hideDatepicker() : d.datepicker._showDatepicker(a[0]);
          return false
        })
      }
    }, _autoSize:function (a) {
      if (this._get(a, "autoSize") && !a.inline) {
        var b = new Date(2009, 11, 20), c = this._get(a, "dateFormat");
        if (c.match(/[DM]/)) {
          var e = function (f) {
            for (var h = 0, i = 0, g = 0; g < f.length; g++)if (f[g].length > h) {
              h = f[g].length;
              i = g
            }
            return i
          };
          b.setMonth(e(this._get(a,
            c.match(/MM/) ? "monthNames" : "monthNamesShort")));
          b.setDate(e(this._get(a, c.match(/DD/) ? "dayNames" : "dayNamesShort")) + 20 - b.getDay())
        }
        a.input.attr("size", this._formatDate(a, b).length)
      }
    }, _inlineDatepicker:function (a, b) {
      var c = d(a);
      if (!c.hasClass(this.markerClassName)) {
        c.addClass(this.markerClassName).append(b.dpDiv).bind("setData.datepicker",
          function (e, f, h) {
            b.settings[f] = h
          }).bind("getData.datepicker", function (e, f) {
          return this._get(b, f)
        });
        d.data(a, "datepicker", b);
        this._setDate(b, this._getDefaultDate(b),
          true);
        this._updateDatepicker(b);
        this._updateAlternate(b)
      }
    }, _dialogDatepicker:function (a, b, c, e, f) {
      a = this._dialogInst;
      if (!a) {
        this.uuid += 1;
        this._dialogInput = d('<input type="text" id="' + ("dp" + this.uuid) + '" style="position: absolute; top: -100px; width: 0px; z-index: -10;"/>');
        this._dialogInput.keydown(this._doKeyDown);
        d("body").append(this._dialogInput);
        a = this._dialogInst = this._newInst(this._dialogInput, false);
        a.settings = {};
        d.data(this._dialogInput[0], "datepicker", a)
      }
      E(a.settings, e || {});
      b = b && b.constructor ==
        Date ? this._formatDate(a, b) : b;
      this._dialogInput.val(b);
      this._pos = f ? f.length ? f : [f.pageX, f.pageY] : null;
      if (!this._pos)this._pos = [document.documentElement.clientWidth / 2 - 100 + (document.documentElement.scrollLeft || document.body.scrollLeft), document.documentElement.clientHeight / 2 - 150 + (document.documentElement.scrollTop || document.body.scrollTop)];
      this._dialogInput.css("left", this._pos[0] + 20 + "px").css("top", this._pos[1] + "px");
      a.settings.onSelect = c;
      this._inDialog = true;
      this.dpDiv.addClass(this._dialogClass);
      this._showDatepicker(this._dialogInput[0]);
      d.blockUI && d.blockUI(this.dpDiv);
      d.data(this._dialogInput[0], "datepicker", a);
      return this
    }, _destroyDatepicker:function (a) {
      var b = d(a), c = d.data(a, "datepicker");
      if (b.hasClass(this.markerClassName)) {
        var e = a.nodeName.toLowerCase();
        d.removeData(a, "datepicker");
        if (e == "input") {
          c.append.remove();
          c.trigger.remove();
          b.removeClass(this.markerClassName).unbind("focus", this._showDatepicker).unbind("keydown", this._doKeyDown).unbind("keypress", this._doKeyPress).unbind("keyup", this._doKeyUp)
        } else if (e == "div" || e == "span")b.removeClass(this.markerClassName).empty()
      }
    },
    _enableDatepicker:function (a) {
      var b = d(a), c = d.data(a, "datepicker");
      if (b.hasClass(this.markerClassName)) {
        var e = a.nodeName.toLowerCase();
        if (e == "input") {
          a.disabled = false;
          c.trigger.filter("button").each(
            function () {
              this.disabled = false
            }).end().filter("img").css({opacity:"1.0", cursor:""})
        } else if (e == "div" || e == "span")b.children("." + this._inlineClass).children().removeClass("ui-state-disabled");
        this._disabledInputs = d.map(this._disabledInputs, function (f) {
          return f == a ? null : f
        })
      }
    }, _disableDatepicker:function (a) {
      var b =
        d(a), c = d.data(a, "datepicker");
      if (b.hasClass(this.markerClassName)) {
        var e = a.nodeName.toLowerCase();
        if (e == "input") {
          a.disabled = true;
          c.trigger.filter("button").each(
            function () {
              this.disabled = true
            }).end().filter("img").css({opacity:"0.5", cursor:"default"})
        } else if (e == "div" || e == "span")b.children("." + this._inlineClass).children().addClass("ui-state-disabled");
        this._disabledInputs = d.map(this._disabledInputs, function (f) {
          return f == a ? null : f
        });
        this._disabledInputs[this._disabledInputs.length] = a
      }
    }, _isDisabledDatepicker:function (a) {
      if (!a)return false;
      for (var b = 0; b < this._disabledInputs.length; b++)if (this._disabledInputs[b] == a)return true;
      return false
    }, _getInst:function (a) {
      try {
        return d.data(a, "datepicker")
      } catch (b) {
        throw"Missing instance data for this datepicker";
      }
    }, _optionDatepicker:function (a, b, c) {
      var e = this._getInst(a);
      if (arguments.length == 2 && typeof b == "string")return b == "defaults" ? d.extend({}, d.datepicker._defaults) : e ? b == "all" ? d.extend({}, e.settings) : this._get(e, b) : null;
      var f = b || {};
      if (typeof b == "string") {
        f = {};
        f[b] = c
      }
      if (e) {
        this._curInst == e &&
        this._hideDatepicker();
        var h = this._getDateDatepicker(a, true);
        E(e.settings, f);
        this._attachments(d(a), e);
        this._autoSize(e);
        this._setDateDatepicker(a, h);
        this._updateDatepicker(e)
      }
    }, _changeDatepicker:function (a, b, c) {
      this._optionDatepicker(a, b, c)
    }, _refreshDatepicker:function (a) {
      (a = this._getInst(a)) && this._updateDatepicker(a)
    }, _setDateDatepicker:function (a, b) {
      if (a = this._getInst(a)) {
        this._setDate(a, b);
        this._updateDatepicker(a);
        this._updateAlternate(a)
      }
    }, _getDateDatepicker:function (a, b) {
      (a = this._getInst(a)) &&
        !a.inline && this._setDateFromField(a, b);
      return a ? this._getDate(a) : null
    }, _doKeyDown:function (a) {
      var b = d.datepicker._getInst(a.target), c = true, e = b.dpDiv.is(".ui-datepicker-rtl");
      b._keyEvent = true;
      if (d.datepicker._datepickerShowing)switch (a.keyCode) {
        case 9:
          d.datepicker._hideDatepicker();
          c = false;
          break;
        case 13:
          c = d("td." + d.datepicker._dayOverClass, b.dpDiv).add(d("td." + d.datepicker._currentClass, b.dpDiv));
          c[0] ? d.datepicker._selectDay(a.target, b.selectedMonth, b.selectedYear, c[0]) : d.datepicker._hideDatepicker();
          return false;
        case 27:
          d.datepicker._hideDatepicker();
          break;
        case 33:
          d.datepicker._adjustDate(a.target, a.ctrlKey ? -d.datepicker._get(b, "stepBigMonths") : -d.datepicker._get(b, "stepMonths"), "M");
          break;
        case 34:
          d.datepicker._adjustDate(a.target, a.ctrlKey ? +d.datepicker._get(b, "stepBigMonths") : +d.datepicker._get(b, "stepMonths"), "M");
          break;
        case 35:
          if (a.ctrlKey || a.metaKey)d.datepicker._clearDate(a.target);
          c = a.ctrlKey || a.metaKey;
          break;
        case 36:
          if (a.ctrlKey || a.metaKey)d.datepicker._gotoToday(a.target);
          c = a.ctrlKey ||
            a.metaKey;
          break;
        case 37:
          if (a.ctrlKey || a.metaKey)d.datepicker._adjustDate(a.target, e ? +1 : -1, "D");
          c = a.ctrlKey || a.metaKey;
          if (a.originalEvent.altKey)d.datepicker._adjustDate(a.target, a.ctrlKey ? -d.datepicker._get(b, "stepBigMonths") : -d.datepicker._get(b, "stepMonths"), "M");
          break;
        case 38:
          if (a.ctrlKey || a.metaKey)d.datepicker._adjustDate(a.target, -7, "D");
          c = a.ctrlKey || a.metaKey;
          break;
        case 39:
          if (a.ctrlKey || a.metaKey)d.datepicker._adjustDate(a.target, e ? -1 : +1, "D");
          c = a.ctrlKey || a.metaKey;
          if (a.originalEvent.altKey)d.datepicker._adjustDate(a.target,
            a.ctrlKey ? +d.datepicker._get(b, "stepBigMonths") : +d.datepicker._get(b, "stepMonths"), "M");
          break;
        case 40:
          if (a.ctrlKey || a.metaKey)d.datepicker._adjustDate(a.target, +7, "D");
          c = a.ctrlKey || a.metaKey;
          break;
        default:
          c = false
      } else if (a.keyCode == 36 && a.ctrlKey)d.datepicker._showDatepicker(this); else c = false;
      if (c) {
        a.preventDefault();
        a.stopPropagation()
      }
    }, _doKeyPress:function (a) {
      var b = d.datepicker._getInst(a.target);
      if (d.datepicker._get(b, "constrainInput")) {
        b = d.datepicker._possibleChars(d.datepicker._get(b, "dateFormat"));
        var c = String.fromCharCode(a.charCode == G ? a.keyCode : a.charCode);
        return a.ctrlKey || c < " " || !b || b.indexOf(c) > -1
      }
    }, _doKeyUp:function (a) {
      a = d.datepicker._getInst(a.target);
      if (a.input.val() != a.lastVal)try {
        if (d.datepicker.parseDate(d.datepicker._get(a, "dateFormat"), a.input ? a.input.val() : null, d.datepicker._getFormatConfig(a))) {
          d.datepicker._setDateFromField(a);
          d.datepicker._updateAlternate(a);
          d.datepicker._updateDatepicker(a)
        }
      } catch (b) {
        d.datepicker.log(b)
      }
      return true
    }, _showDatepicker:function (a) {
      a = a.target ||
        a;
      if (a.nodeName.toLowerCase() != "input")a = d("input", a.parentNode)[0];
      if (!(d.datepicker._isDisabledDatepicker(a) || d.datepicker._lastInput == a)) {
        var b = d.datepicker._getInst(a);
        d.datepicker._curInst && d.datepicker._curInst != b && d.datepicker._curInst.dpDiv.stop(true, true);
        var c = d.datepicker._get(b, "beforeShow");
        E(b.settings, c ? c.apply(a, [a, b]) : {});
        b.lastVal = null;
        d.datepicker._lastInput = a;
        d.datepicker._setDateFromField(b);
        if (d.datepicker._inDialog)a.value = "";
        if (!d.datepicker._pos) {
          d.datepicker._pos = d.datepicker._findPos(a);
          d.datepicker._pos[1] += a.offsetHeight
        }
        var e = false;
        d(a).parents().each(function () {
          e |= d(this).css("position") == "fixed";
          return!e
        });
        if (e && d.browser.opera) {
          d.datepicker._pos[0] -= document.documentElement.scrollLeft;
          d.datepicker._pos[1] -= document.documentElement.scrollTop
        }
        c = {left:d.datepicker._pos[0], top:d.datepicker._pos[1]};
        d.datepicker._pos = null;
        b.dpDiv.css({position:"absolute", display:"block", top:"-1000px"});
        d.datepicker._updateDatepicker(b);
        c = d.datepicker._checkOffset(b, c, e);
        b.dpDiv.css({position:d.datepicker._inDialog &&
          d.blockUI ? "static" : e ? "fixed" : "absolute", display:"none", left:c.left + "px", top:c.top + "px"});
        if (!b.inline) {
          c = d.datepicker._get(b, "showAnim");
          var f = d.datepicker._get(b, "duration"), h = function () {
            d.datepicker._datepickerShowing = true;
            var i = d.datepicker._getBorders(b.dpDiv);
            b.dpDiv.find("iframe.ui-datepicker-cover").css({left:-i[0], top:-i[1], width:b.dpDiv.outerWidth(), height:b.dpDiv.outerHeight()})
          };
          b.dpDiv.zIndex(d(a).zIndex() + 1);
          d.effects && d.effects[c] ? b.dpDiv.show(c, d.datepicker._get(b, "showOptions"), f,
            h) : b.dpDiv[c || "show"](c ? f : null, h);
          if (!c || !f)h();
          b.input.is(":visible") && !b.input.is(":disabled") && b.input.focus();
          d.datepicker._curInst = b
        }
      }
    }, _updateDatepicker:function (a) {
      var b = this, c = d.datepicker._getBorders(a.dpDiv);
      a.dpDiv.empty().append(this._generateHTML(a)).find("iframe.ui-datepicker-cover").css({left:-c[0], top:-c[1], width:a.dpDiv.outerWidth(), height:a.dpDiv.outerHeight()}).end().find("button, .ui-datepicker-prev, .ui-datepicker-next, .ui-datepicker-calendar td a").bind("mouseout",
        function () {
          d(this).removeClass("ui-state-hover");
          this.className.indexOf("ui-datepicker-prev") != -1 && d(this).removeClass("ui-datepicker-prev-hover");
          this.className.indexOf("ui-datepicker-next") != -1 && d(this).removeClass("ui-datepicker-next-hover")
        }).bind("mouseover",
        function () {
          if (!b._isDisabledDatepicker(a.inline ? a.dpDiv.parent()[0] : a.input[0])) {
            d(this).parents(".ui-datepicker-calendar").find("a").removeClass("ui-state-hover");
            d(this).addClass("ui-state-hover");
            this.className.indexOf("ui-datepicker-prev") != -1 && d(this).addClass("ui-datepicker-prev-hover");
            this.className.indexOf("ui-datepicker-next") != -1 && d(this).addClass("ui-datepicker-next-hover")
          }
        }).end().find("." + this._dayOverClass + " a").trigger("mouseover").end();
      c = this._getNumberOfMonths(a);
      var e = c[1];
      e > 1 ? a.dpDiv.addClass("ui-datepicker-multi-" + e).css("width", 17 * e + "em") : a.dpDiv.removeClass("ui-datepicker-multi-2 ui-datepicker-multi-3 ui-datepicker-multi-4").width("");
      a.dpDiv[(c[0] != 1 || c[1] != 1 ? "add" : "remove") + "Class"]("ui-datepicker-multi");
      a.dpDiv[(this._get(a, "isRTL") ? "add" : "remove") + "Class"]("ui-datepicker-rtl");
      a == d.datepicker._curInst && d.datepicker._datepickerShowing && a.input && a.input.is(":visible") && !a.input.is(":disabled") && a.input.focus()
    }, _getBorders:function (a) {
      var b = function (c) {
        return{thin:1, medium:2, thick:3}[c] || c
      };
      return[parseFloat(b(a.css("border-left-width"))), parseFloat(b(a.css("border-top-width")))]
    }, _checkOffset:function (a, b, c) {
      var e = a.dpDiv.outerWidth(), f = a.dpDiv.outerHeight(), h = a.input ? a.input.outerWidth() : 0, i = a.input ? a.input.outerHeight() : 0, g = document.documentElement.clientWidth + d(document).scrollLeft(),
        k = document.documentElement.clientHeight + d(document).scrollTop();
      b.left -= this._get(a, "isRTL") ? e - h : 0;
      b.left -= c && b.left == a.input.offset().left ? d(document).scrollLeft() : 0;
      b.top -= c && b.top == a.input.offset().top + i ? d(document).scrollTop() : 0;
      b.left -= Math.min(b.left, b.left + e > g && g > e ? Math.abs(b.left + e - g) : 0);
      b.top -= Math.min(b.top, b.top + f > k && k > f ? Math.abs(f + i) : 0);
      return b
    }, _findPos:function (a) {
      for (var b = this._get(this._getInst(a), "isRTL"); a && (a.type == "hidden" || a.nodeType != 1);)a = a[b ? "previousSibling" : "nextSibling"];
      a = d(a).offset();
      return[a.left, a.top]
    }, _hideDatepicker:function (a) {
      var b = this._curInst;
      if (!(!b || a && b != d.data(a, "datepicker")))if (this._datepickerShowing) {
        a = this._get(b, "showAnim");
        var c = this._get(b, "duration"), e = function () {
          d.datepicker._tidyDialog(b);
          this._curInst = null
        };
        d.effects && d.effects[a] ? b.dpDiv.hide(a, d.datepicker._get(b, "showOptions"), c, e) : b.dpDiv[a == "slideDown" ? "slideUp" : a == "fadeIn" ? "fadeOut" : "hide"](a ? c : null, e);
        a || e();
        if (a = this._get(b, "onClose"))a.apply(b.input ? b.input[0] : null, [b.input ? b.input.val() :
          "", b]);
        this._datepickerShowing = false;
        this._lastInput = null;
        if (this._inDialog) {
          this._dialogInput.css({position:"absolute", left:"0", top:"-100px"});
          if (d.blockUI) {
            d.unblockUI();
            d("body").append(this.dpDiv)
          }
        }
        this._inDialog = false
      }
    }, _tidyDialog:function (a) {
      a.dpDiv.removeClass(this._dialogClass).unbind(".ui-datepicker-calendar")
    }, _checkExternalClick:function (a) {
      if (d.datepicker._curInst) {
        a = d(a.target);
        a[0].id != d.datepicker._mainDivId && a.parents("#" + d.datepicker._mainDivId).length == 0 && !a.hasClass(d.datepicker.markerClassName) &&
          !a.hasClass(d.datepicker._triggerClass) && d.datepicker._datepickerShowing && !(d.datepicker._inDialog && d.blockUI) && d.datepicker._hideDatepicker()
      }
    }, _adjustDate:function (a, b, c) {
      a = d(a);
      var e = this._getInst(a[0]);
      if (!this._isDisabledDatepicker(a[0])) {
        this._adjustInstDate(e, b + (c == "M" ? this._get(e, "showCurrentAtPos") : 0), c);
        this._updateDatepicker(e)
      }
    }, _gotoToday:function (a) {
      a = d(a);
      var b = this._getInst(a[0]);
      if (this._get(b, "gotoCurrent") && b.currentDay) {
        b.selectedDay = b.currentDay;
        b.drawMonth = b.selectedMonth = b.currentMonth;
        b.drawYear = b.selectedYear = b.currentYear
      } else {
        var c = new Date;
        b.selectedDay = c.getDate();
        b.drawMonth = b.selectedMonth = c.getMonth();
        b.drawYear = b.selectedYear = c.getFullYear()
      }
      this._notifyChange(b);
      this._adjustDate(a)
    }, _selectMonthYear:function (a, b, c) {
      a = d(a);
      var e = this._getInst(a[0]);
      e._selectingMonthYear = false;
      e["selected" + (c == "M" ? "Month" : "Year")] = e["draw" + (c == "M" ? "Month" : "Year")] = parseInt(b.options[b.selectedIndex].value, 10);
      this._notifyChange(e);
      this._adjustDate(a)
    }, _clickMonthYear:function (a) {
      var b =
        this._getInst(d(a)[0]);
      b.input && b._selectingMonthYear && setTimeout(function () {
        b.input.focus()
      }, 0);
      b._selectingMonthYear = !b._selectingMonthYear
    }, _selectDay:function (a, b, c, e) {
      var f = d(a);
      if (!(d(e).hasClass(this._unselectableClass) || this._isDisabledDatepicker(f[0]))) {
        f = this._getInst(f[0]);
        f.selectedDay = f.currentDay = d("a", e).html();
        f.selectedMonth = f.currentMonth = b;
        f.selectedYear = f.currentYear = c;
        this._selectDate(a, this._formatDate(f, f.currentDay, f.currentMonth, f.currentYear))
      }
    }, _clearDate:function (a) {
      a =
        d(a);
      this._getInst(a[0]);
      this._selectDate(a, "")
    }, _selectDate:function (a, b) {
      a = this._getInst(d(a)[0]);
      b = b != null ? b : this._formatDate(a);
      a.input && a.input.val(b);
      this._updateAlternate(a);
      var c = this._get(a, "onSelect");
      if (c)c.apply(a.input ? a.input[0] : null, [b, a]); else a.input && a.input.trigger("change");
      if (a.inline)this._updateDatepicker(a); else {
        this._hideDatepicker();
        this._lastInput = a.input[0];
        typeof a.input[0] != "object" && a.input.focus();
        this._lastInput = null
      }
    }, _updateAlternate:function (a) {
      var b = this._get(a,
        "altField");
      if (b) {
        var c = this._get(a, "altFormat") || this._get(a, "dateFormat"), e = this._getDate(a), f = this.formatDate(c, e, this._getFormatConfig(a));
        d(b).each(function () {
          d(this).val(f)
        })
      }
    }, noWeekends:function (a) {
      a = a.getDay();
      return[a > 0 && a < 6, ""]
    }, iso8601Week:function (a) {
      a = new Date(a.getTime());
      a.setDate(a.getDate() + 4 - (a.getDay() || 7));
      var b = a.getTime();
      a.setMonth(0);
      a.setDate(1);
      return Math.floor(Math.round((b - a) / 864E5) / 7) + 1
    }, parseDate:function (a, b, c) {
      if (a == null || b == null)throw"Invalid arguments";
      b = typeof b ==
        "object" ? b.toString() : b + "";
      if (b == "")return null;
      for (var e = (c ? c.shortYearCutoff : null) || this._defaults.shortYearCutoff, f = (c ? c.dayNamesShort : null) || this._defaults.dayNamesShort, h = (c ? c.dayNames : null) || this._defaults.dayNames, i = (c ? c.monthNamesShort : null) || this._defaults.monthNamesShort, g = (c ? c.monthNames : null) || this._defaults.monthNames, k = c = -1, l = -1, u = -1, j = false, o = function (p) {
        (p = z + 1 < a.length && a.charAt(z + 1) == p) && z++;
        return p
      }, m = function (p) {
        o(p);
        p = new RegExp("^\\d{1," + (p == "@" ? 14 : p == "!" ? 20 : p == "y" ? 4 : p == "o" ?
          3 : 2) + "}");
        p = b.substring(s).match(p);
        if (!p)throw"Missing number at position " + s;
        s += p[0].length;
        return parseInt(p[0], 10)
      }, n = function (p, w, H) {
        p = o(p) ? H : w;
        for (w = 0; w < p.length; w++)if (b.substr(s, p[w].length).toLowerCase() == p[w].toLowerCase()) {
          s += p[w].length;
          return w + 1
        }
        throw"Unknown name at position " + s;
      }, r = function () {
        if (b.charAt(s) != a.charAt(z))throw"Unexpected literal at position " + s;
        s++
      }, s = 0, z = 0; z < a.length; z++)if (j)if (a.charAt(z) == "'" && !o("'"))j = false; else r(); else switch (a.charAt(z)) {
        case "d":
          l = m("d");
          break;
        case "D":
          n("D", f, h);
          break;
        case "o":
          u = m("o");
          break;
        case "m":
          k = m("m");
          break;
        case "M":
          k = n("M", i, g);
          break;
        case "y":
          c = m("y");
          break;
        case "@":
          var v = new Date(m("@"));
          c = v.getFullYear();
          k = v.getMonth() + 1;
          l = v.getDate();
          break;
        case "!":
          v = new Date((m("!") - this._ticksTo1970) / 1E4);
          c = v.getFullYear();
          k = v.getMonth() + 1;
          l = v.getDate();
          break;
        case "'":
          if (o("'"))r(); else j = true;
          break;
        default:
          r()
      }
      if (c == -1)c = (new Date).getFullYear(); else if (c < 100)c += (new Date).getFullYear() - (new Date).getFullYear() % 100 + (c <= e ? 0 : -100);
      if (u >
        -1) {
        k = 1;
        l = u;
        do {
          e = this._getDaysInMonth(c, k - 1);
          if (l <= e)break;
          k++;
          l -= e
        } while (1)
      }
      v = this._daylightSavingAdjust(new Date(c, k - 1, l));
      if (v.getFullYear() != c || v.getMonth() + 1 != k || v.getDate() != l)throw"Invalid date";
      return v
    }, ATOM:"yy-mm-dd", COOKIE:"D, dd M yy", ISO_8601:"yy-mm-dd", RFC_822:"D, d M y", RFC_850:"DD, dd-M-y", RFC_1036:"D, d M y", RFC_1123:"D, d M yy", RFC_2822:"D, d M yy", RSS:"D, d M y", TICKS:"!", TIMESTAMP:"@", W3C:"yy-mm-dd", _ticksTo1970:(718685 + Math.floor(492.5) - Math.floor(19.7) + Math.floor(4.925)) * 24 *
      60 * 60 * 1E7, formatDate:function (a, b, c) {
      if (!b)return"";
      var e = (c ? c.dayNamesShort : null) || this._defaults.dayNamesShort, f = (c ? c.dayNames : null) || this._defaults.dayNames, h = (c ? c.monthNamesShort : null) || this._defaults.monthNamesShort;
      c = (c ? c.monthNames : null) || this._defaults.monthNames;
      var i = function (o) {
        (o = j + 1 < a.length && a.charAt(j + 1) == o) && j++;
        return o
      }, g = function (o, m, n) {
        m = "" + m;
        if (i(o))for (; m.length < n;)m = "0" + m;
        return m
      }, k = function (o, m, n, r) {
        return i(o) ? r[m] : n[m]
      }, l = "", u = false;
      if (b)for (var j = 0; j < a.length; j++)if (u)if (a.charAt(j) ==
        "'" && !i("'"))u = false; else l += a.charAt(j); else switch (a.charAt(j)) {
        case "d":
          l += g("d", b.getDate(), 2);
          break;
        case "D":
          l += k("D", b.getDay(), e, f);
          break;
        case "o":
          l += g("o", (b.getTime() - (new Date(b.getFullYear(), 0, 0)).getTime()) / 864E5, 3);
          break;
        case "m":
          l += g("m", b.getMonth() + 1, 2);
          break;
        case "M":
          l += k("M", b.getMonth(), h, c);
          break;
        case "y":
          l += i("y") ? b.getFullYear() : (b.getYear() % 100 < 10 ? "0" : "") + b.getYear() % 100;
          break;
        case "@":
          l += b.getTime();
          break;
        case "!":
          l += b.getTime() * 1E4 + this._ticksTo1970;
          break;
        case "'":
          if (i("'"))l +=
            "'"; else u = true;
          break;
        default:
          l += a.charAt(j)
      }
      return l
    }, _possibleChars:function (a) {
      for (var b = "", c = false, e = function (h) {
        (h = f + 1 < a.length && a.charAt(f + 1) == h) && f++;
        return h
      }, f = 0; f < a.length; f++)if (c)if (a.charAt(f) == "'" && !e("'"))c = false; else b += a.charAt(f); else switch (a.charAt(f)) {
        case "d":
        case "m":
        case "y":
        case "@":
          b += "0123456789";
          break;
        case "D":
        case "M":
          return null;
        case "'":
          if (e("'"))b += "'"; else c = true;
          break;
        default:
          b += a.charAt(f)
      }
      return b
    }, _get:function (a, b) {
      return a.settings[b] !== G ? a.settings[b] : this._defaults[b]
    },
    _setDateFromField:function (a, b) {
      if (a.input.val() != a.lastVal) {
        var c = this._get(a, "dateFormat"), e = a.lastVal = a.input ? a.input.val() : null, f, h;
        f = h = this._getDefaultDate(a);
        var i = this._getFormatConfig(a);
        try {
          f = this.parseDate(c, e, i) || h
        } catch (g) {
          this.log(g);
          e = b ? "" : e
        }
        a.selectedDay = f.getDate();
        a.drawMonth = a.selectedMonth = f.getMonth();
        a.drawYear = a.selectedYear = f.getFullYear();
        a.currentDay = e ? f.getDate() : 0;
        a.currentMonth = e ? f.getMonth() : 0;
        a.currentYear = e ? f.getFullYear() : 0;
        this._adjustInstDate(a)
      }
    }, _getDefaultDate:function (a) {
      return this._restrictMinMax(a,
        this._determineDate(a, this._get(a, "defaultDate"), new Date))
    }, _determineDate:function (a, b, c) {
      var e = function (h) {
        var i = new Date;
        i.setDate(i.getDate() + h);
        return i
      }, f = function (h) {
        try {
          return d.datepicker.parseDate(d.datepicker._get(a, "dateFormat"), h, d.datepicker._getFormatConfig(a))
        } catch (i) {
        }
        var g = (h.toLowerCase().match(/^c/) ? d.datepicker._getDate(a) : null) || new Date, k = g.getFullYear(), l = g.getMonth();
        g = g.getDate();
        for (var u = /([+-]?[0-9]+)\s*(d|D|w|W|m|M|y|Y)?/g, j = u.exec(h); j;) {
          switch (j[2] || "d") {
            case "d":
            case "D":
              g +=
                parseInt(j[1], 10);
              break;
            case "w":
            case "W":
              g += parseInt(j[1], 10) * 7;
              break;
            case "m":
            case "M":
              l += parseInt(j[1], 10);
              g = Math.min(g, d.datepicker._getDaysInMonth(k, l));
              break;
            case "y":
            case "Y":
              k += parseInt(j[1], 10);
              g = Math.min(g, d.datepicker._getDaysInMonth(k, l));
              break
          }
          j = u.exec(h)
        }
        return new Date(k, l, g)
      };
      if (b = (b = b == null ? c : typeof b == "string" ? f(b) : typeof b == "number" ? isNaN(b) ? c : e(b) : b) && b.toString() == "Invalid Date" ? c : b) {
        b.setHours(0);
        b.setMinutes(0);
        b.setSeconds(0);
        b.setMilliseconds(0)
      }
      return this._daylightSavingAdjust(b)
    },
    _daylightSavingAdjust:function (a) {
      if (!a)return null;
      a.setHours(a.getHours() > 12 ? a.getHours() + 2 : 0);
      return a
    }, _setDate:function (a, b, c) {
      var e = !b, f = a.selectedMonth, h = a.selectedYear;
      b = this._restrictMinMax(a, this._determineDate(a, b, new Date));
      a.selectedDay = a.currentDay = b.getDate();
      a.drawMonth = a.selectedMonth = a.currentMonth = b.getMonth();
      a.drawYear = a.selectedYear = a.currentYear = b.getFullYear();
      if ((f != a.selectedMonth || h != a.selectedYear) && !c)this._notifyChange(a);
      this._adjustInstDate(a);
      if (a.input)a.input.val(e ?
        "" : this._formatDate(a))
    }, _getDate:function (a) {
      return!a.currentYear || a.input && a.input.val() == "" ? null : this._daylightSavingAdjust(new Date(a.currentYear, a.currentMonth, a.currentDay))
    }, _generateHTML:function (a) {
      var b = new Date;
      b = this._daylightSavingAdjust(new Date(b.getFullYear(), b.getMonth(), b.getDate()));
      var c = this._get(a, "isRTL"), e = this._get(a, "showButtonPanel"), f = this._get(a, "hideIfNoPrevNext"), h = this._get(a, "navigationAsDateFormat"), i = this._getNumberOfMonths(a), g = this._get(a, "showCurrentAtPos"), k =
        this._get(a, "stepMonths"), l = i[0] != 1 || i[1] != 1, u = this._daylightSavingAdjust(!a.currentDay ? new Date(9999, 9, 9) : new Date(a.currentYear, a.currentMonth, a.currentDay)), j = this._getMinMaxDate(a, "min"), o = this._getMinMaxDate(a, "max");
      g = a.drawMonth - g;
      var m = a.drawYear;
      if (g < 0) {
        g += 12;
        m--
      }
      if (o) {
        var n = this._daylightSavingAdjust(new Date(o.getFullYear(), o.getMonth() - i[0] * i[1] + 1, o.getDate()));
        for (n = j && n < j ? j : n; this._daylightSavingAdjust(new Date(m, g, 1)) > n;) {
          g--;
          if (g < 0) {
            g = 11;
            m--
          }
        }
      }
      a.drawMonth = g;
      a.drawYear = m;
      n = this._get(a,
        "prevText");
      n = !h ? n : this.formatDate(n, this._daylightSavingAdjust(new Date(m, g - k, 1)), this._getFormatConfig(a));
      n = this._canAdjustMonth(a, -1, m, g) ? '<a class="ui-datepicker-prev ui-corner-all" onclick="DP_jQuery_' + y + ".datepicker._adjustDate('#" + a.id + "', -" + k + ", 'M');\" title=\"" + n + '"><span class="ui-icon ui-icon-circle-triangle-' + (c ? "e" : "w") + '">' + n + "</span></a>" : f ? "" : '<a class="ui-datepicker-prev ui-corner-all ui-state-disabled" title="' + n + '"><span class="ui-icon ui-icon-circle-triangle-' + (c ? "e" : "w") + '">' +
        n + "</span></a>";
      var r = this._get(a, "nextText");
      r = !h ? r : this.formatDate(r, this._daylightSavingAdjust(new Date(m, g + k, 1)), this._getFormatConfig(a));
      f = this._canAdjustMonth(a, +1, m, g) ? '<a class="ui-datepicker-next ui-corner-all" onclick="DP_jQuery_' + y + ".datepicker._adjustDate('#" + a.id + "', +" + k + ", 'M');\" title=\"" + r + '"><span class="ui-icon ui-icon-circle-triangle-' + (c ? "w" : "e") + '">' + r + "</span></a>" : f ? "" : '<a class="ui-datepicker-next ui-corner-all ui-state-disabled" title="' + r + '"><span class="ui-icon ui-icon-circle-triangle-' +
        (c ? "w" : "e") + '">' + r + "</span></a>";
      k = this._get(a, "currentText");
      r = this._get(a, "gotoCurrent") && a.currentDay ? u : b;
      k = !h ? k : this.formatDate(k, r, this._getFormatConfig(a));
      h = !a.inline ? '<button type="button" class="ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all" onclick="DP_jQuery_' + y + '.datepicker._hideDatepicker();">' + this._get(a, "closeText") + "</button>" : "";
      e = e ? '<div class="ui-datepicker-buttonpane ui-widget-content">' + (c ? h : "") + (this._isInRange(a, r) ? '<button type="button" class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" onclick="DP_jQuery_' +
        y + ".datepicker._gotoToday('#" + a.id + "');\">" + k + "</button>" : "") + (c ? "" : h) + "</div>" : "";
      h = parseInt(this._get(a, "firstDay"), 10);
      h = isNaN(h) ? 0 : h;
      k = this._get(a, "showWeek");
      r = this._get(a, "dayNames");
      this._get(a, "dayNamesShort");
      var s = this._get(a, "dayNamesMin"), z = this._get(a, "monthNames"), v = this._get(a, "monthNamesShort"), p = this._get(a, "beforeShowDay"), w = this._get(a, "showOtherMonths"), H = this._get(a, "selectOtherMonths");
      this._get(a, "calculateWeek");
      for (var M = this._getDefaultDate(a), I = "", C = 0; C < i[0]; C++) {
        for (var N =
          "", D = 0; D < i[1]; D++) {
          var J = this._daylightSavingAdjust(new Date(m, g, a.selectedDay)), t = " ui-corner-all", x = "";
          if (l) {
            x += '<div class="ui-datepicker-group';
            if (i[1] > 1)switch (D) {
              case 0:
                x += " ui-datepicker-group-first";
                t = " ui-corner-" + (c ? "right" : "left");
                break;
              case i[1] - 1:
                x += " ui-datepicker-group-last";
                t = " ui-corner-" + (c ? "left" : "right");
                break;
              default:
                x += " ui-datepicker-group-middle";
                t = "";
                break
            }
            x += '">'
          }
          x += '<div class="ui-datepicker-header ui-widget-header ui-helper-clearfix' + t + '">' + (/all|left/.test(t) && C == 0 ? c ?
            f : n : "") + (/all|right/.test(t) && C == 0 ? c ? n : f : "") + this._generateMonthYearHeader(a, g, m, j, o, C > 0 || D > 0, z, v) + '</div><table class="ui-datepicker-calendar"><thead><tr>';
          var A = k ? '<th class="ui-datepicker-week-col">' + this._get(a, "weekHeader") + "</th>" : "";
          for (t = 0; t < 7; t++) {
            var q = (t + h) % 7;
            A += "<th" + ((t + h + 6) % 7 >= 5 ? ' class="ui-datepicker-week-end"' : "") + '><span title="' + r[q] + '">' + s[q] + "</span></th>"
          }
          x += A + "</tr></thead><tbody>";
          A = this._getDaysInMonth(m, g);
          if (m == a.selectedYear && g == a.selectedMonth)a.selectedDay = Math.min(a.selectedDay,
            A);
          t = (this._getFirstDayOfMonth(m, g) - h + 7) % 7;
          A = l ? 6 : Math.ceil((t + A) / 7);
          q = this._daylightSavingAdjust(new Date(m, g, 1 - t));
          for (var O = 0; O < A; O++) {
            x += "<tr>";
            var P = !k ? "" : '<td class="ui-datepicker-week-col">' + this._get(a, "calculateWeek")(q) + "</td>";
            for (t = 0; t < 7; t++) {
              var F = p ? p.apply(a.input ? a.input[0] : null, [q]) : [true, ""], B = q.getMonth() != g, K = B && !H || !F[0] || j && q < j || o && q > o;
              P += '<td class="' + ((t + h + 6) % 7 >= 5 ? " ui-datepicker-week-end" : "") + (B ? " ui-datepicker-other-month" : "") + (q.getTime() == J.getTime() && g == a.selectedMonth &&
                a._keyEvent || M.getTime() == q.getTime() && M.getTime() == J.getTime() ? " " + this._dayOverClass : "") + (K ? " " + this._unselectableClass + " ui-state-disabled" : "") + (B && !w ? "" : " " + F[1] + (q.getTime() == u.getTime() ? " " + this._currentClass : "") + (q.getTime() == b.getTime() ? " ui-datepicker-today" : "")) + '"' + ((!B || w) && F[2] ? ' title="' + F[2] + '"' : "") + (K ? "" : ' onclick="DP_jQuery_' + y + ".datepicker._selectDay('#" + a.id + "'," + q.getMonth() + "," + q.getFullYear() + ', this);return false;"') + ">" + (B && !w ? "&#xa0;" : K ? '<span class="ui-state-default">' + q.getDate() +
                "</span>" : '<a class="ui-state-default' + (q.getTime() == b.getTime() ? " ui-state-highlight" : "") + (q.getTime() == J.getTime() ? " ui-state-active" : "") + (B ? " ui-priority-secondary" : "") + '" href="#">' + q.getDate() + "</a>") + "</td>";
              q.setDate(q.getDate() + 1);
              q = this._daylightSavingAdjust(q)
            }
            x += P + "</tr>"
          }
          g++;
          if (g > 11) {
            g = 0;
            m++
          }
          x += "</tbody></table>" + (l ? "</div>" + (i[0] > 0 && D == i[1] - 1 ? '<div class="ui-datepicker-row-break"></div>' : "") : "");
          N += x
        }
        I += N
      }
      I += e + (d.browser.msie && parseInt(d.browser.version, 10) < 7 && !a.inline ? '<iframe src="javascript:false;" class="ui-datepicker-cover" frameborder="0"></iframe>' :
        "");
      a._keyEvent = false;
      return I
    }, _generateMonthYearHeader:function (a, b, c, e, f, h, i, g) {
      var k = this._get(a, "changeMonth"), l = this._get(a, "changeYear"), u = this._get(a, "showMonthAfterYear"), j = '<div class="ui-datepicker-title">', o = "";
      if (h || !k)o += '<span class="ui-datepicker-month">' + i[b] + "</span>"; else {
        i = e && e.getFullYear() == c;
        var m = f && f.getFullYear() == c;
        o += '<select class="ui-datepicker-month" onchange="DP_jQuery_' + y + ".datepicker._selectMonthYear('#" + a.id + "', this, 'M');\" onclick=\"DP_jQuery_" + y + ".datepicker._clickMonthYear('#" +
          a.id + "');\">";
        for (var n = 0; n < 12; n++)if ((!i || n >= e.getMonth()) && (!m || n <= f.getMonth()))o += '<option value="' + n + '"' + (n == b ? ' selected="selected"' : "") + ">" + g[n] + "</option>";
        o += "</select>"
      }
      u || (j += o + (h || !(k && l) ? "&#xa0;" : ""));
      if (h || !l)j += '<span class="ui-datepicker-year">' + c + "</span>"; else {
        g = this._get(a, "yearRange").split(":");
        var r = (new Date).getFullYear();
        i = function (s) {
          s = s.match(/c[+-].*/) ? c + parseInt(s.substring(1), 10) : s.match(/[+-].*/) ? r + parseInt(s, 10) : parseInt(s, 10);
          return isNaN(s) ? r : s
        };
        b = i(g[0]);
        g = Math.max(b,
          i(g[1] || ""));
        b = e ? Math.max(b, e.getFullYear()) : b;
        g = f ? Math.min(g, f.getFullYear()) : g;
        for (j += '<select class="ui-datepicker-year" onchange="DP_jQuery_' + y + ".datepicker._selectMonthYear('#" + a.id + "', this, 'Y');\" onclick=\"DP_jQuery_" + y + ".datepicker._clickMonthYear('#" + a.id + "');\">"; b <= g; b++)j += '<option value="' + b + '"' + (b == c ? ' selected="selected"' : "") + ">" + b + "</option>";
        j += "</select>"
      }
      j += this._get(a, "yearSuffix");
      if (u)j += (h || !(k && l) ? "&#xa0;" : "") + o;
      j += "</div>";
      return j
    }, _adjustInstDate:function (a, b, c) {
      var e =
        a.drawYear + (c == "Y" ? b : 0), f = a.drawMonth + (c == "M" ? b : 0);
      b = Math.min(a.selectedDay, this._getDaysInMonth(e, f)) + (c == "D" ? b : 0);
      e = this._restrictMinMax(a, this._daylightSavingAdjust(new Date(e, f, b)));
      a.selectedDay = e.getDate();
      a.drawMonth = a.selectedMonth = e.getMonth();
      a.drawYear = a.selectedYear = e.getFullYear();
      if (c == "M" || c == "Y")this._notifyChange(a)
    }, _restrictMinMax:function (a, b) {
      var c = this._getMinMaxDate(a, "min");
      a = this._getMinMaxDate(a, "max");
      b = c && b < c ? c : b;
      return b = a && b > a ? a : b
    }, _notifyChange:function (a) {
      var b = this._get(a,
        "onChangeMonthYear");
      if (b)b.apply(a.input ? a.input[0] : null, [a.selectedYear, a.selectedMonth + 1, a])
    }, _getNumberOfMonths:function (a) {
      a = this._get(a, "numberOfMonths");
      return a == null ? [1, 1] : typeof a == "number" ? [1, a] : a
    }, _getMinMaxDate:function (a, b) {
      return this._determineDate(a, this._get(a, b + "Date"), null)
    }, _getDaysInMonth:function (a, b) {
      return 32 - (new Date(a, b, 32)).getDate()
    }, _getFirstDayOfMonth:function (a, b) {
      return(new Date(a, b, 1)).getDay()
    }, _canAdjustMonth:function (a, b, c, e) {
      var f = this._getNumberOfMonths(a);
      c = this._daylightSavingAdjust(new Date(c, e + (b < 0 ? b : f[0] * f[1]), 1));
      b < 0 && c.setDate(this._getDaysInMonth(c.getFullYear(), c.getMonth()));
      return this._isInRange(a, c)
    }, _isInRange:function (a, b) {
      var c = this._getMinMaxDate(a, "min");
      a = this._getMinMaxDate(a, "max");
      return(!c || b.getTime() >= c.getTime()) && (!a || b.getTime() <= a.getTime())
    }, _getFormatConfig:function (a) {
      var b = this._get(a, "shortYearCutoff");
      b = typeof b != "string" ? b : (new Date).getFullYear() % 100 + parseInt(b, 10);
      return{shortYearCutoff:b, dayNamesShort:this._get(a,
        "dayNamesShort"), dayNames:this._get(a, "dayNames"), monthNamesShort:this._get(a, "monthNamesShort"), monthNames:this._get(a, "monthNames")}
    }, _formatDate:function (a, b, c, e) {
      if (!b) {
        a.currentDay = a.selectedDay;
        a.currentMonth = a.selectedMonth;
        a.currentYear = a.selectedYear
      }
      b = b ? typeof b == "object" ? b : this._daylightSavingAdjust(new Date(e, c, b)) : this._daylightSavingAdjust(new Date(a.currentYear, a.currentMonth, a.currentDay));
      return this.formatDate(this._get(a, "dateFormat"), b, this._getFormatConfig(a))
    }});
  d.fn.datepicker =
    function (a) {
      if (!d.datepicker.initialized) {
        d(document).mousedown(d.datepicker._checkExternalClick).find("body").append(d.datepicker.dpDiv);
        d.datepicker.initialized = true
      }
      var b = Array.prototype.slice.call(arguments, 1);
      if (typeof a == "string" && (a == "isDisabled" || a == "getDate" || a == "widget"))return d.datepicker["_" + a + "Datepicker"].apply(d.datepicker, [this[0]].concat(b));
      if (a == "option" && arguments.length == 2 && typeof arguments[1] == "string")return d.datepicker["_" + a + "Datepicker"].apply(d.datepicker, [this[0]].concat(b));
      return this.each(function () {
        typeof a == "string" ? d.datepicker["_" + a + "Datepicker"].apply(d.datepicker, [this].concat(b)) : d.datepicker._attachDatepicker(this, a)
      })
    };
  d.datepicker = new L;
  d.datepicker.initialized = false;
  d.datepicker.uuid = (new Date).getTime();
  d.datepicker.version = "1.8.5";
  window["DP_jQuery_" + y] = d
})(jQuery);
;

/*
 * jQuery Mobile Framework : temporary extension to port jQuery UI's datepicker for mobile
 * Copyright (c) jQuery Project
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 * todo datepicker for mobile
 */
(function ($, undefined) {

  //cache previous datepicker ui method
  var prevDp = $.fn.datepicker;

  //rewrite datepicker
  $.fn.datepicker = function (options) {

    var dp = this;

    //call cached datepicker plugin
    prevDp.call(this, options);

    //extend with some dom manipulation to update the markup for jQM
    //call immediately
    function updateDatepicker(event) {
      $(".ui-datepicker-header", dp).addClass("ui-body-c ui-corner-top").removeClass("ui-corner-all");
      $(".ui-datepicker-prev, .ui-datepicker-next", dp).attr("href", "#");
      $(".ui-datepicker-prev", dp).buttonMarkup({iconpos:"notext", icon:"arrow-l", shadow:true, corners:true});
      $(".ui-datepicker-next", dp).buttonMarkup({iconpos:"notext", icon:"arrow-r", shadow:true, corners:true});
      $(".ui-datepicker-calendar th", dp).addClass("ui-bar-c");
      $(".ui-datepicker-calendar td", dp).addClass("ui-body-c");
      $(".ui-datepicker-calendar a", dp).buttonMarkup({corners:false, shadow:false});
      $(".ui-datepicker-calendar a.ui-state-active", dp).addClass("ui-btn-active"); // selected date
      $(".ui-datepicker-calendar a.ui-state-highlight", dp).addClass("ui-btn-up-e"); // today"s date
      $(".ui-datepicker-calendar .ui-btn", dp).each(function () {
        var el = $(this);
        // remove extra button markup - necessary for date value to be interpreted correctly
        // todo Apptouch Mod {
        if (el.find(".ui-btn-text").text()) {
          el.html(el.find(".ui-btn-text").text());
        }
      });
      if (event && ($(event.target).closest('.ui-btn').length > 0 || $(event.target).hasClass('ui-btn')) && $(event.target).closest('td').length > 0) {
        dp.closest('.ui-dialog').dialog('close');
        setTimeout(function () {
          $(".component-form input[type='date'], input:jqmData(type='date')").change(); // todo shit code!
        }, 100);
      }
      ;
      // todo } Apptouch Mod

    }

    ;

    //update now
    updateDatepicker();

    // and on click
    $(dp).click(updateDatepicker);

    //return jqm obj
    return this;
  };
  function parseDPDate(datestr){
    var value = datestr.split('/');
    var df = core.locale.format('date').split('/');
    var dt = {};
    for(var i = 0; i < df.length; i ++){
      dt[df[i].toLowerCase()] = value[i];
    }
    return new Date([dt['mm'], dt['dd'], dt['yy']].join('/'));
  }

  //bind to pagecreate to automatically enhance date inputs
  $(".ui-page").live("pagecreate", function () {
    $("input[type='date'], input:jqmData(type='date')", this).each(function () {
      // todo Apptouch Mod {

      var $this = $(this);
      var oldVal = $this.val();
      var $dp = $("<div />").datepicker({ dateFormat:core.locale.format('date'), altField:"#" + $this.attr("id"), showOtherMonths:true });
      var dp = $dp.data('datepicker');
      if (oldVal) {
        $dp.datepicker('setDate', new Date(oldVal));
      }
      var dialog_id = 'apptouch_local_' + $dp.attr('id');
      var $dialog = UIComponent.layout.$createBase('dialog', {'id':dialog_id});
      var $button = $("<a />").text(oldVal ? parseDPDate(oldVal).toLocaleDateString() : parseDPDate($this.val()).toLocaleDateString()).attr({'href':'#' + dialog_id, 'data-icon':'calendar'}).button();

      $this.after($button);
      $this.bind('change', function () {
        $button.find('span.ui-btn-text').html(parseDPDate($(this).val()).toLocaleDateString());
      });
//      $dialog.children("div:jqmData(role='header')").remove();
      $dialog.children("div:jqmData(role='content')").append($("<div />").datepicker({dateFormat:core.locale.format('date'), altField:"#" + $this.attr("id"), showOtherMonths:true }));

      if (oldVal) {
        $this.val(oldVal);
      }
      // todo } Apptouch Mod
    });
  });
})(jQuery);

/**
 * Picup Widget
 */
(function($){
  $.widget("mobile.picup", $.mobile.widget, {
    /* Available options for the widget are specified here, along with default values. */
    options: {
      inline: false,
      mode: "default",
      height: 200
    },
    /* Mandatory method - automatically called by jQuery Mobile to initialise the widget. */
    _create: function() {
      var inputElement = this.element;
      var opts = $.extend(this.options, inputElement.data("options"));
      $(document).trigger("picupcreate");
     // ...
      inputElement.after("<button>" + inputElement.val() + "</button>");
     // ...
    },
    /* Custom method to handle updates. */
    _update: function() {
      var inputElement = this.element;
      var opts = $.extend(this.options, inputElement.data("options"));
      $(document).trigger("picupupdate");
      //...
      inputElement.siblings("button").text(inputElement.val());
      //...
    },
    /* Externally callable method to force a refresh of the widget. */
    refresh: function() {
      return this._update();
    }
  });
  /* Handler which initialises all widget instances during page creation. */
//  $(document).bind("pagecreate", function(e) {
//    $(document).trigger("picupbeforecreate");
//    return $(":jqmData(role='picup')", e.target).picup();
//  });
//  var stdMethod  = $.fn.picup;
//  $.fn.picup = function(method) {
//    stdMethod(arguments);
//// 		if (methods[method]) {
//// 			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
//// 		} else if (typeof method === 'object' || !method) {
//// 			return methods.init.apply(this, arguments);
//// 		}
// 	};

})(jQuery);