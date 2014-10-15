(function(/*! Brunch !*/) {
  'use strict';

  var globals = typeof window !== 'undefined' ? window : global;
  if (typeof globals.require === 'function') return;

  var modules = {};
  var cache = {};

  var has = function(object, name) {
    return ({}).hasOwnProperty.call(object, name);
  };

  var expand = function(root, name) {
    var results = [], parts, part;
    if (/^\.\.?(\/|$)/.test(name)) {
      parts = [root, name].join('/').split('/');
    } else {
      parts = name.split('/');
    }
    for (var i = 0, length = parts.length; i < length; i++) {
      part = parts[i];
      if (part === '..') {
        results.pop();
      } else if (part !== '.' && part !== '') {
        results.push(part);
      }
    }
    return results.join('/');
  };

  var dirname = function(path) {
    return path.split('/').slice(0, -1).join('/');
  };

  var localRequire = function(path) {
    return function(name) {
      var dir = dirname(path);
      var absolute = expand(dir, name);
      return globals.require(absolute, path);
    };
  };

  var initModule = function(name, definition) {
    var module = {id: name, exports: {}};
    cache[name] = module;
    definition(module.exports, localRequire(name), module);
    return module.exports;
  };

  var require = function(name, loaderPath) {
    var path = expand(name, '.');
    if (loaderPath == null) loaderPath = '/';

    if (has(cache, path)) return cache[path].exports;
    if (has(modules, path)) return initModule(path, modules[path]);

    var dirIndex = expand(path, './index');
    if (has(cache, dirIndex)) return cache[dirIndex].exports;
    if (has(modules, dirIndex)) return initModule(dirIndex, modules[dirIndex]);

    throw new Error('Cannot find module "' + name + '" from '+ '"' + loaderPath + '"');
  };

  var define = function(bundle, fn) {
    if (typeof bundle === 'object') {
      for (var key in bundle) {
        if (has(bundle, key)) {
          modules[key] = bundle[key];
        }
      }
    } else {
      modules[bundle] = fn;
    }
  };

  var list = function() {
    var result = [];
    for (var item in modules) {
      if (has(modules, item)) {
        result.push(item);
      }
    }
    return result;
  };

  globals.require = require;
  globals.require.define = define;
  globals.require.register = define;
  globals.require.list = list;
  globals.require.brunch = true;
})();
require.register("config/app", function(exports, require, module) {
'use strict';

var config = {
    LOG_TRANSITIONS: true,
    LOG_TRANSITIONS_INTERNAL: false,
    apiMode: 'system' // seo | system
  };

module.exports = Ember.Application.create(config);

});

require.register("config/env", function(exports, require, module) {
'use strict';

module.exports = (function() {
  var envObject = {};
  var moduleNames = window.require.list().filter(function(module) {
    return new RegExp('^envs/').test(module);
  });

  moduleNames.forEach(function(module) {
    var key = module.split('/').reverse()[0];
    envObject[key] = require(module);
  });

  return envObject;
}());

});

require.register("config/router", function(exports, require, module) {
'use strict';

module.exports = App.Router.map(function() {
    // this.resource('about');
    this.route('index', {path: '/'});
    this.resource('articles', function(){

        this.resource('articles.index', {path: '/'});

        this.resource('blog', function(){
            this.resource('blog.index', {path: '/'});
            this.resource('blog.article', {path: '/:slug'});
        });

        this.resource('table', function(){
            this.resource('table.index', {path: '/'});
            this.resource('table.article', {path: '/:slug'});
        });
        
    });
    this.resource('files', function(){
        this.route('file', {path: ':slug'});
    });
});

module.exports = App.Router.reopen({
  location: 'hash'
});
});

