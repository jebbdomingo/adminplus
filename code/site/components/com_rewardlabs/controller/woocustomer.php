<?php
/**
 * Reward Labs
 *
 * @package     Reward Labs
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComRewardlabsControllerWoocustomer extends ComRewardlabsControllerIntegrationabstract
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
            'model'             => 'com://site/rewardlabs.model.members',
            'columns'           => array(
                'id'       => 'app_entity',
                'username' => 'username',
                'email'    => 'email',
            ),
            // 'behaviors' => array(
            //     'customersyncable' => array(
            //         'actions' => array(
            //             'after.addcustomer'    => 'add',
            //             'after.editcustomer'   => 'update',
            //             'after.deletecustomer' => 'update',
            //         )
            //     ),
            // ),
        ));

        parent::_initialize($config);
    }

    protected function _validate(KControllerContextInterface $context)
    {
        parent::_validate($context);

        $request = $context->request;
        $action  = $request->query->get('action', 'cmd');
        $content = $request->data ? $request->data : json_decode($request->getContent());

        // Ensure there's only single user account based on email regardless of the user being member of multiple online stores
        if ('add' == $action)
        {
            $email   = $content->email;
            $account = $this->getObject('com:koowa.model.users')->email($email)->count();

            if ($account) {
                throw new KControllerExceptionActionFailed("User creation aborted - email {$email} already exists");
            }
        }

        // Validate sponsor id
        if (isset($content->meta_data))
        {
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
                            throw new KControllerExceptionActionFailed('Invalid sponsor id');
                        }
                    }

                    break;
                }
            }
        }

        return true;
    }

    protected function _mapColumns(KControllerContextInterface $context)
    {
        $request = $context->request;
        $app     = $request->query->get('app', 'cmd');
        $action  = $request->query->get('action', 'cmd');
        $content = $request->data ? $request->data : json_decode($request->getContent());
        $data    = array();

        // Direct column mapping
        foreach ($this->_columns as $field => $column) {
            $data[$column] = isset($content->$field) ? $content->$field : null;
        }

        // Dynamic column mapping
        if ('edit' == $action)
        {
            // Fetch the identifier of the local copy of the entity
            $data['name'] = "{$content->first_name} {$content->last_name}";

            $account = $this->getObject('com://site/rewardlabs.model.accounts')->app($app)->app_entity($content->id)->fetch();
            $request->query->set($this->_identifier_column, $account->user_id);
        }
        elseif ('add' == $action)
        {
            // Dynamic column mapping
            $data['name'] = !empty($content->first_name) ? "{$content->first_name} {$content->last_name}" : $content->username;
        }

        $data['app']    = $app;
        $data['status'] = 'active';

        // Meta data column mapping
        if (isset($content->meta_data))
        {
            $params = array(
                'account_number' => 'account_number',
                'sponsor_id'     => 'sponsor_id',
            );

            foreach ($content->meta_data as $datum)
            {
                if (!array_key_exists($datum['key'], $params)) {
                    continue;
                }

                // Map columns
                $column = $params[$datum['key']];
                $data[$column] = $datum['value'];
            }
        }

        $context->request->setData($data);
    }
}
