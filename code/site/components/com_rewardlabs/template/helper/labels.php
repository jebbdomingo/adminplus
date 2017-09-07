<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsTemplateHelperLabels extends KTemplateHelperAbstract
{
    /**
     * Order status
     *
     * @param mixed $config
     *
     * @return string
     */
    public function orderStatus(array $config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'value' => null
        ));

        switch ($config->value) {
            case ComRewardlabsModelEntityOrder::STATUS_PAYMENT:
                $state = 'info';
                break;

            case ComRewardlabsModelEntityOrder::STATUS_PROCESSING:
                $state = 'info';
                break;

            case ComRewardlabsModelEntityOrder::STATUS_SHIPPED:
                $state = 'accent';
                break;

            case ComRewardlabsModelEntityOrder::STATUS_COMPLETED:
            case ComRewardlabsModelEntityOrder::STATUS_DELIVERED:
            case ComRewardlabsModelEntityOrder::STATUS_VERIFIED:
                $state = 'success';
                break;

            case ComRewardlabsModelEntityOrder::STATUS_CANCELLED:
            case ComRewardlabsModelEntityOrder::STATUS_PENDING:
                $state = 'error';
                break;
            
            default:
                $state = 'accent';
                break;
        }

        $label = ComRewardlabsTemplateHelperOrder::$status_messages[$config->value];
        $html  = '<span class="k-icon-badge k-icon--' . $state . '" aria-hidden="true"></span>';
        $html  .= "<span>{$label}</span>";

        return $html;
    }

    /**
     * Product status
     *
     * @param mixed $config
     *
     * @return string
     */
    public function productStatus(array $config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'value' => null
        ));

        switch ($config->value) {
            case ComRewardlabsTemplateHelperProduct::STATUS_ACTIVE:
                $state = 'success';
                break;

            case ComRewardlabsTemplateHelperProduct::STATUS_INACTIVE:
                $state = 'error';
                break;

            default:
                $state = 'accent';
                break;
        }

        $label = ComRewardlabsTemplateHelperProduct::$status_messages[$config->value];
        $html  = '<span class="k-icon-badge k-icon--' . $state . '" aria-hidden="true"></span>';
        $html  .= "<span>{$label}</span>";

        return $html;
    }
}
