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