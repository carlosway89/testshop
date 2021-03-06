/*
  spsrsmart_core_1_0_0.js 2015-04-24 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
*/

/** Simple JavaScript Inheritance
 * By John Resig http://ejohn.org/
 * MIT Licensed.
 */
// Inspired by base2 and Prototype
( function(){
  var initializing = false, fnTest = /xyz/.test(function(){xyz;}) ? /\b_super\b/ : /.*/;

  // The base RSmartClass implementation (does nothing)
  //this.RSmartClass = function(){};
  RSmartClass = function(){};
 
  // Create a new RSmartClass that inherits from this class
  RSmartClass.extend = function(prop) {
    var _super = this.prototype;
   
    // Instantiate a base class (but only create the instance,
    // don't run the init constructor)
    initializing = true;
    var prototype = new this();
    initializing = false;
   
    // Copy the properties over onto the new prototype
    for (var name in prop) {
      // Check if we're overwriting an existing function
      prototype[name] = typeof prop[name] == "function" &&
        typeof _super[name] == "function" && fnTest.test(prop[name]) ?
        (function(name, fn){
          return function() {
            var tmp = this._super;
           
            // Add a new ._super() method that is the same method
            // but on the super-class
            this._super = _super[name];
           
            // The method only need to be bound temporarily, so we
            // remove it when we're done executing
            var ret = fn.apply(this, arguments);       
            this._super = tmp;
           
            return ret;
          };
        })(name, prop[name]) :
        prop[name];
    }
   
    // The dummy class constructor
    function RSmartClass() {
      // All construction is actually done in the init method
      if ( !initializing && this.init )
        this.init.apply(this, arguments);
    }
   
    // Populate our constructed prototype object
    RSmartClass.prototype = prototype;
   
    // Enforce the constructor to be what we expect
    RSmartClass.prototype.constructor = RSmartClass;

    // And make this class extendable
    RSmartClass.extend = arguments.callee;
   
    return RSmartClass;
  };
})();

// ============================================================================

/** GUID Generator, je nach Browser mit Crypro-Bibliothek oder nicht.
 * by stackoverflow.com
 * Wird zur Methode der Ur-Klasse.
 */
RSmartClass.prototype.generateGUID = (typeof(window.crypto) != 'undefined' && 
                typeof(window.crypto.getRandomValues) != 'undefined') ?
    function() {
        // If we have a cryptographically secure PRNG, use that
        // http://stackoverflow.com/questions/6906916/collisions-when-generating-uuids-in-javascript
        var buf = new Uint16Array(8);
        window.crypto.getRandomValues(buf);
        var S4 = function(num) {
            var ret = num.toString(16);
            while(ret.length < 4){
                ret = "0"+ret;
            }
            return ret;
        };
        return (S4(buf[0])+S4(buf[1])+"-"+S4(buf[2])+"-"+S4(buf[3])+"-"+S4(buf[4])+"-"+S4(buf[5])+S4(buf[6])+S4(buf[7]));
    }

    :

    function() {
        // Otherwise, just use Math.random
        // http://stackoverflow.com/questions/105034/how-to-create-a-guid-uuid-in-javascript/2117523#2117523
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
            return v.toString(16);
        });
    };

// ============================================================================

/** Checks if an argument is defined.
 *
 * @param argument
 *    The argument to check
 *    
 * @return
 *    true if argument is defined, false otherwise
 */
RSmartClass.prototype.isDefined = function(argument) {
    var argtype = typeof(argument);
    if(argtype == 'undefined')
        return false;
    else
        return true;
}; // End isDefined

/** Checks if an argument is an object.
 *
 * @param argument
 *    The argument to check
 *    
 * @return
 *    true if argument is an object, false otherwise
 */
RSmartClass.prototype.isObject = function(argument) {
    var argtype = typeof(argument);
    if(argtype == 'object') {
        if(argument != null)
            return true;
        else
            return false;
    }
    else
        return false;
}; // End isObject

/** Checks if an argument is a jQuery object.
 *
 * @param argument
 *    The argument to check
 *    
 * @return
 *    true if argument is a jQuery object, false otherwise
 */
