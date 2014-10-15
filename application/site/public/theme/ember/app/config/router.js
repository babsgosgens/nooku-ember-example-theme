'use strict';

module.exports = App.Router.map(function() {
    // this.resource('about');
    this.route('index', {path: '/'});
    this.resource('articles', function(){
        this.route('article', {path: ':slug'});
    });
    this.resource('files', function(){
        this.route('file', {path: ':slug'});
    });
});

// module.exports = App.Router.reopen({
//   location: 'history'
// });