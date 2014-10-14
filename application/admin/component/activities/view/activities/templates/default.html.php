<?
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-platform for the canonical source repository
 */
?>

<ktml:module position="actionbar">
    <ktml:toolbar type="actionbar">
</ktml:module>

<ktml:module position="sidebar">
	<?= import('default_sidebar.html'); ?>
</ktml:module>

<form action="" method="get" class="-koowa-grid">
	<?= import('default_scopebar.html') ?>
	<table>
		<thead>
			<tr>
				<th width="10">
                    <?= helper('grid.checkall') ?>
                </th>
				<th width="55"><?=translate('Time')?></th>
				<th><?=translate('Message')?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3">
					<?= helper('com:application.paginator.pagination') ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<? $date = $old_date = '';   ?>
		<? foreach ($activities as $activity) : ?>
	        <? $date = helper('date.format', array('date' => $activity->created_on, 'format' => 'l d M Y'))?>
	        <? if ($date != $old_date): ?>
	        <? $old_date = $date; ?>
	        <tr class="no-hover separator">
				<td colspan="3">
			        <?= $date; ?>
				</td>
			</tr>
	        <? endif; ?>
			<tr>
				<td>
			        <?= helper('grid.checkbox',array('entity' => $activity)); ?>
				</td>

				<td align="left">
			        <?= helper('date.format', array('date' => $activity->created_on, 'format' => 'H:i'))?>
				</td>

				<td>
					<i class="icon-<?= $activity->action ?>"></i> <?= helper('activity.message', array('entity' => $activity))?>
				</td>
			</tr>
        <? endforeach; ?>
		</tbody>
	</table>
</form>