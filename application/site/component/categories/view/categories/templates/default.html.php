<?
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */
?>

<? foreach($categories as $category) : ?>
<div>
    <div class="page-header">
        <h1>
            <a href="<?= helper('route.category', array('row' => $category)) ?>">
                <?= escape($category->title);?>
            </a>
        </h1>
    </div>

    <? if($category->attachments_attachment_id) : ?>
        <a href="<?= helper('route.category', array('row' => $category)) ?>">
            <figure>
                <?= helper('com:attachments.image.thumbnail', array(
                    'attachment' => $category->attachments_attachment_id,
                    'attribs' => array('width' => '200', 'align' => 'right', 'class' => 'thumbnail'))) ?>
            </figure>
        </a>
    <? endif ?>

    <? if ($category->description) : ?>
    <p><?= $category->description; ?></p>
    <? endif; ?>

    <a href="<?= helper('route.category', array('row' => $category)) ?>"><?= translate('Read more') ?></a>
</div>
<? endforeach; ?>
