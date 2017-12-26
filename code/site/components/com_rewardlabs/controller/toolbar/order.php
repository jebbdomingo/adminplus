<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsControllerToolbarOrder extends ComKoowaControllerToolbarActionbar
{
    /**
     * Sync order Command
     *
     * @param KControllerToolbarCommand $command
     * @return void
     */
    protected function _commandSync(KControllerToolbarCommand $command)
    {
        $translator = $this->getObject('translator');

        $command->icon = 'k-icon-loop-square';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'sync'
            )
        ));

        $command->label = $translator->translate('Sync');
    }

    /**
     * After browse
     *
     * @param  KControllerContextInterface $context
     * @return void
     */
    protected function _afterBrowse(KControllerContextInterface $context)
    {
        parent::_afterBrowse($context);

        $controller = $this->getController();
        $allowed    = true;

        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        $this->removeCommand('new');
        $this->removeCommand('delete');

        // Mark order as in processing command
        if ($controller->isEditable() && $controller->canSave())
        {
            $this->addCommand('sync', [
                'allowed' => $allowed
            ]);
        }
    }
}
