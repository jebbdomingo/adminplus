<?php
/**
 * Nucleon Plus Admin
 *
 * @package     Nucleon Plus Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsDispatcherHttp extends ComKoowaDispatcherHttp
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
        $query   = $request->query;

        $query->tmpl = 'koowa';

        if ($query->view == 'cart' && $query->customer)
        {
            $model = $this->getObject('com://site/rewardlabs.model.carts');
            $cart  = $model
                ->customer($query->customer)
                ->fetch()
            ;

            if (!count($cart))
            {
                $cart = $model->create(array('customer' => $query->customer));
                $cart->save();

                $id = $cart->id;
            }
            else $id = $cart->id;

            $query->id = (int) $id;
        }

        // Update payout status
        if ($query->view == 'dragonpay' && $query->api == 'payout' && $query->switch == 'postback' && $request->getMethod() == 'GET') {
            $this->_updatePayoutStatus($query);
        }

        return $request;
    }

    protected function _actionDispatch(KDispatcherContextInterface $context)
    {
        if (!array_intersect(array(6,7,8), $this->getUser()->getGroups()))
        {
            $message = 'Invalid access';
            $context->response->setRedirect($context->request->getBaseUrl()->toString(), $message, KControllerResponse::FLASH_WARNING);
            $context->response->send();
        }
        else return parent::_actionDispatch($context);
    }

    protected function _updatePayoutStatus($query)
    {
        $result = 'result=FAILED';

        try
        {
            $controller = $this->getObject('com://site/rewardlabs.controller.payoutprocessor');
            $controller->id($query->txnid);
            $controller->updatepayoutstatus($query->toArray());

            $result = 'result=OK';
        }
        catch (Exception $e)
        {
            // Transform error message to THIS_FORMAT
            $result = 'result=' . str_replace(' ', '_', strtoupper($e->getMessage()));
        }

        $request      = parent::getRequest();
        $url_query    = json_encode($request->query->toArray());
        $request_data = json_encode($request->toString());
        $referrer     = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

        $log = $this->getObject('com://site/rewardlabs.model.httplogs')->create(array(
            'referrer'     => $referrer,
            'request_data' => $request_data,
            'url_query'    => $url_query,
            'message'      => $result
        ));

        $log->save();

        exit("{$result}");
    }
}