RSmartClass.prototype.isJQueryObject = function(argument) {
    if(!this.isObject(argument))
        return false;
    else {
        if(argument.selector)
            return true;
        else
            return false;
    }
};


/** Checks if an argument is a function.
 *
 * @param argument
 *    The argument to check
 *    
 * @return
 *    true if argument is a function, false otherwise
 */
RSmartClass.prototype.isFunction = function(argument) {
    var argtype = typeof(argument);
    if(argtype == 'function')
        return true;
    else
        return false;
}; // End isFunction

/** Checks if an argument is a boolean.
 *
 * @param argument
 *    The argument to check
 *    
 * @return
 *    true if argument is a boolean, false otherwise
 */
RSmartClass.prototype.isBoolean = function(argument) {
    var argtype = typeof(argument);
    if(argtype == 'boolean')
        return true;
    else
        return false;
}; // End isBoolean

/** Checks if an argument is a string.
 *
 * @param argument
 *    The argument to check
 *    
 * @return
 *    true if argument is a string, false otherwise
 */
RSmartClass.prototype.isString = function(argument) {
    var argtype = typeof(argument);
    if(argtype == 'string')
        return true;
    else
        return false;
}; // End isString

/** Checks if an argument is a number.
 *
 * @param argument
 *    The argument to check
 *    
 * @return
 *    true if argument is a number, false otherwise
 */
RSmartClass.prototype.isNumber = function(argument) {
    var argtype = typeof(argument);
    if(argtype == 'number')
        return true;
    else
        return false;
}; // End isNumber

/** Checks if an argument is numeric.
 *  This can be either a number or a string containing a number
 *
 * @param argument
 *    The argument to check
 *    
 * @return
 *    true if argument is numeric, false otherwise
 */
RSmartClass.prototype.isNumeric = function(argument) {
    return !isNaN( parseFloat(argument) ) && isFinite( argument );
}; // End isNumeric

/** Checks if an argument is an array.
 *
 * @param argument
 *    The argument to check
 *    
 * @return
 *    true if argument is an array, false otherwise
 */
RSmartClass.prototype.isArray = function(argument) {
    return argument instanceof Array ? true : false;
}; // End isArray

// jQuery.noConflict(); is needed for WebSites that use Scriptaculous or Prototype JavaScript Libraries,
// because they also use the $ Variable. Gambio for example is using Scriptaculous.
// See also (http://api.jquery.com/jQuery.noConflict/)
if(window.jQuery) {
    jQuery.noConflict();
}

// Declare the used class namespaces
window.spsrsmart = window.spsrsmart || {};
spsrsmart.rsmartcore = spsrsmart.rsmartcore || {};
spsrsmart.rsmartcore.AppConfig = spsrsmart.rsmartcore.AppConfig || {};

/**
 * Observer Implementation
 */
