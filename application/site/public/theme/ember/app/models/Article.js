'use strict';

module.exports = App.Article = DS.Model.extend({
    title: DS.attr('string'),
    slug: DS.attr('string'),
    introtext: DS.attr('string'),
    fulltext: DS.attr('string'),
    introtextHtml: function(){
        return this.get('introtext').htmlSafe();
    }.property('introtext')
});