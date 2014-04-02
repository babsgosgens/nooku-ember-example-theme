<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright      Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Library;

/**
 * Orderable Model Behavior
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Library\Orderable
 */
class ModelBehaviorOrderable extends ModelBehaviorAbstract
{
    /**
     * Insert the model states
     *
     * @param ObjectMixable $mixer
     */
    public function onMixin(ObjectMixable $mixer)
    {
        parent::onMixin($mixer);

        $mixer->getState()
            ->insert('sort', 'cmd')
            ->insert('direction', 'word', 'asc');
    }

    /**
     * Add order query
     *
     * @param   ModelContextInterface $context A model context object
     *
     * @return    void
     */
    protected function _beforeFetch(ModelContextInterface $context)
    {
        $model = $context->getSubject();

        if ($model instanceof ModelDatabase && !$context->state->isUnique()) {
            $state = $context->state;

            $sort      = $state->sort;
            $direction = strtoupper($state->direction);
            $columns   = array_keys($this->getTable()->getColumns());

            if ($sort) {
                $column = $this->getTable()->mapColumns($sort);

                //if(in_array($column, $columns)) {
                $context->query->order($column, $direction);
                //}
            }

            if ($sort != 'ordering' && in_array('ordering', $columns)) {
                $context->query->order('tbl.ordering', 'ASC');
            }
        }
    }
}