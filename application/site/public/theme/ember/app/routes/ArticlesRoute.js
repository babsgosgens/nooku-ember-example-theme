'use strict';

module.exports = App.ArticlesRoute = Ember.Route.extend({
  model: function() {
    return this.store.findAll('article');
  }
});
