'use strict';

module.exports = App.ArticleRoute = Ember.Route.extend({
  model: function(params) {
    return this.store.find('article', params.slug);
  }
});
