<?php
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-platform for the canonical source repository
 */

namespace Nooku\Component\Articles;

use Nooku\Library;

/**
 * Publishable Database Behavior
 *
 * Auto publishes/un-publishes items.
 *
 * @author  Arunas Mazeika <http://github.com/amazeika>
 * @package Nooku\Component\Articles
 */
class DatabaseBehaviorPublishable extends Library\DatabaseBehaviorAbstract
{
    /**
     * Track updated status
     *
     * Variable keeps track of the updated status of the items table. A value of true indicates that items are
     * already up to date, i.e. published and unpublished according with the current timestamp.
     *
     * @var bool
     */
    protected $_uptodate = false;

    /**
     * The name of the table containing the publishable items.
     *
     * @var string
     */
    protected $_table;

    /**
     * The current date.
     *
     * @var Library\Date The current date.
     */
    protected $_date;

    public function __construct(Library\ObjectConfig $config)
    {
        parent::__construct($config);
        
        $this->_table = $config->table;
        $this->_date  = $this->getObject('lib:date', array('timezone' => 'GMT'));
    }

    protected function _initialize(Library\ObjectConfig $config)
    {
        $config->append(array(
            'table'=> 'articles'
        ));

        parent::_initialize($config);
    }

    protected function _afterSelect(Library\DatabaseContext $context)
    {
        $data = $context->data;

        if ($data instanceof Library\DatabaseRowsetInterface && !$this->_uptodate)
        {
            $this->_publishItems();
            $this->_unpublishItems();

            $this->_uptodate = true;
        }
    }

    protected function _beforeInsert(Library\DatabaseContext $context)
    {
        // Same as update.
        $this->_beforeUpdate($context);
    }

    protected function _beforeUpdate(Library\DatabaseContext $context)
    {
        $data = $context->data;

        // Un-publish the item
        if ($data->published && (strtotime($data->publish_on) > $this->_date->getTimestamp())) {
            $data->published = 0;
        }
    }

    /**
     * Publishes items given a date.
     */
    protected function _publishItems()
    {
        $query = $this->_getQuery();

        $query->where('publish_on <= :date')->where('published = :published')->where('publish_on IS NOT NULL')
            ->values('published = :value')
            ->bind(array('date'      => $this->_date->format('Y-m-d H:i:s'),
                         'published' => 0,
                         'value'     => 1));

        $this->getMixer()->getTable()->getAdapter()->update($query);
    }

    /**
     * Un-publishes items given a date.
     */
    protected function _unpublishItems()
    {
        $query = $this->_getQuery();

        $query->where('unpublish_on <= :date')->where('published = :published')->where('unpublish_on IS NOT NULL')
            ->values('published = :value')
            ->bind(array('date'      => $this->_date->format('Y-m-d H:i:s'),
                         'published' => 1,
                         'value'     => 0));

        $this->getMixer()->getTable()->getAdapter()->update($query);
    }

    /**
     * Generic query getter.
     *
     * @return object A query object.
     */
    protected function _getQuery()
    {
        $query = $this->getObject('lib:database.query.update');
        $query->table(array($this->_table));

        return $query;
    }
}