spsrsmart.rsmartcore.Observer = RSmartClass.extend({
    
    init: function() {
        this.observers = [];
        this.listeners = [];
    }, // End init
    
    /**
     * Adds an observer.
     * The callback function must have the signature function(scope, data),
     * where the scope is the object that extends this class and data
     * is an arbitrary object. 
     * 
     * @param object obj
     *    The object that adds the observer
     * 
     * @param string callback
     *    The name of a callback function of obj
     */
    addObserver: function(obj, callback) {
        // Check, if function exists
        if(typeof (obj[callback] ) !== 'function') {
            throw ("Function don't exist" );
        }
        // OK
        this.observers.push({
            obj: obj,
            callback: callback
        });
    }, // End addObserver
    
    /**
     * Removes an observer.
     * 
     * @param object obj
     *    The object that removes the observer
     *    
     * @param string callback
     *    The name of a callback function of obj
     */
    removeObserver: function(obj, callback) {
        this.observers = this.observers.filter(function(el) {
            if (el.obj !== obj && el.callback != callback) {
                return el;
            }
         });
    }, // End removeObserver
    
    /**
     * Adds a listener. 
     * The listener function must have the signature function(scope, data),
     * where the scope is the object that extends this class and data
     * is an arbitrary object.
     * 
     * @param function func
     *    A function. This can also be an anonymous function
     */
    addListener: function(func) {
        var argtype = typeof(func);
        if(argtype == 'function') {
            this.listeners.push({
                func: func
            });
        }
    }, // End addListener
    
    /**
     * Removes a listener.
     * 
     * @param function func
     *    A function. This can also be an anonymous function
     */
    removeListener: function(func) {
        var argtype = typeof(func);
        if(argtype == 'function') {
            this.listeners = this.listeners.filter(function(el) {
                if(el.func !== func) {
                    return el;
                }
            });
        }
    }, // End removeListener
    
    /**
     * Notifies all registered observers and listeners.
     * 
     * @param data
     *    The notification data
     */
    notify: function(data) {
        var scope = this;
        for (var i = 0; i < this.observers.length; i++) {
            var el = this.observers[i];
            // The first argument is obj. This ensures that
            // within the callback method 'this' is the reference to
            // the object that added the observer.
            // scope is the reference of the observer
            el.obj[el.callback].call(el.obj, scope, data);
        }
        
        for (var i = 0; i < this.listeners.length; i++) {
            var el = this.listeners[i];
            el.func.call(scope, data);
        }
    } // End notify
    
});
// End class spsrsmart.rsmartcore.Observer

/**
 * DynamicHtmlClient
 */
