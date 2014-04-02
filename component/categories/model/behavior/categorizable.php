<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Component\Categories;

use Nooku\Library;

/**
 * Categorizable Controller Behavior
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Component\Categories
 */
class ModelBehaviorCategorizable extends Library\ModelBehaviorAbstract
{
    /**
     * Constructor.
     *
     * @param   Library\ObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(Library\ObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.fetch', '_buildQuery')
            ->addCommandCallback('before.count', '_buildQuery');
    }

    public function onMixin(Library\ObjectMixable $mixer)
    {
        parent::onMixin($mixer);

        $mixer->getState()
            ->insert('category', 'slug')
            ->insert('category_recurse', 'boolean', false);
    }

    protected function _buildQuery(Library\ModelContextInterface $context)
    {
        $model = $context->getSubject();

        if ($model instanceof Library\ModelDatabase && $model->getTable()->isCategorizable()) {
            $state = $context->state;

            $context->query->bind(array(
                'category'         => $state->category,
                'category_recurse' => $state->category_recurse
            ));

            //Order based on category title
            $context->query->order('category_title', 'ASC');
        }
    }
}