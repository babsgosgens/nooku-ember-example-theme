<?
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright   Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        git://git.assembla.com/nooku-framework.git for the canonical source repository
 */
?>

<!DOCTYPE HTML>
<html lang="<?= $language; ?>" dir="<?= $direction; ?>">
  <?= import('page_head.html') ?>
  <body>

  
  <script type="text/x-handlebars">
    <h2>Welcome to Ember.js</h2>

    {{outlet}}
  </script>

  <script type="text/x-handlebars" id="index">
    <ul>
    {{#each item in model}}
      <li>{{item}}</li>
    {{/each}}
    </ul>
  </script>

  <script src="assets://application/js/libs/jquery-1.10.2.js"></script>
  <script src="assets://application/js/libs/handlebars-1.1.2.js"></script>
  <script src="assets://application/js/libs/ember-1.6.1.js"></script>
  <script src="assets://application/js/app.js"></script>
  <!-- to activate the test runner, add the "?test" query string parameter -->
  <script src="assets://application/tests/runner.js"></script>

  </body>
</html>