/**
 * @version     $Id$
 * @category	Nooku
 * @package     Nooku_Media
 * @subpackage  Javascript
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Koowa global namespace
.*
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Media
 * @subpackage  Javascript
 */
if(!Koowa) var Koowa = {};
Koowa.version = 0.7;

/* Section: Functions */
var $get = function(key, defaultValue) {
    return location.search.get(key, defaultValue);
}   

/* Section: onDomReady */
window.addEvent('domready', function() {
    $$('.submitable').addEvent('click', function(e){
        e = new Event(e);
        new Koowa.Form(Json.evaluate(e.target.getProperty('rel'))).submit();
    });

    $$('.-koowa-grid').each(function(grid){
        new Koowa.Grid(grid);
        
        var toolbar = grid.get('data-toolbar') ? grid.get('data-toolbar') : '.toolbar';
        new Koowa.Controller.Grid({form: grid, toolbar: document.getElement(toolbar)});
    });

    $$('.-koowa-form').each(function(form){
        var toolbar = form.get('data-toolbar') ? form.get('data-toolbar') : '.toolbar';
        new Koowa.Controller.Form({form: form, toolbar: document.getElement(toolbar)});
    });
});

/* Section: Classes */

/**
 * Creates a 'virtual form'
 * 
 * @param   json    Configuration:  method, url, params, formelem
 * @example new KForm({method:'post', url:'foo=bar&id=1', params:{field1:'val1', field2...}}).submit();
 */
Koowa.Form = new Class({
    
    initialize: function(config) {
        this.config = config;
        if(this.config.element) {
            this.form = $(document[this.config.element]);
        } 
        else {
            this.form = new Element('form', {
                name: 'dynamicform',
                method: this.config.method,
                action: this.config.url
            });
            this.form.injectInside($(document.body));
        }
    },
    
    addField: function(name, value) {
        var elem = new Element('input', {
            name: name,
            value: value,
            type: 'hidden'
        });
        elem.injectInside(this.form);
        return this;
    },
    
    submit: function() {
        $each(this.config.params, function(value, name){
            this.addField(name, value);
        }.bind(this));
        this.form.submit();
    }
});


/**
 * Grid class
 *
 * @package     Koowa_Media
 * @subpackage  Javascript
 */
Koowa.Grid = new Class({

    initialize: function(element){
        
        this.element    = $(element);
        this.form       = this.element.match('form') ? this.element : this.element.getParent('form');
        this.toggles    = this.element.getElements('.-koowa-grid-checkall');
        this.checkboxes = this.element.getElements('.-koowa-grid-checkbox');
        
        var self = this;
        this.toggles.addEvent('change', function(event){
            if(event) self.checkAll(this.get('checked'));
        });
        
        this.checkboxes.addEvent('change', function(event){
            if(event) self.uncheckAll();
        });
    },
    
    checkAll: function(value){

        var changed = this.checkboxes.filter(function(checkbox){
            return checkbox.get('checked') !== value;
        });

        this.checkboxes.set('checked', value);

        changed.fireEvent('change');

    },
    
    uncheckAll: function(){

        var total = this.checkboxes.filter(function(checkbox){
        	return checkbox.get('checked') !== false ;
        }).length;

        this.toggles.set('checked', this.checkboxes.length === total);
        this.toggles.fireEvent('change');

    }
});
/**
 * Find all selected checkboxes' ids in the grid
 *
 * @return  array   The items' ids
 */
Koowa.Grid.getAllSelected = function() {
        var result = new Array;
        var inputs = $$('input[class^=-koowa-grid-checkbox]');
        for (var i=0; i < inputs.length; i++) {
           if (inputs[i].checked) {
              result.include(inputs[i]);
           }
        }
        return result;
};
Koowa.Grid.getIdQuery = function() {
        var result = new Array();
        $each(this.getAllSelected(), function(selected){
            result.include(selected.name+'='+selected.value);
        });
        return result.join('&');
};



/**
 * Controller class, execute actions complete with command chains
 *
 * @package     Koowa_Media
 * @subpackage  Javascript
 */
