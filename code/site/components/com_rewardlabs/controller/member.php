<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsControllerMember extends ComKoowaControllerModel
{
    /**
     * Constructor
     *
     * @param KObjectConfig $config
     */
    public function __construct(KObjectConfig $config)
    {
        @ini_set('max_execution_time', 300);
        
        parent::__construct($config);

        $this->addCommandCallback('before.add', '_validateSponsorId');
        $this->addCommandCallback('before.edit', '_validateSponsorId');
        $this->addCommandCallback('after.delete', '_setRedirect');
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'customersyncable',
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Redirect callback
     *
     * @param KControllerContextInterface $context
     * @return void
     */
    protected function _setRedirect(KControllerContextInterface $context)
    {
        $identifier = $context->getSubject()->getIdentifier();
        $url        = JRoute::_(sprintf('index.php?option=com_%s', $identifier->package), false);

        $context->response->setRedirect($url);
    }

    /**
     * Validate sponsor id
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateSponsorId(KControllerContextInterface $context)
    {
        $result = true;

        if (!$context->result instanceof KModelEntityInterface) {
            $entity = $this->getModel()->create();
        } else {
            $entity = $context->result;
        }

        try
        {
            $translator = $this->getObject('translator');

            $entity->setProperties($context->request->data->toArray());
            $sponsorId = trim($entity->sponsor_id);

            if (!empty($sponsorId))
            {
                $account = $this->getObject('com://site/rewardlabs.model.accounts')->id($sponsorId)->fetch();

                if (count($account) == 0)
                {
                    throw new KControllerExceptionActionFailed($translator->translate('Invalid Sponsor ID'));
                    $result = false;
                }
            }
            else $context->request->data->sponsor_id = $entity->_account_sponsor_id;
        }
        catch(Exception $e)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
            $context->getResponse()->send();

            $result = false;
        }

        return $result;
    }
}
