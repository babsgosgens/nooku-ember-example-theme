<?php 
/**
 * @version     $Id$
 * @package     Nooku_Server
 * @subpackage  Articles
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<ul class="scrollable">
	<li class="<?= !is_numeric($state->category) ? 'active' : ''; ?>">
		<a href="<?= @route('category=' ) ?>">
		    <?= @text('All articles')?>
		</a>
	</li>
	<li class="<?= $state->category == '0' ? 'active' : ''; ?>">
		<a href="<?= @route('&category=0' ) ?>">
			<?= @text('Uncategorised') ?>
		</a>
	</li>
	<? foreach($categories as $category) : ?>
	<li class="<?= $state->category == $category->id ? 'active' : ''; ?>">
		<a href="<?= @route('category='.$category->id ) ?>">
			<?= @escape($category->title) ?>
		</a>
		<? if($category->hasChildren()) : ?>
		<ul>
			<? foreach($category->getChildren() as $child) : ?>
			<li class="<?= $state->category == $child->id ? 'active' : ''; ?>">
				<a href="<?= @route('category='.$child->id ) ?>">
					<?= $child->title; ?>
				</a>
			</li>
			<? endforeach ?>
		</ul>
		<? endif; ?>
	</li>
	<? endforeach ?>
</ul>