require.register("config/store", function(exports, require, module) {
'use strict';

module.exports = App.ApplicationStore = DS.Store.extend();


module.exports = App.ApplicationAdapter = DS.RESTAdapter.extend({

    primaryKey: 'id',

    /**
    http://emberjs.com/api/data/classes/DS.RESTAdapter.html#method_buildURL
    */
    buildURL: function(type, id, record) {

        var url = [],
            host = Ember.get(this, 'host'),
            prefix = this.urlPrefix();

        if (App.get('apiMode') == 'seo') {


            if (type) { url.push(this.pathForType(type)); }

            //We might get passed in an array of ids from findMany
            //in which case we don't want to modify the url, as the
            //ids will be passed in through a query param
            if (id && !Ember.isArray(id)) { url.push(id); }

            if (prefix) { url.unshift(prefix); }

            url = url.join('/');

            url = url + '?format=json'
        }

        if (App.get('apiMode') == 'system') {

            url.push('format=json');

            if (type) { 
                url.push('component='+this.pathForType(type));
                url.push('view='+this.pathForType(type));
            }

            if (id && !Ember.isArray(id)) { url.push(this.get('primaryKey') +'='+id); }

            // Not sure about this, can it be left out?
            // if (prefix) { url.unshift(prefix); }

            url = '?' + url.join('&');
        }

        if (!host && url) { url = '/' + url; }
        
        console.log(url);

        return url;
    },
});

module.exports = App.ApplicationSerializer = DS.RESTSerializer.extend({

  primaryKey: 'id',

  entityNamespace: function(type){
    return ( (type + '').split('.')[1] ).toLowerCase();
  },
  entitiesNamespace: function(type){
    return Ember.Inflector.inflector.pluralize( (type + '').split('.')[1] ).toLowerCase();
  },
  extractArray: function(store, type, payload) {

    var entities = this.entitiesNamespace(type);

    var content = {};
    content[entities] = payload.entities;

    return this._super(store, type, content);
  },
  extractSingle: function(store, type, payload, id) {

    var entities = this.entityNamespace(type);

    var content = {};
    content[entities] = payload.entities;

    return this._super(store, type, payload);
  },
  normalize: function(type, hash) {

    delete hash.version;
    delete hash.links;
    delete hash.meta;

    if(this.get('primaryKey') != 'id') {
      hash.id = hash[this.get('primaryKey')];
      delete hash[this.get('primaryKey')];
    }

    return this._super(type, hash);
  }
});
});

require.register("controllers/ApplicationController", function(exports, require, module) {
'use strict';

module.exports = App.ApplicationController = Ember.Controller.extend({
  pageTitle: 'Nooku Platform'
});

});

require.register("controllers/ArticlesController", function(exports, require, module) {
'use strict';

module.exports = App.ArticlesController = Ember.ArrayController.extend({
});

});

require.register("initialize", function(exports, require, module) {
'use strict';

window.App = require('config/app');
require('config/router');
require('config/store');
require('controllers/ApplicationController');

// Load all modules in order automagically. Ember likes things to work this
// way so everything is in the App.* namespace.
var folderOrder = [
    'initializers', 'mixins', 'routes', 'models',
    'views', 'controllers', 'helpers',
    'templates', 'components'
  ];

folderOrder.forEach(function(folder) {
  window.require.list().filter(function(module) {
    return new RegExp('^' + folder + '/').test(module);
  }).forEach(function(module) {
    require(module);
  });
});

});

require.register("models/Article", function(exports, require, module) {
'use strict';

module.exports = App.Article = DS.Model.extend({
    title: DS.attr('string'),
    slug: DS.attr('string'),
    introtext: DS.attr('string'),
    fulltext: DS.attr('string'),
    introtextSafe: function(){
        return this.get('introtext').htmlSafe();
    }.property('introtext')
});
});

require.register("routes/ArticleRoute", function(exports, require, module) {
'use strict';

module.exports = App.ArticleRoute = Ember.Route.extend({
  model: function(params) {
    return this.store.find('article', params.slug);
  }
});

});

require.register("routes/ArticlesRoute", function(exports, require, module) {
'use strict';

module.exports = App.ArticlesRoute = Ember.Route.extend({
  model: function() {
    return this.store.findAll('article');
  }
});

});

require.register("routes/BlogRoute", function(exports, require, module) {
'use strict';

module.exports = App.BlogRoute = Ember.Route.extend({
  model: function() {
    return this.modelFor('articles');
  }
});

});

require.register("routes/IndexRoute", function(exports, require, module) {
'use strict';

module.exports = App.IndexRoute = Ember.Route.extend({
  model: function() {
    return ['red', 'yellow', 'blue'];
  }
});

});

require.register("routes/TableRoute", function(exports, require, module) {
'use strict';

module.exports = App.TableRoute = Ember.Route.extend({
  model: function() {
    return this.modelFor('articles');
  }
});

});

