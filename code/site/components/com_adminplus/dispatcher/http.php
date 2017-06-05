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

        if ($request->query->view == 'cart' && $request->query->customer)
        {
            $model = $this->getObject('com://admin/nucleonplus.model.carts');
            $cart  = $model
                ->customer($request->query->customer)
                ->fetch()
            ;

            if (!count($cart))
            {
                $cart = $model->create(array('customer' => $request->query->customer));
                $cart->save();

                $id = $cart->id;
            }
            else $id = $cart->id;

            $request->query->id = (int) $id;
        }

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
