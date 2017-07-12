<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComAdminplusControllerToolbarMember extends ComKoowaControllerToolbarActionbar
{
    protected function _commandActivate(KControllerToolbarCommand $command)
    {
        $command->icon  = 'k-icon-plus k-icon--success';
        $command->label = 'Activate';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'activate',
            )
        ));
    }

    protected function _afterRead(KControllerContextInterface $context)
    {
        parent::_afterRead($context);

        $this->_addReadCommands($context);
    }

    /**
     * Add read view toolbar buttons
     *
     * @param KControllerContextInterface $context
     *
     * @return void
     */
    protected function _addReadCommands(KControllerContextInterface $context)
    {
        $controller = $this->getController();
        $allowed    = true;

        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        if ($controller->isEditable() && $controller->canSave() && in_array($context->result->_account_status, array('new', 'pending'))) {
            $this->addCommand('activate', [
                'allowed' => $allowed
            ]);
        }

        if ($controller->isEditable() && ($controller->canSave() & $controller->canDelete()) && in_array($context->result->_account_status, array('active'))) {
            $this->addCommand('delete', [
                'allowed' => $allowed
            ]);
        }

        if (in_array($context->result->_account_status, array('new', 'pending'))) {
            $context->response->addMessage('This account is currently inactive', 'warning');
        }
    }
}
