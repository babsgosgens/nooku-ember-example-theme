<? /** $Id$ */ ?>
<? defined('KOOWA') or die('Restricted access'); ?>

<? @style(@$mediaurl.'/com_profiles/css/grid.css') ?>
<? @style(@$mediaurl.'/com_profiles/css/admin.css') ?>

<form action="<?= @route()?>" method="get">
	<input type="hidden" name="option" value="com_profiles" />
	<input type="hidden" name="view" value="departments" />

	<table>
		<tr>
			<td align="left" width="100%">
				<?=@text('Search')?>:
				<input name="search" id="search" value="<?= @$state->search?>" />
				<button onclick="this.form.submit();"><?= @text('Go')?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('enabled').value='';this.form.submit();"><?= @text('Reset') ?></button>
			</td>
			<td nowrap="nowrap">
				<?= @helper('admin::com.profiles.helper.select.enabled',  @$state->enabled ); ?>
			</td>
		</tr>
	</table>
</form>

<form action="<?= @route()?>" method="post" name="adminForm">
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="action" value="" />
	<table class="adminlist" style="clear: both;">
		<thead>
			<tr>
				<th width="5">
					<?= @text('NUM'); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?= count(@$departments); ?>);" />
				</th>
				<th>
					<?= @helper('grid.sort', 'Title', 'title', @$state->direction, @$state->order); ?>
				</th>
				<th>
					<?= @helper('grid.sort', 'Enabled', 'enabled', @$state->direction, @$state->order); ?>
				</th>
				<th>
					<?= @helper('grid.sort', 'People', 'people', @$state->direction, @$state->order); ?>
				</th>
				<th>
					<?= @helper('grid.sort', 'ID', 'profiles_department_id', @$state->direction, @$state->order); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		
		<?= @template('default_items'); ?>

		<? if (!count(@$departments)) : ?>
			<tr>
				<td colspan="8" align="center">
					<?= @text('No items found'); ?>
				</td>
			</tr>
		<? endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="20">
					<?= @helper('admin::com.koowa.helper.paginator.pagination', @$total, @$state->offset, @$state->limit) ?>
				</td>
			</tr>
		</tfoot>
	</table>
</form>

<?= @template('admin::com.profiles.view.dashboard.default_footer'); ?>