Koowa.Controller = new Class({

    Implements: [Options, Events],
    
	form: null,
	toolbar: null,
	buttons: null,

	options: {
		toolbar: false,
		url: window.location.href
	},
	
	initialize: function(options){
		
		this.setOptions(options);
		
		this.form = this.options.form;
		this.toolbar = this.options.toolbar || this.form;

		this.form.store('controller', this);
		
		//Allows executing actions on the form element itself using fireEvent
		this.form.addEvent('execute', this.execute.bind(this));
		
		//Attach toolbar buttons actions
		this.buttons = this.toolbar.getElements('.toolbar').filter(function(button){
		    return button.get('data-action');
		});
		this.buttons.each(function(button){
		    var data = button.get('data-data'), action = button.get('data-action'), token_name = button.get('data-token-name');
		    data = data ? JSON.decode(data) : {};
		    
		    //Set token data
		    if(token_name) data[token_name] = button.get('data-token-value');
		    
		    button.addEvent('click', function(){
		        if(!button.hasClass('disabled')) this.fireEvent('execute', [action, data, button.get('data-novalidate') == 'novalidate']);
		    }.bind(this));
		    
		}, this);
    },
    
    execute: function(action, data, novalidate){
    	var method = '_action'+action.capitalize();
    	
		this.options.action = action;
		if(this.fireEvent('before.'+action, [data, novalidate])) {
		    this[method] ? this[method].call(this, data) : this._action_default.call(this, action, data, novalidate);
		    this.fireEvent('after.'+action, [data, novalidate])
		}
    	
    	return this;
    },
    
    addEvent: function(type, fn, internal){

        return this.form.addEvent.apply(this.form, [type, fn, internal]);
    
    },
    
    fireEvent: function(type, args, delay){
		var events = this.form.retrieve('events');
		if (!events || !events[type]) return this;
		var result = events[type].keys.map(function(fn){
			return fn.create({'bind': this, 'delay': delay, 'arguments': args})() !== false;
		}, this).every(function(v){ return v;});
		return result;
	},
	
	checkValidity: function(){
	    var buttons = this.buttons.filter(function(button){
	        return button.get('data-novalidate') != 'novalidate';
	    }, this);
	    
	    /* We use a class for this state instead of a data attribute because not all browsers supports attribute selectors */
	    if(this.fireEvent('validate')) {
	        buttons.removeClass('disabled');
	    } else {
	        buttons.addClass('disabled');
	    }
	}
});

/**
 * Controller class specialized for grids, extends Koowa.Controller
 *
 * @package     Koowa_Media
 * @subpackage  Javascript
 */
Koowa.Controller.Grid = new Class({

    Extends: Koowa.Controller,
    
    options: {
        inputs: '.-koowa-grid-checkbox'
    },
    
    initialize: function(options){
        
        this.parent(options);

        this.addEvent('validate', this.validate);
        
        //Perform grid validation and set the right classes on toolbar buttons
        if(this.options.inputs) {
            //This is to allow CSS3 transitions without those animating onload without user interaction
            this.buttons.addClass('beforeload');
            this.checkValidity();
            //Remove the class 1ms afterwards, which is enough for bypassing css transitions onload
            this.buttons.removeClass.delay(1, this.buttons, ['beforeload']);
            this.form.getElements(this.options.inputs).addEvent('change', this.checkValidity.bind(this));
        }
        
        //<select> elements in headers and footers are for filters, so they need to submit the form on change
        this.form.getElements('thead select, tfoot select').addEvent('change', this.form.submit.bind(this.form));
    },
    
    validate: function(){
        if(!Koowa.Grid.getIdQuery()) return false;
    },
    
    _action_default: function(action, data, novalidate){
        if(!novalidate && !this.fireEvent('validate')) return false;
    
        var options = {
            method:'post',
            url: this.options.url+'&'+Koowa.Grid.getIdQuery(),
            params: $merge({
                action: action
            }, data)
        };
    	new Koowa.Form(options).submit();
    }

});

/**
 * Controller class specialized for forms, extends Koowa.Controller
 *
 * @package     Koowa_Media
 * @subpackage  Javascript
 */
