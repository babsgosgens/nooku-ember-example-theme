<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

use Nooku\Library;
use Nooku\Component\Tags;

/**
 * Tag Controller
 *
 * @author  Tom Janssens <http://nooku.assembla.com/profile/tomjanssens>
 * @package Component\Tags
 */
abstract class TagsControllerTag extends Tags\ControllerTag
{
    public function getRequest()
    {
        $request = parent::getRequest();

        $request->query->access    = $this->getUser()->isAuthentic();
        $request->query->published = 1;

        return $request;
    }
}