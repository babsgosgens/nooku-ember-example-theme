<?
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-platform for the canonical source repository
 */
?>

<ktml:style src="assets://debug/css/debug-default.css" />
<ktml:script src="assets://debug/js/debug.js" />

<div id="debug" class="profiler">
<a class="close" title="<?= translate("press 'd' to bring the debug bar back up") ?>"></a>
<?=	helper('tabs.startPane', array('id' => 'debug')); ?>

<?= helper('tabs.startPanel', array('title' => 'Overview', 'attribs' => array( 'class' => 'profiles'))); ?>
    <?= import('default_overview.html'); ?>
<?= helper('tabs.endPanel'); ?>

<?= helper('tabs.startPanel', array('title' => 'Profiles', 'attribs' => array( 'class' => 'timeline'))); ?>
	<?= import('default_profiles.html'); ?>
<?= helper('tabs.endPanel'); ?>

<?= helper('tabs.startPanel', array('title' => 'Queries', 'attribs' => array( 'class' => 'storage'))); ?>
	<?= import('default_queries.html'); ?>
<?= helper('tabs.endPanel'); ?>

<?= helper('tabs.startPanel', array('title' => 'Resources', 'attribs' => array( 'class' => 'resources'))); ?>
	<?= import('default_resources.html'); ?>
<?= helper('tabs.endPanel'); ?>

<?= helper('tabs.startPanel', array('title' => 'Strings', 'attribs' => array( 'class' => 'audits'))); ?>
	<?= import('default_strings.html'); ?>
<?= helper('tabs.endPanel'); ?>

<?= helper('tabs.endPane'); ?>
</div>