spsrsmart.rsmartcore.DynamicHtmlClient = spsrsmart.rsmartcore.Observer.extend({
    
    /**
     * Constructs a new spsrsmart.rsmartcore.DynamicHtmlClient
     */
    init: function() {
        // Call the constructor of spsrsmart.Observer
        this._super();
        
        this.dynamicHtmlId = "";
        this.dynamicHtmlParent = null;
        this.dynamicHtmlCode = "";
    }, // End constructor
    
    dynamicHtmlSetParent: function(jqueryObject) {
        var parent = null;
        if(this.isJQueryObject(jqueryObject)) {
            parent = jqueryObject;
        }
        
        if(parent != null) {
            this.dynamicHtmlParent = parent;
            this.dynamicHtmlId = this.generateGUID();
            var htmlCode = this.dynamicHtmlCode.replace(/#mwmucid/g, this.dynamicHtmlId);
            this.dynamicHtmlParent.append(htmlCode);
        }
        else {
            if(this.dynamicHtmlParent != null) {
                this.dynamicHtmlRemoveId(this.dynamicHtmlId);
            }
            this.dynamicHtmlParent = null;
            this.dynamicHtmlId = "";
        }
    }, // End dynamicHtmlSetParent
    
    dynamicHtmlRemoveId: function(id) {
        var idToRemove = "";
        if(this.isString(id)) {
            idToRemove = id;
        }
        
        if(idToRemove != "") {
            idToRemove = "#" + idToRemove;
            jQuery(idToRemove).remove();
        }
    }, // End dynamicHtmlRemoveId
    
    dynamicHtmlGetParent: function() {
        return this.dynamicHtmlParent;
    }, // End dynamicHtmlGetParent
    
    dynamicHtmlGetId: function() {
        return this.dynamicHtmlId;
    }, // End dynamicHtmlGetId
    
    dynamicHtmlGetJQueryId: function(suffix) {
        var idSuffix = "";
        if(this.isString(suffix)) {
            idSuffix = suffix;
        }
        var result = "";
        if(idSuffix != "")
            result = "#" + this.dynamicHtmlId + idSuffix;
        else
            result = "#" + this.dynamicHtmlId;
        return result;
    }, // End dynamicHtmlGetJQueryId
    
    dynamicHtmlSetHtmlCode: function(htmlcode) {
        if(this.isString(htmlcode)) {
            this.dynamicHtmlCode = htmlcode;
        }
    }, // End dynamicHtmlSetHtmlCode
    
    dynamicHtmlGetHtmlCode: function() {
        return this.dynamicHtmlCode;
    } // End dynamicHtmlGetHtmlCode
    
});
// End class spsrsmart.rsmartcore.DynamicHtmlClient

/**
 * GlobalObject
 */
spsrsmart.rsmartcore.GlobalObject = spsrsmart.rsmartcore.Observer.extend({
    
    init: function() {
        // Call the constructor of spsrsmart.rsmartcore.Observer
        this._super();
        
        this.mainAPP = null;
    }, // End constructor
    
    setMainApp: function(app) {
        if(this.isObject(app)) {
            this.mainAPP = app;
        }
    }, // End setMainApp
    
    getMainApp: function() {
        return this.mainAPP;
    } // End getMainApp
    
});
// End class spsrsmart.rsmartcore.GlobalObject

window.SPSRSMARTGLOBALOBJECT = new spsrsmart.rsmartcore.GlobalObject();

/**
 * Draggable
 * ---------
 * Draggable support for logging window
 */
spsrsmart.rsmartcore.Draggable = spsrsmart.rsmartcore.Observer.extend({
    
    init: function(options) {
        // Call the constructor of spsrsmart.rsmartcore.Observer
        this._super();
        
        this.draggableid = "";
        this.draggableElement = null;
        this.oldX = null;
        this.oldY = null;
        this.mouseInElement = false;
        this.dragging = false;
        
        this.setOptions(options);
        if(this.draggableid != "") {
            this.run();
        }
    }, // End constructor
    
    setOptions: function(options) {
        if(this.isObject(options)) {
            if(this.isString(options.draggableid)) {
                this.draggableid = options.draggableid;
            }
        }
    }, // End setOptions
    
    run: function() {
        var that = this;
        
        this.draggableElement = jQuery("#" + this.draggableid);
        
        this.draggableElement.mouseenter(function(evt) {
            jQuery(this).css({"cursor": "move"});
            that.mouseInElement = true;
        });
        this.draggableElement.mouseleave(function(evt) {
            jQuery(this).css({"cursor": "default"});
            that.mouseInElement = false;
        });
        
        
        jQuery(document).mousedown(function(evt) {
            //evt.preventDefault();
            // MWMUC-23.01.2015
            if(that.mouseInElement == true) {
               that.dragging = true;
            }
            // MWMUC-23.01.2015  that.dragging = true;
        })
        
        // this.draggableElement.mouseup
        jQuery(document).mouseup(function(evt) {
            //evt.preventDefault();
            that.dragging = false;
            that.oldX = null;
            that.oldY = null;
        });
        
        jQuery(document).mousemove(function(evt) {
            //evt.preventDefault();
            var x = evt.clientX;
            var y = evt.clientY;
            if(that.dragging == true) {
                if(that.oldX == null || that.oldY == null) {
                    that.oldX = x;
                    that.oldY = y;
                }
                else {
                    var diffx = x - that.oldX;
                    var diffy = y - that.oldY;
                    that.oldX = x;
                    that.oldY = y;
                    
                    // MWMUC-23.01.2015: Commented out, so that move events are also
                    // sent, when the cursor is temporary not in the emelent                    
                    // if(that.mouseInElement == true) {
                        var notifyOptions = {
                            action: "elementDragged",
                            diffx: diffx,
                            diffy: diffy,
                            mouseInElement: that.mouseInElement
                        };
                        that.notify(notifyOptions);
                    //} // end: if(that.mouseInElement == true)
                }
            }
        });
    } // End run
    
});
// End class spsrsmart.rsmartcore.Draggable

/**
 * DynamicLoggingWindowDraggable
 * -----------------------------
 * The draggable logging window
 */
spsrsmart.rsmartcore.DynamicLoggingWindowDraggable = spsrsmart.rsmartcore.DynamicHtmlClient.extend({
    
    init: function(options) {
        // Call the constructor of spsrsmart.rsmartcore.DynamicHtmlClient
        this._super();
        
        // Variables
        this.OPTIONS = null;
        this.refWindow = null;
        this.refWindowTitle = null;
        this.refWindowContent = null;
        this.refButtonClear = null;
        this.refButtonHide = null;
        
        this.loggingWindowTop = 3;
        this.loggingWindowLeft = 3;        
        this.loggingWindowWidth = 400;
        this.loggingWindowHeight = 400;
        this.loggingContentWidth = this.loggingWindowWidth - 10; // 390;
        this.loggingContentHeight = this.loggingWindowHeight - 25; // 375;
        this.entityClear = "&#8855;"; // "&#8801;"; //"&#8709;";
        this.entityArrowDown = "&#8659;";
        this.entityArrowRight = "&#8658;";
        
        var markup               = "<div id='#mwmucid' class='mwmuc-loggingwindow mwmuc-loggingwindow-visible' >" +
                                   "  <div id='#mwmucid-title' class='mwmuc-loggingwindow-title' >" +
                                   "    <span id='#mwmucid-titletext' class='mwmuc-loggingwindow-titletext' >" + "Logging Window" + "</span>" +
                                   "    <span id='#mwmucid-titlebuttons' class='mwmuc-loggingwindow-titlebuttons'  >" +
                                   "       <span id='#mwmucid-clearbutton' class='mwmuc-loggingwindow-titlebuttons-clearbutton' title='Clear'>" + this.entityClear + "</span>" +
                                   "       <span id='#mwmucid-hidebutton' class='mwmuc-loggingwindow-titlebuttons-hidebutton' title='Hide'>" + this.entityArrowDown + "</span>" +
                                   "    </span>" +
                                   "  </div>" +
                                   "  <div id='#mwmucid-content' class='mwmuc-loggingwindow-content' ></div>" +
                                   "</div>";
        this.dynamicHtmlSetHtmlCode(markup);
        
        this.windowWidth = jQuery(window).width();
        this.windowHeight = jQuery(window).height();
        
        this.visible = false;
        this.initiallyHidden = false;
        
        this.draggableClass = null;
        
        this.setOptions(options);
        
        this.createLoggingWindow();
    }, // End constructor
    
    setOptions: function(options) {
        if(this.isObject(options)) {
            this.OPTIONS = options;
            
            if(this.isNumber(this.OPTIONS.width)) {
                if(this.OPTIONS.width > 50) { 
                    this.loggingWindowWidth = this.OPTIONS.width;
                    this.loggingContentWidth = this.loggingWindowWidth - 10; 
                }
            }
            
            if(this.isNumber(this.OPTIONS.height)) {
                if(this.OPTIONS.height > 50) {
                    this.loggingWindowHeight = this.OPTIONS.height;
                    this.loggingContentHeight = this.loggingWindowHeight - 25;
                }
            }
            
            if(this.isNumber(this.OPTIONS.top)) {
                this.loggingWindowTop = this.OPTIONS.top;
            }
            
            if(this.isNumber(this.OPTIONS.left)) {
                this.loggingWindowLeft = this.OPTIONS.left;
            }
            
            if(this.isString(this.OPTIONS.mode)) {
                if(this.OPTIONS.mode == "hidden") {
                    this.initiallyHidden = true;
                }
            }
        }
        
        
        if(this.windowWidth < this.loggingWindowWidth) {
            this.loggingWindowWidth = this.windowWidth - 20;
            this.loggingContentWidth = this.loggingWindowWidth - 10;
        }
        if(this.windowHeight < this.loggingWindowHeight) {
            this.loggingWindowHeight = this.windowHeight - 20;
            this.loggingContentHeight = this.loggingWindowHeight - 25;
        }        
    }, // End setOptions

    getOptions: function() {
        return this.OPTIONS;
    }, // End getOptions

    logString: function(str) {
        if(this.isString(str)) {
            if(this.refWindow == null) {
                this.createLoggingWindow();
            }
            
            if(this.refWindowContent != null) {
                var html = this.refWindowContent.html();
                html = html + str + "<br/>";
                this.refWindowContent.html(html);
            }            
        }
    }, // End logString
    
    clearContent: function() {
        if(this.refWindowContent != null) {
            this.refWindowContent.html("");
        }
    }, // End clearContent

    hideWindow: function() {
        if(this.visible == true) {
            if(this.refWindow != null) {
                var pos = 0 - this.loggingWindowWidth + 25;
                this.refWindow.css({
                    left: pos
                });
                this.refWindowContent.hide();
                this.refButtonHide.attr("title", "Show Logging Window");
                this.refButtonHide.html(this.entityArrowRight);
                
                // MWMUC-23.01.2015
                this.refWindow.removeClass("mwmuc-loggingwindow-visible");
                this.refWindow.addClass("mwmuc-loggingwindow-invisible");                
            }
            this.visible = false;
        }
    }, // End hideWindow

    showWindow: function() {
        if(this.visible == false) {
            if(this.refWindow != null) {
                this.refWindow.css({
                    left: this.loggingWindowLeft,
                    height: this.loggingWindowHeight
                });
                this.refWindowContent.show();
                this.refButtonHide.attr("title", "Hide");
                this.refButtonHide.html(this.entityArrowDown);  
                
                // MWMUC-23.01.2015
                this.refWindow.removeClass("mwmuc-loggingwindow-invisible");
                this.refWindow.addClass("mwmuc-loggingwindow-visible");                
            }
            this.visible = true;
        }
    }, // End showWindow

    createLoggingWindow: function() {
        var that = this;
        if(this.refWindow != null) {
            // Window is already created
            return;
        }
        
        this.dynamicHtmlSetParent(jQuery("body"));
        
        this.refWindow = jQuery(this.dynamicHtmlGetJQueryId());
        this.refWindow.css({
            position: "fixed",
            top: this.loggingWindowTop,
            left: this.loggingWindowLeft,
            width: this.loggingWindowWidth,
            height: this.loggingWindowHeight,
            "z-index": 20000,
            "pointer-events": "auto" // This catches events here in the overlay and do not let go them through to the underlayed components
        });
        
        this.refWindowTitle = jQuery(this.dynamicHtmlGetJQueryId("-title"));        
        this.refWindowContent = jQuery(this.dynamicHtmlGetJQueryId("-content"));
        this.refWindowContent.css({
            "overflow-x": "auto",
            "overflow-y": "auto",
            "white-space": "nowrap",
            width: this.loggingContentWidth,
            height: this.loggingContentHeight - 20 // 10
        });
                
        this.refButtonClear = jQuery(this.dynamicHtmlGetJQueryId("-clearbutton"));
        this.refButtonClear.bind("click", function(evt) {
            evt.preventDefault();
            that.clearContent();
        });
        
        this.refButtonHide = jQuery(this.dynamicHtmlGetJQueryId("-hidebutton"));
        this.refButtonHide.bind("click", function(evt) {
            evt.preventDefault();
            if(that.visible == true)
                that.hideWindow();
            else
                that.showWindow();
        });
        
        this.visible = true;
        
        if(this.initiallyHidden == true) {
            this.hideWindow();
        }
        
        // Exists spsrsmart.rsmartcore.Draggable ?
        if(spsrsmart.rsmartcore.Draggable) {
            var draggableOptions = {
                draggableid: this.dynamicHtmlGetId() + "-title"
            };
            this.draggableClass = new spsrsmart.rsmartcore.Draggable(draggableOptions);
            this.draggableClass.addObserver(this, "dragHandler");
        }
    }, // End createLoggingWindow

    dragHandler: function(draggable, data) {
        if(this.isObject(data)) {
            //this.logString("dragHandler: " + JSON.stringify(data));
            
            if(data.action == "elementDragged") {
                this.loggingWindowTop = this.loggingWindowTop + data.diffy;
                // MWMUC-26.01.2015: Move the window up maximum to the top  
                if(this.loggingWindowTop < 0) {
                    this.loggingWindowTop = 0;
                }
                // MWMUC-26.01.2015: Move the window down maximum to window height minus 50
                if(this.loggingWindowTop > (this.windowHeight - 50) ) {
                    this.loggingWindowTop = this.windowHeight - 50;
                }                
                this.loggingWindowLeft = this.loggingWindowLeft + data.diffx;
                this.refWindow.css({
                    top: this.loggingWindowTop,
                    left: this.loggingWindowLeft
                });
            }
        }
    } // End dragHandler
    
});
// End class spsrsmart.rsmartcore.DynamicLoggingWindowDraggable
