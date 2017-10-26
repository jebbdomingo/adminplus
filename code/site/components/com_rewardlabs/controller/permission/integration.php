<?php
/**
 * Reward Labs
 *
 * @author      Jebb Domingo <[jebb.domingo@gmail.com]>
 * @package     Reward Labs
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsControllerPermissionIntegration extends ComKoowaControllerPermissionAbstract
{
    /**
     * Can add
     *
     * @return boolean
     */
    public function canAdd()
    {
        $config = $this->getObject('com://site/rewardlabs.model.configs')
            ->item('woocommerce_webhook_secret')
            ->fetch();

        $request   = $this->getRequest();
        $data      = $request->data;
        $signature = $request->getHeaders()->get('x-wc-webhook-signature');
        $digest    = base64_encode(hash_hmac('sha256', $request->getContent(), $config->value, true));

        if ($signature != $digest)
        {
            $error_message = 'FAIL_DIGEST_MISMATCH';

            if (getenv('HTTP_APP_ENV') != 'production') {
                $error_message .= "_{$digest}";
            }

            throw new KControllerExceptionRequestInvalid($error_message);
        }

        return true;
    }

    /**
     * Can edit
     *
     * @return boolean
     */
    public function canEdit()
    {
        return $this->canAdd();
    }

    /**
     * Can delete
     *
     * @return boolean
     */
    public function canDelete()
    {
        return $this->canAdd();
    }
}
