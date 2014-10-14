<?
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-platform for the canonical source repository
 */
?>

<? if(parameters()->type['name'] == 'component') : ?>

<?= $page->getParams('url')->render('urlparams') ?>

<? if($rendered_params = $page->getParams('layout')->render('params')) : ?>
    <?= $rendered_params ?>
<? endif ?>

<?= $page->getParams('page')->render('params'); ?>
<? endif ?>

<? if(parameters()->type['name'] == 'redirect') : ?>
    <div id="page-link-type">
        <label for="parent"><?= translate('Type') ?></label>
        <div id="parent" class="controls">
            <label class="radio">
                <input type="radio" name="link_type" value="id" <?= $page->isNew() || $page->link_id ? 'checked="checked"' : '' ?> />
                <?= translate('Page') ?>
            </label>

            <label class="radio">
                <input type="radio" name="link_type" value="url" <?= $page->link_url ? 'checked="checked"' : '' ?> />
                <?= translate('URL') ?>
            </label>
        </div>
    </div>
    <div id="page-link-id">
        <label for="parent"><?= translate('Page') ?></label>
        <div id="parent" class="controls">
            <?= helper('listbox.pages', array('name' => 'link_id', 'selected' => $page->link_id)) ?>
        </div>
    </div>
    <div id="page-link-url">
        <label for="parent"><?= translate('URL') ?></label>
        <div id="parent" class="controls">
            <input type="text" name="link_url" value="<?= $page->link_url ?>" />
        </div>
    </div>
<? endif ?>

<? if(parameters()->type['name'] == 'pagelink') : ?>
    <div>
        <label for="parent"><?= translate('Page') ?></label>
        <div id="parent" class="controls">
            <?= helper('listbox.pages', array('name' => 'link_id', 'selected' => $page->link_id)) ?>
        </div>
    </div>
<? endif ?>