require.register("templates/application", function(exports, require, module) {
module.exports = Ember.TEMPLATES['application'] = Ember.Handlebars.template(function anonymous(Handlebars,depth0,helpers,partials,data) {
this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Ember.Handlebars.helpers); data = data || {};
  var buffer = '', stack1, helper, options, escapeExpression=this.escapeExpression, self=this, helperMissing=helpers.helperMissing;

function program1(depth0,data) {
  
  var buffer = '';
  data.buffer.push("<a ");
  data.buffer.push(escapeExpression(helpers['bind-attr'].call(depth0, {hash:{
    'href': ("view.href")
  },hashTypes:{'href': "STRING"},hashContexts:{'href': depth0},contexts:[],types:[],data:data})));
  data.buffer.push(">Home</a>");
  return buffer;
  }

function program3(depth0,data) {
  
  var buffer = '';
  data.buffer.push("<a ");
  data.buffer.push(escapeExpression(helpers['bind-attr'].call(depth0, {hash:{
    'href': ("view.href")
  },hashTypes:{'href': "STRING"},hashContexts:{'href': depth0},contexts:[],types:[],data:data})));
  data.buffer.push(">Articles</a>");
  return buffer;
  }

function program5(depth0,data) {
  
  var buffer = '';
  data.buffer.push("<a ");
  data.buffer.push(escapeExpression(helpers['bind-attr'].call(depth0, {hash:{
    'href': ("view.href")
  },hashTypes:{'href': "STRING"},hashContexts:{'href': depth0},contexts:[],types:[],data:data})));
  data.buffer.push(">Files</a>");
  return buffer;
  }

  data.buffer.push("<header class=\"container\">\n    <nav class=\"navbar navbar-default\">\n        <a class=\"navbar-brand\" href=\"/\">");
  stack1 = helpers._triageMustache.call(depth0, "pageTitle", {hash:{},hashTypes:{},hashContexts:{},contexts:[depth0],types:["ID"],data:data});
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("</a>\n        <div>\n            <nav role=\"navigation\">\n                <ul class=\"nav navbar-nav\">\n                    ");
  stack1 = (helper = helpers['link-to'] || (depth0 && depth0['link-to']),options={hash:{
    'tagName': ("li")
  },hashTypes:{'tagName': "STRING"},hashContexts:{'tagName': depth0},inverse:self.noop,fn:self.program(1, program1, data),contexts:[depth0],types:["STRING"],data:data},helper ? helper.call(depth0, "index", options) : helperMissing.call(depth0, "link-to", "index", options));
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n                    ");
  stack1 = (helper = helpers['link-to'] || (depth0 && depth0['link-to']),options={hash:{
    'tagName': ("li")
  },hashTypes:{'tagName': "STRING"},hashContexts:{'tagName': depth0},inverse:self.noop,fn:self.program(3, program3, data),contexts:[depth0],types:["STRING"],data:data},helper ? helper.call(depth0, "articles.index", options) : helperMissing.call(depth0, "link-to", "articles.index", options));
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n                    ");
  stack1 = (helper = helpers['link-to'] || (depth0 && depth0['link-to']),options={hash:{
    'tagName': ("li")
  },hashTypes:{'tagName': "STRING"},hashContexts:{'tagName': depth0},inverse:self.noop,fn:self.program(5, program5, data),contexts:[depth0],types:["STRING"],data:data},helper ? helper.call(depth0, "files", options) : helperMissing.call(depth0, "link-to", "files", options));
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n                </ul>\n            </nav>\n        </div>\n        <form action=\"/search\" method=\"get\" class=\"navbar-form pull-right\">\n            <div class=\"form-group\">\n                <input id=\"search\" name=\"search\" class=\"form-control\" type=\"text\" value=\"\" placeholder=\"Search articles\">\n            </div>\n            <button type=\"submit\" class=\"btn btn-default\">Submit</button>\n        </form>\n    </nav>\n</header>\n\n");
  stack1 = helpers._triageMustache.call(depth0, "outlet", {hash:{},hashTypes:{},hashContexts:{},contexts:[depth0],types:["ID"],data:data});
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  return buffer;
  
});
});

