<?php
/**
 * @version     $Id$
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Users
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<!--
<script src="media://lib_koowa/js/koowa.js" />
<style src="media://lib_koowa/css/koowa.css" />
-->

<?= @template('com://admin/default.view.grid.toolbar'); ?>

<module title="" position="sidebar">
	<?= @template('default_sidebar', array('groups' => $groups)); ?>
</module>

<module title="" position="inspector">
    <?= @template('com://admin/activities.view.activities.simple', array('package' => 'users', 'name' => 'user')); ?>
</module>

<form action="" method="get" class="-koowa-grid">
	<?= @template('default_scopebar'); ?>
	<table>
		<thead>
			<tr>
				<th width="10">
				    <?= @helper('grid.checkall'); ?>
				</th>
				<th>
					<?= @helper('grid.sort', array('title' => 'Name', 'column' => 'name')) ?>
				</th>
				<th width="15%">
					<?= @helper('grid.sort',  array('title' => 'Username', 'column' => 'username')) ?>
				</th>
				<th width="8%">
					<?= @helper('grid.sort',  array('title' => 'Logged In', 'column' => 'loggedin')) ?>
				</th>
				<th width="8%">
					<?= @helper('grid.sort',  array('title' => 'Enabled', 'column' => 'enabled')) ?>
				</th>
				<th width="15%">
					<?= @helper('grid.sort',  array('title' => 'Group', 'column' => 'group_name')) ?>
				</th>
				<th width="15%">
					<?= @helper('grid.sort',  array('title' => 'E-Mail', 'column' => 'email')) ?>
				</th>
				<th width="10%">
					<?= @helper('grid.sort',  array('title' => 'Last Visit', 'column' => 'last_visited_on')) ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?= @helper('paginator.pagination', array('total' => $total)) ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<? $i = 0 ?>
		<? foreach($users as $user) : ?>
			<tr>
				<td align="center">
					<?= @helper('grid.checkbox' , array('row' => $user)) ?>
				</td>
				<td>
					<a href="<?= @route('view=user&id='.$user->id) ?>">
						<?= @escape($user->name) ?>
					</a>
				</td>
				<td>
					<?= @escape($user->username) ?>
				</td>
				<td align="center">
					<img src="media://system/images/<?= $user->loggedin ? 'tick.png' : 'publish_x.png' ?>" border="0" />
				</td>
				<td align="center">
					<?= @helper('grid.enable', array('row' => $user, 'option' => 'com_users', 'view' => 'users')) ?>
				</td>
				<td>
					<?= @escape($user->group_name) ?>
				</td>
				<td>
					<?= @escape($user->email) ?>
				</td>
				<td>
					<? if($user->last_visited_on == '0000-00-00 00:00:00') : ?>
						<?= @text('Never') ?>
					<? else : ?>
						<?= @helper('date.humanize', array('date' => $user->last_visited_on)) ?>
					<? endif ?>
				</td>
			</tr>
			<? $i++ ?>
		<? endforeach ?>
		</tbody>
	</table>
</form>