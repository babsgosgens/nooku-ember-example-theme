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
 * Indexable Model Behavior
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Library\Controller
 */
class ModelBehaviorIndexable extends ModelBehaviorAbstract
{
    /**
     * Constructor.
     *
     * @param   ObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.fetch', '_buildQuery')
            ->addCommandCallback('before.count', '_buildQuery');
    }

    /**
     * Insert the model states
     *
     * @param ObjectMixable $mixer
     */
    public function onMixin(ObjectMixable $mixer)
    {
        parent::onMixin($mixer);

        if ($mixer instanceof ModelDatabase) {
            $table = $mixer->getTable();

            // Set the dynamic states based on the unique table keys
            foreach ($table->getUniqueColumns() as $key => $column) {
                $mixer->getState()->insert($key, $column->filter, null, true, $table->mapColumns($column->related, true));
            }
        }
    }

    /**
     * Add order query
     *
     * @param   ModelContextInterface $context A model context object
     *
     * @return    void
     */
    protected function _buildQuery(ModelContextInterface $context)
    {
        $model = $context->getSubject();

        if ($model instanceof ModelDatabase) {
            //Get only the unique states
            $states = $context->state->getValues(true);

            if (!empty($states)) {
                $states = $model->getTable()->mapColumns($states);
                foreach ($states as $key => $value) {
                    if (isset($value)) {
                        $context->query->where('tbl.' . $key . ' ' . (is_array($value) ? 'IN' : '=') . ' :' . $key)
                            ->bind(array($key => $value));
                    }
                }
            }
        }
    }
}