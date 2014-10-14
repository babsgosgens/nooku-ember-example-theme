<?php
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright      Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           https://github.com/nooku/nooku-platform for the canonical source repository
 */

namespace Nooku\Component\Attachments;

use Nooku\Library;

/**
 * Attachments Model
 *
 * @author  Steven Rombauts <http://github.com/stevenrombauts>
 * @package Nooku\Component\Attachments
 */
class ModelAttachments extends Library\ModelDatabase
{
    public function __construct(Library\ObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('row', 'int')
            ->insert('table', 'string');
    }

    protected function _buildQueryColumns(Library\DatabaseQuerySelect $query)
    {
        if (!$this->getState()->isUnique()) {
            $query->columns(array('count' => 'COUNT(relations.attachments_attachment_id)'))
                ->columns('table')
                ->columns('row');
        }

        parent::_buildQueryColumns($query);
    }

    protected function _buildQueryGroup(Library\DatabaseQuerySelect $query)
    {
        if (!$this->getState()->isUnique()) {
            $query->group('relations.attachments_attachment_id');
        }

        return parent::_buildQueryGroup($query);
    }

    protected function _buildQueryJoins(Library\DatabaseQuerySelect $query)
    {
        if (!$this->getState()->isUnique()) {
            $query->join(array('relations' => 'attachments_relations'), 'relations.attachments_attachment_id = tbl.attachments_attachment_id', 'LEFT');
        }

        parent::_buildQueryJoins($query);
    }

    protected function _buildQueryWhere(Library\DatabaseQuerySelect $query)
    {
        if (!$this->getState()->isUnique()) {
            if ($this->getState()->table) {
                $query->where('relations.table = :table')->bind(array('table' => $this->getState()->table));
            }

            if ($this->getState()->row) {
                $query->where('relations.row IN :row')->bind(array('row' => (array)$this->getState()->row));
            }
        }

        parent::_buildQueryWhere($query);
    }
}