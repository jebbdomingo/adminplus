<?php
/**
 * Reward Labs
 *
 * @package     Reward Labs
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComRewardlabsControllerWooorder extends ComRewardlabsControllerIntegrationabstract
{
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
            'identifier_column' => 'id',
            'model'             => 'com://site/rewardlabs.model.orders',
            'columns'           => array(),
        ));

        parent::_initialize($config);
    }

    protected function _actionRewards(KControllerContextInterface $context)
    {
        $request    = $context->request;
        $app        = $request->query->get('app', 'cmd');
        $action     = $request->query->get('action', 'cmd');
        $content    = $request->data ? $request->data : json_decode($request->getContent());
        $sponsor_id = null;
        $items      = array();

        // Dynamic column mapping
        if ('completed' != $content->status) {
            throw new Exception('Rewards creation aborted - order is not yet completed');
        }

        // Fetch sponsor id
        foreach ($content->meta_data as $datum)
        {
            if ('sponsor_id' == $datum['key'])
            {
                $sponsor_id = trim($datum['value']);

                if (!empty($sponsor_id))
                {
                    $account = $this->getObject('com://site/rewardlabs.model.accounts')
                        ->id($sponsor_id)
                        ->fetch();

                    if (count($account) == 0) {
                        throw new KControllerExceptionActionFailed('INVALID_SPONSOR_ID');
                    }
                }
            }
        }
        
        // Fetch rewards metadata
        $params  = array(
            'drpv' => 'drpv',
            'irpv' => 'irpv',
        );

        foreach ($content->line_items as $item)
        {
            if (isset($item['meta_data']))
            {
                $metadata = $item['meta_data'];
                $rewards  = array();

                $rewards['id']       = $item['id'];
                $rewards['quantity'] = $item['quantity'];

                foreach ($metadata as $datum)
                {
                    if (!array_key_exists($datum['key'], $params)) {
                        continue;
                    }

                    // Map columns
                    $column = $params[$datum['key']];
                    $rewards[$column] = $datum['value'];
                }

                $items[] = $rewards;
            }
        }

        if ($sponsor_id)
        {
            $data = array(
                'referrer' => $sponsor_id,
                'items'    => $items,
            );

            $this->getObject('com://site/rewardlabs.service.reward')->encode($data);
        }
    }
}
