'use strict';

module.exports = App.TableRoute = Ember.Route.extend({
  model: function() {
    return this.modelFor('articles');
  }
});
