<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComAdminplusControllerToolbarCart extends ComKoowaControllerToolbarActionbar
{
    protected function _commandDeleteitem(KControllerToolbarCommand $command)
    {
        $translator    = $this->getObject('translator');
        $command->icon = 'k-icon-delete';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'deleteitem',
                'data-prompt' => $translator->translate('Item(s) will be deleted from the cart. Would you like to continue?')
            )
        ));

        $command->label = 'Delete item(s)';
    }

    protected function _commandCheckout(KControllerToolbarCommand $command)
    {
        $translator    = $this->getObject('translator');
        $command->icon = 'k-icon-dollar';
        $command->attribs->class->append(array('k-button--success'));

        $command->append(array(
            'attribs' => array(
                'data-action'     => 'checkout',
                'data-novalidate' => 'novalidate', // This is needed for koowa-grid and view without form
                'accesskey'       => 'c',
                'data-prompt' => $translator->translate('Created order cannot be undone. Would you like to continue?')
            )
        ));

        $command->label = 'Checkout';
    }

    protected function _afterRead(KControllerContextInterface $context)
    {
        $this->_addReadCommands($context);

        parent::_afterRead($context);
        
        $this->removeCommand('apply');
        $this->removeCommand('save');
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
            $this->addCommand('deleteitem', [
                'allowed' => $allowed,
            ]);
        }

        if ($controller->isEditable() && $controller->canSave()) {
            $this->addCommand('checkout', [
                'allowed' => $allowed,
            ]);
        }

        $account_id = ($controller->getRequest()->query->customer);
        $account = $this->getObject('com://admin/nucleonplus.model.accounts')->id($account_id)->fetch();

        if (in_array($account->status, array('new', 'pending'))) {
            $context->response->addMessage('This account is currently inactive', 'warning');
        }
    }
}
