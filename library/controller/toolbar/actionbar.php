<?php
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-platform for the canonical source repository
 */

namespace Nooku\Library;

/**
 * Model Controller Toolbar
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Nooku\Library\Controller
 */
class ControllerToolbarActionbar extends ControllerToolbarAbstract
{
    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   ObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'type'  => 'actionbar',
        ));

        parent::_initialize($config);
    }

	/**
	 * Add default toolbar commands and set the toolbar title
	 * .
	 * @param	ControllerContextInterface	$context A controller context object
	 */
    protected function _afterRead(ControllerContextInterface $context)
    {
        $controller = $this->getController();

        if($controller->isEditable() && $controller->canSave()) {
            $this->addCommand('save');
        }

        if($controller->isEditable() && $controller->canApply()) {
            $this->addCommand('apply');
        }

        if($controller->isEditable() && $controller->canCancel()) {
            $this->addCommand('cancel',  array('attribs' => array('data-novalidate' => 'novalidate')));
        }
    }

    /**
	 * Add default toolbar commands
	 * .
	 * @param	ControllerContextInterface	$context A controller context object
	 */
    protected function _afterBrowse(Command $context)
    {
        $controller = $this->getController();

        if($controller->canAdd()) {
            $this->addCommand('new');
        }

        if($controller->canDelete()) {
            $this->addCommand('delete');
        }
    }

    /**
     * New toolbar command
     *
     * @param   ControllerToolbarCommand $command  A ControllerToolbarCommand object
     * @return  void
     */
    protected function _commandNew(ControllerToolbarCommand $command)
    {
        $identifier = $this->getController()->getIdentifier();
        $command->href = 'component='.$identifier->package.'&view='.$identifier->name;
    }

    /**
     * Delete toolbar command
     *
     * @param   ControllerToolbarCommand $command  A ControllerToolbarCommand object
     * @return  void
     */
    protected function _commandDelete(ControllerToolbarCommand $command)
    {
        $command->append(array(
            'attribs' => array(
                'data-action' => 'delete',
                'data-prompt' => $this->getObject('translator')
                        ->translate('Deleted items will be lost forever. Would you like to continue?')
            )
        ));
    }

    /**
     * Enable toolbar command
     *
     * @param   ControllerToolbarCommand $commend  A ControllerToolbarCommand object
     * @return  void
     */
    protected function _commandEnable(ControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-publish';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'edit',
                'data-data'   => '{enabled:1}'
            )
        ));
    }

    /**
     * Disable toolbar command
     *
     * @param   ControllerToolbarCommand $command  A ControllerToolbarCommand object
     * @return  void
     */
    protected function _commandDisable(ControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-unpublish';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'edit',
                'data-data'   => '{enabled:0}'
            )
        ));
    }

    /**
     * Export toolbar command
     *
     * @param   ControllerToolbarCommand $command  A ControllerToolbarCommand object
     * @return  void
     */
    protected function _commandExport(ControllerToolbarCommand $command)
    {
        //Get the states
        $states = $this->getController()->getModel()->getState()->getValues();

        unset($states['limit']);
        unset($states['offset']);

        $states['format'] = 'csv';

        //Get the query options
        $query  = http_build_query($states);
        $option = $this->getIdentifier()->package;
        $view   = $this->getIdentifier()->name;

        $command->href = 'component='.$option.'&view='.$view.'&'.$query;
    }

    /**
     * Dialog toolbar command
     *
     * @param   ControllerToolbarCommand $command  A ControllerToolbarCommand object
     * @return  void
     */
    protected function _commandDialog(ControllerToolbarCommand $command)
    {
        $option = $this->getIdentifier()->package;

        $command->append(array(
            'width'   => '640',
            'height'  => '480',
        ))->append(array(
            'attribs' => array(
                'class' => array('modal'),
                'rel'   => '{handler: \'url\', ajaxOptions:{method:\'get\'}}',
            )
        ));
    }
}