Koowa.Controller.Form = new Class({

    Extends: Koowa.Controller,
    
    options: {
        updateToolbarOnChanged: true
    },
    
    //Default values in the form, used as ajaxed form inputs may have a different default value than the input's defaultValue property
    defaults: {},
    
    initialize: function(options){
    
        this.parent(options);
        
        //Perform form validation and set the right classes on toolbar buttons
        if(this.options.updateToolbarOnChanged) {
        
            //Gather preset values when one of the elements get focus
            var buttons = this.buttons.filter(function(button){
                    return button.get('data-novalidate') != 'novalidate';
                }, this), 
                elements = $$(this.form.elements).filter('[name]'), 
                setDefaults = function(){
                    //Only run setDefaults once
                    elements.removeEvent('focus', setDefaults);

                    elements.each(function(element){
                        this.defaults[element.get('name')] = element.get('value');
                    }, this);

                    var self = this, changed = [];
                    elements.addEvent('change', function(){
                        var name = this.get('name'), value = this.get('value'), defaultValue = self.defaults[name];
                        
                        value !== defaultValue ? changed.include(this) : changed.erase(this);
                        changed.length ? buttons.removeClass('disabled') : buttons.addClass('disabled');
                    });
                    
                }.bind(this);
            elements.addEvent('focus', setDefaults);
            window.addEvent('load', setDefaults);
            
            //This is to allow CSS3 transitions without those animating onload without user interaction
            buttons.addClass('beforeload disabled')
                        //Remove the class 1ms afterwards, which is enough for bypassing css transitions onload
                        .removeClass.delay(1, this.buttons, ['beforeload']);
        }
    
    },
    
    _action_default: function(action, data, novalidate){
        if(!novalidate && !this.fireEvent('validate')) return false;
    
        this.form.adopt(new Element('input', {name: 'action', type: 'hidden', value: action}));
        this.form.submit();
    }

});

/**
 * Query class
 *
 * @package     Koowa_Media
 * @subpackage  Javascript
 */
Koowa.Query = new Class({
    
    toString: function() {
        var result = [];
        
        for (var key in this) {
            // make sure it's not a function
            if (!(this[key] instanceof Function)) {
                // we only go one level deep for now
                if(this[key] instanceof Object) {
                    for (var subkey in this[key]) {
                        result.push(key + '[' + subkey + ']' + '=' + this[key][subkey]);
                    }
                } else {
                    result.push(key + '=' + this[key]);
                }
            }
        }
        
        return result.join('&');
    }
});


/**
 * Overlay class
 *
 * @package     Koowa_Media
 * @subpackage  Javascript
 */
Koowa.Overlay = new Class({
	Extends: Request,
    element : null,
    
    options: {
        method      : 'get',
        evalScripts : true,
        evalStyles  : true,
        
        onComplete: function() {
            var element = new Element('div', {html: this.response.text});
            element.getElement('[id='+this.element.id+']').replaces(this.element);
            if (this.options.evalScripts) {
                scripts = element.getElementsBySelector('script[type=text/javascript]');
                scripts.each(function(script) {
                    new Asset.javascript(script.src, {id: script.id });
                    script.remove();
                }.bind(this))
            }
            
            if (this.options.evalStyles) {
                styles  = element.getElementsBySelector('link[type=text/css]');
                styles.each(function(style) {
                    new Asset.css(style.href, {id: style.id });
                    style.remove();
                }.bind(this))
            }
        }
    },
    
    initialize: function(element, options) {
        if(typeof options == 'string') {
            var options = Json.evaluate(options);
        }
        
        this.element = $(element); 
        this.options.url = element.getAttribute('href'); 
        this.parent(options);
        
        this.send();
    }
});


/**
 * String class
 *
 * @package     Koowa_Media
 * @subpackage  Javascript
 */
String.extend({
    get : function(key, defaultValue) {
        if(key == "") return;
    
        var uri   = this.parseUri();
        if($defined(uri['query'])) {
            var query = uri['query'].parseQueryString();
            if($defined(query[key])) {
                return query[key];
            }
        }
        
        return defaultValue;
    },

    parseUri: function() {
        var bits = this.match(/^(?:([^:\/?#.]+):)?(?:\/\/)?(([^:\/?#]*)(?::(\d*))?)((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[\?#]|$)))*\/?)?([^?#\/]*))?(?:\?([^#]*))?(?:#(.*))?/);
        return (bits)
            ? bits.associate(['uri', 'scheme', 'authority', 'domain', 'port', 'path', 'directory', 'file', 'query', 'fragment'])
            : null;
    }
});