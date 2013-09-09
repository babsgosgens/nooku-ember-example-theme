<?
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */
?>
<ktml:module position="actionbar">
    <ktml:toolbar type="actionbar">
</ktml:module>

<form action="" method="get" class="-koowa-grid">
    <table>
        <thead>
            <tr>
                <th width="1">
                    <?= helper('grid.checkall') ?>
                </th>
                <th>
                    <?= helper('grid.sort', array('column' => 'created_on', 'title' => 'Date')); ?>
                </th>
                <th>
                    <?= translate('From') ?>
                </th>
                <th>
                    <?= translate('On') ?>
                </th>
                <th>
                    <?= translate('Comment') ?>
                </th>
            </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="20">
                <?= helper('com:application.paginator.pagination', array('total' => $total)) ?>
            </td>
        </tr>
        </tfoot>
        <tbody>
        <? foreach ($comments as $comment) : ?>
            <tr>
                <td align="center">
                    <?= helper('grid.checkbox', array('row' => $comment)); ?>
                </td>
                <td>
                    <?= helper('date.humanize', array('date' => $comment->created_on)) ?>
                </td>
                <td>
                    <a href="<?= route('option=com_users&view=user&id='.$comment->created_by) ?>">
                        <?= escape($comment->created_by_name); ?>
                    </a>
                </td>
                <td>
                    <a href="<?= route('option=com_articles&view='.$comment->table.'&id='.$comment->row); ?>">
                        <?= escape($comment->title); ?>
                    </a>
                </td>
                <td style="width: 100%" class="ellipsis">
                    <a href="<?= route('view=comment&id='.$comment->id); ?>">
                        <?= escape(strip_tags($comment->text)); ?>
                    </a>
                </td>
            </tr>
        <? endforeach; ?>
        </tbody>
    </table>
</form>