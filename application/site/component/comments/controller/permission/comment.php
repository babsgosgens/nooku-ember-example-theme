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
 * Comment Controller Permission
 *
 * @author     Terry Visser <http://nooku.assembla.com/profile/terryvisser>
 * @package    Component\Comments
 */
class CommentsControllerPermissionComment extends ApplicationControllerPermissionAbstract
{
    public function canAdd()
    {
        $result = false;

        // Logged in users can add comments
        if($this->getUser()->getId()){
            $result = true;
        }

        return $result;
    }

    public function canEdit()
    {
        $result  = false;
        $comment = $this->getModel()->fetch();

        // If the user is manager he can moderator comments
        if($this->getUser()->getRole() >= 23) {
            $result = true;
        }

        // If the user is the creator of a comment he can moderator it
        if($comment->created_by == $this->getUser()->getId()) {
            $result = true;
        }

        return $result;
    }

    public function canDelete()
    {
        $comment = $this->getModel()->fetch();
        $result = false;

        // If the user is author he can delete comments
        if($this->getUser()->getRole() > 18) {
            $result = true;
        }

        // If the user is the creator of a comment he can delete it
        if($comment->created_by == $this->getUser()->getId()) {
            $result = true;
        }

        return $result;
    }
}