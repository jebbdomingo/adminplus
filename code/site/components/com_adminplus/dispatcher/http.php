<?php
/**
 * Nucleon Plus Admin
 *
 * @package     Nucleon Plus Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComAdminplusDispatcherHttp extends ComKoowaDispatcherHttp
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'controller' => 'accounts'
        ));
        
        parent::_initialize($config);
    }

    public function getRequest()
    {
        $request = parent::getRequest();
        $request->query->tmpl = 'koowa';

        return $request;
    }

    protected function _actionDispatch(KDispatcherContextInterface $context)
    {
        if (!in_array(6, $this->getUser()->getGroups()))
        {
            $message = 'Invalid access';
            $context->response->setRedirect($context->request->getBaseUrl()->toString(), $message, KControllerResponse::FLASH_WARNING);
            $context->response->send();
        }
        else return parent::_actionDispatch($context);
    }
}
