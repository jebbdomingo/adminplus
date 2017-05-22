<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComAdminplusControllerMember extends ComKoowaControllerModel
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
            $entities = $this->getModel()->fetch();
        } else {
            $entities = $context->result;
        }

        try
        {
            $translator = $this->getObject('translator');

            foreach($entities as $entity)
            {
                $entity->setProperties($context->request->data->toArray());
                $sponsorId = trim($entity->sponsor_id);

                if (!empty($sponsorId))
                {
                    $account = $this->getObject('com://admin/nucleonplus.model.accounts')->account_number($sponsorId)->fetch();

                    if (count($account) == 0)
                    {
                        throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Sponsor ID'));
                        $result = false;
                    }
                }
                else $context->request->data->sponsor_id = $entity->_account_sponsor_id;
            }

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
