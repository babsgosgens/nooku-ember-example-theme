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