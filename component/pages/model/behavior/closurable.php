<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Component\Pages;

use Nooku\Library;

/**
 * Closurable Controller Behavior
 *
 * @author  Gergo Erdosi <http://nooku.assembla.com/profile/gergoerdosi>
 * @package Nooku\Component\Pages
 */
class ModelBehaviorClosurable extends Library\ModelBehaviorAbstract
{
    protected function _beforeFetch(Library\ModelContextInterface $context)
    {
        $model = $context->getSubject();

        if ($model instanceof Library\ModelDatabase && $model->getTable()->isClosurable()) {
            $state = $context->state;

            if (!isset($state->parent)) {
                $state->insert('parent', 'int');
            }

            if (!isset($state->level)) {
                $state->insert('level', 'int');
            }

            if (!$state->isUnique()) {
                if ($state->sort) {
                    $context->query->bind(array('sort' => $state->sort));
                }

                if ($state->parent) {
                    $context->query->bind(array('parent' => $state->parent));
                }

                if ($state->level) {
                    $context->query->bind(array('level' => $state->level));
                }
            }
        }
    }
}