<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Component\Comments;

use Nooku\Library;
use Nooku\Library\DatabaseQuerySelect;

/**
 * Comments Model
 *
 * @author  Terry Visser <https://nooku.assembla.com/profile/terryvisser>
 * @package Nooku\Component\Comments
 */
class ModelComments extends Library\ModelTable
{
	public function __construct(Library\ObjectConfig $config)
	{
		parent::__construct($config);

        $this->getState()
			->insert('table', 'string', $this->getIdentifier()->package)
			->insert('row'  , 'int')
            ->insert('search'  , 'cmd');
	}

    protected function _buildQueryColumns(Library\DatabaseQuerySelect $query)
    {
        parent::_buildQueryColumns($query);

        $query->columns(array(
            'created_by_name' => 'creator.name'
        ));
    }

    protected function _buildQueryJoins(Library\DatabaseQuerySelect $query)
    {
        $query->join(array('creator' => 'users'), 'creator.users_user_id = tbl.created_by');
    }
	
	protected function _buildQueryWhere(Library\DatabaseQuerySelect $query)
	{
		parent::_buildQueryWhere($query);
		
		if(!$this->getState()->isUnique())
        {
            $state = $this->getState();
            if ($state->search) {
                $query->where('(tbl.text LIKE :search)')->bind(array('search' => '%' . $state->search . '%'));
            }
			if($state->table) {
				$query->where('tbl.table = :table')->bind(array('table' => $state->table));
			}

			if($state->row) {
				$query->where('tbl.row = :row')->bind(array('row' => $state->row));
			}
		}
	}
}