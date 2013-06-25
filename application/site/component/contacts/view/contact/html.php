<?php
/**
* @package     	Nooku_Server
* @subpackage  	Contacts
* @copyright	Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net)
* @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link			http://www.nooku.org
*/

use Nooku\Library;

/**
 * Contact Html View
 *
 * @author    	Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package     Nooku_Server
 * @subpackage  Contacts
 */
class ContactsViewContactHtml extends Library\ViewHtml
{
    public function render()
    {
        //Get the parameters
        $params = $this->getObject('application')->getParams();
        
        //Get the contact
        $contact = $this->getModel()->fetch();

        //Get the parameters of the active menu item
        if ($page = $this->getObject('application.pages')->getActive())
        {
            $menu_params = new JParameter( $page->params );
            if (!$menu_params->get( 'page_title')) {
                $params->set('page_title',	$contact->name);
            }
        }
        else $params->set('page_title',	$contact->name);

        //Set the page title
        //JFactory::getDocument()->setTitle( $params->get( 'page_title' ) );

        //Set the breadcrumbs
        $pathway = $this->getObject('application')->getPathway();

        if($page->getLink()->query['view'] == 'categories' )
        {
            $category = $contact->getCategory();

            $pathway->addItem($category->title, $this->getTemplate()->getHelper('route')->category(array('row' => $category)));
            $pathway->addItem($contact->name, '');
        }

        if($page->getLink()->query['view'] == 'contacts' ) {
            $pathway->addItem($contact->name, '');
        }

        $this->params = $params;

        return parent::render();
    }
}