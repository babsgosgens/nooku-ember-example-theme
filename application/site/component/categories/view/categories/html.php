<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

use Nooku\Library;

/**
 * Categories Html View
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Component\Categories
 */
class CategoriesViewCategoriesHtml extends Library\ViewHtml
{
    protected function _fetchData(Library\ViewContext $context)
	{
		$context->data->params =  $params = $this->getObject('application.pages')->getActive()->getParams('page');
        parent::_fetchData($context);
	}
}