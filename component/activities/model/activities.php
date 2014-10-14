<?php
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright      Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           https://github.com/nooku/nooku-platform for the canonical source repository
 */

namespace Nooku\Component\Activities;

use Nooku\Library;

/**
 * Activities Model
 *
 * @author  Israel Canasa <http://github.com/raeldc>
 * @package Nooku\Component\Activities
 */
class ModelActivities extends Library\ModelDatabase
{
    public function __construct(Library\ObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('application', 'cmd')
            ->insert('package', 'cmd')
            ->insert('name', 'cmd')
            ->insert('action', 'cmd')
            ->insert('user', 'cmd')
            ->insert('distinct', 'boolean', false)
            ->insert('column', 'cmd')
            ->insert('start_date', 'date')
            ->insert('days_back', 'int', 14)
            ->insert('ip', 'ip')
            ->insert('direction', 'word', 'desc');

        // Force ordering by created_on
        $this->getState()->sort = 'tbl.created_on';
    }

    protected function _actionFetch(Library\ModelContext $context)
    {
        $state = $context->state;

        if ($state->distinct && !empty($state->column)) {
            $context->query->order('package', 'ASC');
        }

        return parent::_actionFetch($context);
    }

    protected function _buildQueryColumns(Library\DatabaseQuerySelect $query)
    {
        $state = $this->getState();

        if ($state->distinct && !empty($state->column))
        {
            $query->distinct()
                ->columns($state->column)
                ->columns(array('activities_activity_id' => $state->column));
        }
        else  parent::_buildQueryColumns($query);
    }

    protected function _buildQueryWhere(Library\DatabaseQuerySelect $query)
    {
        parent::_buildQueryWhere($query);
        $state = $this->getState();

        if ($state->application) {
            $query->where('tbl.application = :application')->bind(array('application' => $state->application));
        }

        if ($state->package && !($state->distinct && !empty($state->column))) {
            $query->where('tbl.package = :package')->bind(array('package' => $state->package));
        }

        if ($state->name) {
            $query->where('tbl.name = :name')->bind(array('name' => $state->name));
        }

        if ($state->action) {
            $query->where('tbl.action ' . (is_array($state->action) ? 'IN' : '=') . ' :action')->bind(array('action' => $state->action));
        }

        if ($state->start_date && $state->start_date != '0000-00-00')
        {
            // TODO: Sync this code with Date and DatabaseQuery changes.
            $start_date = $this->getObject('lib:date', array('date' => $this->getState()->start_date));
            $days_back  = clone $start_date;
            $start      = $start_date->addDays(1)->addSeconds(-1)->getDate();

            $query->where('tbl.created_on', '<', $start);
            $query->where('tbl.created_on', '>', $days_back->addDays(-(int)$this->getState()->days_back)->getDate());
        }

        if ($state->user) {
            $query->where('tbl.created_by = :created_by')->bind(array('created_by' => $state->user));
        }

        if ($state->ip) {
            $query->where('tbl.ip ' . (is_array($state->ip) ? 'IN' : '=') . ' :ip')->bind(array('ip' => $state->ip));
        }

        // TODO: Implement a better way to exclude information based on package/name
        $query->where('tbl.name != :name')->bind(array('name' => 'session'));
    }
}