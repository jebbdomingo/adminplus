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

        $view   = $query->get('view', 'cmd');
        $api    = $query->get('api', 'cmd');
        $switch = $query->get('switch', 'cmd');

        // Update payout status
        if ($view == 'dragonpay' && $api == 'payout' && $switch == 'postback' && $request->getMethod() == 'POST') {
            $this->_updatePayoutStatus($query);
        }


        if ($query->get('routed', 'int')) {
            $this->_runWebhook($request);
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
        $request_data = $request->toString();
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

    /**
     * Required URL query routed=1&app=nucleonplus&controller=wooproduct&action=add
     *
     * @param  [type] $request [description]
     *
     * @return [type]          [description]
     */
    protected function _runWebhook($request)
    {
        $controller = $request->query->get('controller', 'cmd');
        $action     = $request->query->get('action', 'cmd');
        $app        = $request->query->get('app', 'cmd');
        $result     = 'result=FAILED';

        if ($controller && $action && $app)
        {
            try
            {
                $controller = $this->getObject("com://site/rewardlabs.controller.{$controller}");
                $controller->setRequest($request);
                $controller->$action($request->data->toArray());
                $result = 'result=OK';
            }
            catch (Exception $e)
            {
                // Transform error message to THIS_FORMAT
                $result = 'result=' . str_replace(' ', '_', strtoupper($e->getMessage()));
            }
        }

        exit("{$result}");
    }
}