require.register("templates/articles", function(exports, require, module) {
module.exports = Ember.TEMPLATES['articles'] = Ember.Handlebars.template(function anonymous(Handlebars,depth0,helpers,partials,data) {
this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Ember.Handlebars.helpers); data = data || {};
  var buffer = '', stack1, helper, options, escapeExpression=this.escapeExpression, self=this, helperMissing=helpers.helperMissing;

function program1(depth0,data) {
  
  var buffer = '';
  data.buffer.push("<a ");
  data.buffer.push(escapeExpression(helpers['bind-attr'].call(depth0, {hash:{
    'href': ("view.href")
  },hashTypes:{'href': "STRING"},hashContexts:{'href': depth0},contexts:[],types:[],data:data})));
  data.buffer.push(">Blog</a>");
  return buffer;
  }

function program3(depth0,data) {
  
  var buffer = '';
  data.buffer.push("<a ");
  data.buffer.push(escapeExpression(helpers['bind-attr'].call(depth0, {hash:{
    'href': ("view.href")
  },hashTypes:{'href': "STRING"},hashContexts:{'href': depth0},contexts:[],types:[],data:data})));
  data.buffer.push(">Table</a>");
  return buffer;
  }

  data.buffer.push("<div class=\"container\">\n    <div class=\"row\">\n        <aside class=\"sidebar col-md-3\">\n            <nav role=\"navigation\">\n                <ul class=\"nav nav-pills nav-stacked\">\n                    ");
  stack1 = (helper = helpers['link-to'] || (depth0 && depth0['link-to']),options={hash:{
    'tagName': ("li")
  },hashTypes:{'tagName': "STRING"},hashContexts:{'tagName': depth0},inverse:self.noop,fn:self.program(1, program1, data),contexts:[depth0],types:["STRING"],data:data},helper ? helper.call(depth0, "blog", options) : helperMissing.call(depth0, "link-to", "blog", options));
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n                    ");
  stack1 = (helper = helpers['link-to'] || (depth0 && depth0['link-to']),options={hash:{
    'tagName': ("li")
  },hashTypes:{'tagName': "STRING"},hashContexts:{'tagName': depth0},inverse:self.noop,fn:self.program(3, program3, data),contexts:[depth0],types:["STRING"],data:data},helper ? helper.call(depth0, "table", options) : helperMissing.call(depth0, "link-to", "table", options));
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n                </ul>\n            </nav>\n        </aside>\n        <div class=\"col-md-9\">\n            ");
  stack1 = helpers._triageMustache.call(depth0, "outlet", {hash:{},hashTypes:{},hashContexts:{},contexts:[depth0],types:["ID"],data:data});
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n        </div>\n    </div>\n</div>");
  return buffer;
  
});
});

require.register("templates/blog", function(exports, require, module) {
module.exports = Ember.TEMPLATES['blog'] = Ember.Handlebars.template(function anonymous(Handlebars,depth0,helpers,partials,data) {
this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Ember.Handlebars.helpers); data = data || {};
  var buffer = '', stack1, self=this, helperMissing=helpers.helperMissing;

function program1(depth0,data) {
  
  var buffer = '', stack1, helper, options;
  data.buffer.push("\n    <article>\n        <header>\n            <h1>");
  stack1 = (helper = helpers['link-to'] || (depth0 && depth0['link-to']),options={hash:{},hashTypes:{},hashContexts:{},inverse:self.noop,fn:self.program(2, program2, data),contexts:[depth0,depth0],types:["STRING","ID"],data:data},helper ? helper.call(depth0, "blog.article", "", options) : helperMissing.call(depth0, "link-to", "blog.article", "", options));
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("</h1>\n        </header>\n        ");
  stack1 = helpers._triageMustache.call(depth0, "introtextSafe", {hash:{},hashTypes:{},hashContexts:{},contexts:[depth0],types:["ID"],data:data});
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n        ");
  stack1 = (helper = helpers['link-to'] || (depth0 && depth0['link-to']),options={hash:{
    'classNames': ("article__readmore")
  },hashTypes:{'classNames': "STRING"},hashContexts:{'classNames': depth0},inverse:self.noop,fn:self.program(4, program4, data),contexts:[depth0,depth0],types:["STRING","ID"],data:data},helper ? helper.call(depth0, "blog.article", "", options) : helperMissing.call(depth0, "link-to", "blog.article", "", options));
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n    </article>   \n    ");
  return buffer;
  }
function program2(depth0,data) {
  
  var stack1;
  stack1 = helpers._triageMustache.call(depth0, "title", {hash:{},hashTypes:{},hashContexts:{},contexts:[depth0],types:["ID"],data:data});
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  else { data.buffer.push(''); }
  }

function program4(depth0,data) {
  
  
  data.buffer.push("Read more");
  }

  data.buffer.push("<section>\n    <div class=\"page-header\">\n        <h1>Blog</h1>\n    </div>\n    ");
  stack1 = helpers.each.call(depth0, {hash:{},hashTypes:{},hashContexts:{},inverse:self.noop,fn:self.program(1, program1, data),contexts:[],types:[],data:data});
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push(" \n</section>");
  return buffer;
  
});
});

