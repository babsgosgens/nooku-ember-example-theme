<?
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

<?= helper('behavior.sortable') ?>

<ktml:module position="actionbar">
    <ktml:toolbar type="actionbar">
</ktml:module>

<? if(parameters()->table == 'articles') : ?>
<ktml:module position="sidebar">
    <?= import('default_sidebar.html'); ?>
</ktml:module>
<? endif; ?>

<form action="" method="get" class="-koowa-grid">
    <input type="hidden" name="type" value="<?= parameters()->type;?>" />

    <?= import('default_scopebar.html'); ?>
    <table>
        <thead>
            <tr>
                <? if(parameters()->sort == 'ordering' && parameters()->direction == 'asc') : ?>
                <th class="handle"></th>
                <? endif ?>
                <th width="1">
                    <?= helper('grid.checkall'); ?>
                </th>
                <th width="1"></th>
                <th>
                    <?= helper('grid.sort',  array('column' => 'title')); ?>
                </th>
                <th width="1">
                    <?= helper('grid.sort',  array( 'title' => 'Articles', 'column' => 'count')); ?>
                </th>
            </tr>
        </thead>

        <tfoot>
            <tr>
                <td colspan="13">
                    <?= helper('com:application.paginator.pagination'); ?>
                </td>
            </tr>
        </tfoot>

        <tbody<? if(parameters()->sort == 'ordering' && parameters()->direction == 'asc') : ?> class="sortable"<? endif ?>>
            <? foreach( $categories as $category) :  ?>
                <tr>
                    <? if(parameters()->sort == 'ordering' && parameters()->direction == 'asc') : ?>
                    <td class="handle">
                        <span class="text--small data-order"><?= $category->ordering ?></span>
                    </td>
                    <? endif ?>
                    <td align="center">
                        <?= helper( 'grid.checkbox' , array('entity' => $category)); ?>
                    </td>
                    <td align="center">
                        <?= helper('grid.enable', array('entity' => $category, 'field' => 'published')) ?>
                    </td>
                    <td>
                        <a href="<?= route( 'view=category&id='.$category->id ); ?>">
                            <?= escape($category->title); ?>
                         </a>
                         <? if($category->access) : ?>
                             <span class="label label-important"><?= translate('Registered') ?></span>
                         <? endif; ?>
                    </td>
                    <td align="center">
                        <?= $category->count; ?>
                    </td>
            	</tr>
            <? endforeach; ?>
       </tbody>
    </table>
</form>
