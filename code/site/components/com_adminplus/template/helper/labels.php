<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComAdminplusTemplateHelperLabels extends KTemplateHelperAbstract
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
            case ComNucleonplusModelEntityOrder::STATUS_PAYMENT:
                $state = 'info';
                break;

            case ComNucleonplusModelEntityOrder::STATUS_PROCESSING:
                $state = 'info';
                break;

            case ComNucleonplusModelEntityOrder::STATUS_SHIPPED:
                $state = 'accent';
                break;

            case ComNucleonplusModelEntityOrder::STATUS_COMPLETED:
            case ComNucleonplusModelEntityOrder::STATUS_DELIVERED:
            case ComNucleonplusModelEntityOrder::STATUS_VERIFIED:
                $state = 'success';
                break;

            case ComNucleonplusModelEntityOrder::STATUS_CANCELLED:
            case ComNucleonplusModelEntityOrder::STATUS_PENDING:
                $state = 'error';
                break;
            
            default:
                $state = 'accent';
                break;
        }

        $label = ComAdminplusTemplateHelperOrder::$status_messages[$config->value];
        $html  = '<span class="k-icon-badge k-icon--' . $state . '" aria-hidden="true"></span>';
        $html  .= "<span>{$label}</span>";

        return $html;
    }
}
