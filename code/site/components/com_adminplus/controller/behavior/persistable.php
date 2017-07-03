<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComAdminplusControllerBehaviorPersistable extends ComKoowaControllerBehaviorPersistable
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        // Handle entity after save
        $this->addCommandCallback('after.save', '_handleFailure');

        // Handle exception
        $this->getObject('exception.handler')->addExceptionCallback(array($this, 'handleException'), true);
    }

    /**
     * Check if the behavior is supported
     *
     * Overriden parent method, we need to support both get and post http methods
     *
     * @return  boolean  True on success, false otherwise
     */
    public function isSupported()
    {
        $mixer   = $this->getMixer();
        $request = $mixer->getRequest();

        if ($mixer->getRequest()->query->get('tmpl', 'cmd') !== 'component' && ($mixer instanceof KControllerModellable && $mixer->isDispatched() && $request->getFormat() === 'html')) {
            return true;
        }
        
        return false;
    }

    protected function _afterRead(KControllerContext $context)
    {
        $data = $context->user->get($this->_getStateKey($context));

        if ($data)
        {
            // Restore the data from user's session
            $context->result->setProperties($data);

            // The persisted data already served its purpose
            $context->user->remove($this->_getStateKey($context));
        }
    }

    protected function _handleFailure(KControllerContextInterface $context)
    {
        // Handle failed saving of entity
        if ($context->result->getStatus() == KDatabase::STATUS_FAILED) {
            $this->_persistEntity($context);
        }
    }

    public function handleException(Exception $exception)
    {
        // Handle exception from failed _actionAdd
        if ($exception instanceof KControllerExceptionActionFailed)
        {
            $context = $this->getContext();

            $this->_persistEntity($context);

            $context->getResponse()->setRedirect($context->request->getReferrer(), $exception->getMessage(), KControllerResponse::FLASH_ERROR);
            $context->getResponse()->send();
        }
    }

    protected function _persistEntity(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $entity = $this->getModel()->create($context->request->data->toArray());
        } else {
            $entity = $context->result;
        }

        $context->user->set($this->_getStateKey($context), $entity->getProperties());
    }

    protected function _getStateKey(KControllerContextInterface $context)
    {
        $controller = $context->getSubject()->getIdentifier();
        $view       = $this->getView()->getIdentifier();
        $layout     = $this->getView()->getLayout();

        return "{$controller}.{$view}.{$layout}.persisted.entity";
    }
}
