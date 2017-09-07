<?php

/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsControllerToolbarProduct extends ComKoowaControllerToolbarActionbar
{
    /**
     * Sync Command
     *
     * @param KControllerToolbarCommand $command
     *
     * @return void
     */
    protected function _commandSync(KControllerToolbarCommand $command)
    {
        $command->icon = 'k-icon-loop-square';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'sync',
            )
        ));

        $command->label = 'Update inventory';
    }

    protected function _afterBrowse(KControllerContextInterface $context)
    {
        parent::_afterBrowse($context);

        $this->removeCommand('delete');

        $controller = $this->getController();
        $canSave    = ($controller->isEditable() && $controller->canSave());
        $allowed    = true;

        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        // Sync command
        if ($canSave)
        {
            $this->addCommand('sync', array(
                'allowed' => $allowed,
            ));
        }
    }

    protected function _afterRead(KControllerContextInterface $context)
    {
        parent::_afterRead($context);

        if (!$context->result->isNew()) {
            $context->response->addMessage('To add quantity and update the cost: Create a Purchase Order in QuickBooks Online', KControllerResponse::FLASH_NOTICE);
        }
    }
}