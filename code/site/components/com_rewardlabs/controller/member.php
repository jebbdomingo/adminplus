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
     * Validate sponsor id
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateSponsorId(KControllerContextInterface $context)
    {
        $data = $context->request->data;

        try
        {
            $translator = $this->getObject('translator');

            $sponsorId = trim($data->sponsor_id);

            if (!empty($sponsorId))
            {
                $account = $this->getObject('com://site/rewardlabs.model.accounts')->id($sponsorId)->fetch();

                if (count($account) == 0)
                {
                    throw new KControllerExceptionActionFailed($translator->translate('Invalid Sponsor ID'));
                    $result = false;
                }
            }
        }
        catch(Exception $e)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), KControllerResponse::FLASH_ERROR);
            $context->getResponse()->send();
        }
    }
}