require.register("templates/index", function(exports, require, module) {
module.exports = Ember.TEMPLATES['index'] = Ember.Handlebars.template(function anonymous(Handlebars,depth0,helpers,partials,data) {
this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Ember.Handlebars.helpers); data = data || {};
  var buffer = '', stack1, helper, options, helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression;


  data.buffer.push("<div class=\"container\">\n    <div class=\"row\">\n        <aside class=\"sidebar col-md-3\">\n            ");
  data.buffer.push(escapeExpression((helper = helpers.outlet || (depth0 && depth0.outlet),options={hash:{},hashTypes:{},hashContexts:{},contexts:[depth0],types:["ID"],data:data},helper ? helper.call(depth0, "sidebar", options) : helperMissing.call(depth0, "outlet", "sidebar", options))));
  data.buffer.push("\n        </aside>\n        <div class=\"col-md-9\">\n            ");
  stack1 = helpers._triageMustache.call(depth0, "outlet", {hash:{},hashTypes:{},hashContexts:{},contexts:[depth0],types:["ID"],data:data});
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n\n            <article>\n                <header>\n                    <h1><a href=\"/2-cras\">Cras</a></h1>\n                </header>\n\n\n                <p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum id ligula porta felis euismod semper. Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Etiam porta sem malesuada magna mollis euismod.</p>        <a class=\"article__readmore\" href=\"/2-cras\">Read more</a>\n            </article>    \n            <article>\n                <header>\n                <h1><a href=\"/3-elit-adipiscing\">Elit Adipiscing</a></h1>\n                                </header>\n\n\n                <p>Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Donec sed odio dui. Nullam id dolor id nibh ultricies vehicula ut id elit. Curabitur blandit tempus porttitor.</p>        <a class=\"article__readmore\" href=\"/3-elit-adipiscing\">Read more</a>\n            </article>    \n            <article>\n                <header>\n                <h1><a href=\"/5-nibh-vulputate\">Nibh Vulputate</a></h1>\n                                </header>\n\n\n                <p>Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Nullam id dolor id nibh ultricies vehicula ut id elit. Vestibulum id ligula porta felis euismod semper. Nullam id dolor id nibh ultricies vehicula ut id elit. Aenean lacinia bibendum nulla sed consectetur. Cras mattis consectetur purus sit amet fermentum.</p>        <a class=\"article__readmore\" href=\"/5-nibh-vulputate\">Read more</a>\n            </article>\n        </div>\n    </div>\n</div>\n");
  return buffer;
  
});
});

require.register("templates/table", function(exports, require, module) {
module.exports = Ember.TEMPLATES['table'] = Ember.Handlebars.template(function anonymous(Handlebars,depth0,helpers,partials,data) {
this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Ember.Handlebars.helpers); data = data || {};
  var buffer = '', stack1, self=this, helperMissing=helpers.helperMissing;

function program1(depth0,data) {
  
  var buffer = '', stack1, helper, options;
  data.buffer.push("\n            <tr>\n                <td>\n                    ");
  stack1 = (helper = helpers['link-to'] || (depth0 && depth0['link-to']),options={hash:{},hashTypes:{},hashContexts:{},inverse:self.noop,fn:self.program(2, program2, data),contexts:[depth0,depth0],types:["STRING","ID"],data:data},helper ? helper.call(depth0, "table.article", "", options) : helperMissing.call(depth0, "link-to", "table.article", "", options));
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n                </td>\n            </tr>\n            ");
  return buffer;
  }
function program2(depth0,data) {
  
  var stack1;
  stack1 = helpers._triageMustache.call(depth0, "title", {hash:{},hashTypes:{},hashContexts:{},contexts:[depth0],types:["ID"],data:data});
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  else { data.buffer.push(''); }
  }

  data.buffer.push("<section>\n                \n    <div class=\"page-header\">\n        <h1>Table</h1>\n    </div>\n\n    <table class=\"table table-striped\">\n        <thead>\n            <tr>\n                <th width=\"100%\">\n                    Title\n                </th>\n            </tr>\n        </thead>\n        <tbody>\n            ");
  stack1 = helpers.each.call(depth0, {hash:{},hashTypes:{},hashContexts:{},inverse:self.noop,fn:self.program(1, program1, data),contexts:[],types:[],data:data});
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n        </tbody>\n    </table>\n</section>");
  return buffer;
  
});
});

require.register("envs/development/env", function(exports, require, module) {
'use strict';

module.exports = 'development';

});


//# sourceMappingURL=app.js.map