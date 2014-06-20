<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Library;

/**
 * Column Database Schema
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Library\Database
 */
class DatabaseSchemaColumn
{
	/**
	 * Column name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Column type
	 *
	 * @var	string
	 */
	public $type;

	/**
	 * Column length
	 *
	 * @var integer
	 */
	public $length;

	/**
	 * Column scope
	 *
	 * @var string
	 */
	public $scope;

	/**
	 * Column default value
	 *
	 * @var string
	 */
	public $default;

	/**
	 * Required column
	 *
	 * @var bool
	 */
	public $required = false;

	/**
	 * Is the column a primary key
	 *
	 * @var bool
	 */
	public $primary = false;

	/**
	 * Is the column auto-incremented
	 *
	 * @var	bool
	 */
	public $autoinc = false;

	/**
	 * Is the column unique
	 *
	 * @var	bool
	 */
	public $unique = false;

	/**
	 * Related index columns
	 *
	 * @var	bool
	 */
	public $related = array();

    /**
     * Filter
     *
     * @var	string
     */
    public $filter;
}