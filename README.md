nooku-ember-example-theme
=========================

A example theme for [Nooku Platform](https://github.com/nooku/nooku-platform) implementing [EmberJS](http://emberjs.com/). 

### Installation
To install, fork Nooku Platform from the develop branch and drop the ember folder into your site application theme folder (/application/site/public/theme) and point your site’s config to the new theme.

The theme uses brunch to compile the assets. The skeleton is based on [https://github.com/gcollazo/brunch-with-ember-reloaded](https://github.com/gcollazo/brunch-with-ember-reloaded).

To install, cd to 
```
/application/site/public/theme/ember
```
and run
```
npm install
```

To compile, run

```
brunch watch -s
```
or

```
brunch build --production
```

### Contribution
This project is meant to be a group effort. Feel free to add to this theme by sending in PR’s.

#### Call for help
Nooku Platform is included in this repository at the moment, as I have adapted *ArticlesControllerArticle* to allow JSON requests.

The idea is to have this theme available as a [Brunch](http://brunch.io/) skeleton.

If anyone can submit a PR with a more elegant solution, say an Articles component override, that would be nice. If such a component override could also allow for frontend editing, than that would be even nicer.
