<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComAdminplusControllerToolbarAccount extends ComKoowaControllerToolbarActionbar
{
    protected function _commandCancel(KControllerToolbarCommand $command)
    {
        $controller = $this->getController();
        $command->href  = 'option=com_' . $controller->getIdentifier()->getPackage() . '&view=accounts';
        $command->label = 'Back';
    }

    protected function _commandNew(KControllerToolbarCommand $command)
    {
        $command->href  = 'view=member';
        $command->label = 'New Member';
    }

    protected function _commandOrder(KControllerToolbarCommand $command)
    {
        $command->icon  = 'k-icon-cart k-icon--success';
        $command->label = 'Create Order';

    }

    protected function _afterRead(KControllerContextInterface $context)
    {
        parent::_afterRead($context);

        $this->removeCommand('apply');
        $this->removeCommand('save');

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

        if ($controller->isEditable() && $controller->canSave()) {
            $this->addCommand('edit', [
                'href' => 'view=member&id=' . $context->result->user_id
            ]);
        }

        if ($controller->isEditable() && $controller->canSave() && !in_array($context->result->status, array('new', 'pending'))) {
            $this->addCommand('order', [
                'allowed' => $allowed,
                'href' => 'view=cart&customer=' . $context->result->id
            ]);
        }

        if (in_array($context->result->status, array('new', 'pending'))) {
            $context->response->addMessage('This account is currently inactive', 'warning');
        }
    }
}