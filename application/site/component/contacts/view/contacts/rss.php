<?php
/**
 * @package     Nooku_Server
 * @subpackage  Contacts
 * @copyright	Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://www.nooku.org
 */

use Nooku\Library;

/**
 * Contacts Rss View
 *
 * @author    	Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package     Nooku_Server
 * @subpackage  Contacts
 */
class ContactsViewContactsRss extends Library\ViewRss
{
    public function render()
    {
        $contacts = $this->getModel()->fetch();

        if($contacts->isCategorizable())
        {
            $category = $contacts->getCategory();

            //Set the category image
            if (isset( $category->image ) && !empty($category->image))
            {
                $path = JPATH_IMAGES.'/stories/'.$category->image;
                $size = getimagesize($path);

                $category->image = (object) array(
                    'path'   => '/'.str_replace(JPATH_ROOT.DS, '', $path),
                    'width'  => $size[0],
                    'height' => $size[1]
                );
            }
        }

        $this->category = $category;

        return parent::render();
    }
}