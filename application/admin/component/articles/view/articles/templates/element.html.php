<?php
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-platform for the canonical source repository
 */
?>

<ktml:script src="assets://js/koowa.js" />
<ktml:style src="assets://css/koowa.css" />

<form action="" method="get" class="-koowa-grid">
	<table>
		<thead>
			<tr>
				<th class="title">
				    <?= helper('grid.sort', array('title' => 'Title', 'column' => 'title')) ?>
				</th>
				<th align="center" width="10">
					<?= helper('grid.sort', array('title' => 'Date', 'column' => 'created_on')) ?>
				</th>
			</tr>
			<tr>
				<td>
				    <?= helper('grid.search') ?>
				</td>
				<td></td>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="2">
					<?= helper('com:application.paginator.pagination') ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<? foreach($articles as $article) : ?>
			<tr>
				<td>
					<a style="cursor: pointer;" onclick="window.parent.jSelectArticle('<?= $article->id ?>', '<?= str_replace(array("'", "\""), array("\\'", ""), $article->title); ?>', '<?= object('request')->query->get('object', 'cmd'); ?>');">
					    <?= escape($article->title) ?>
					</a>
				</td>
				<td nowrap="nowrap">
					<?= helper('date.humanize', array('date' => $article->created_on)) ?>
				</td>
			</tr>
		<? endforeach ?>
		</tbody>
	</table>
</form>