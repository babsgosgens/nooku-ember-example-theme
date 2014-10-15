'use strict';

module.exports = App.BlogRoute = Ember.Route.extend({
  model: function() {
    return this.modelFor('articles');
  }
});
