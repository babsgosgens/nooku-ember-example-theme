'use strict';

var config = {
    LOG_TRANSITIONS: true,
    LOG_TRANSITIONS_INTERNAL: false,
    apiMode: 'system' // seo | system
  };

module.exports = Ember.Application.create(config);
