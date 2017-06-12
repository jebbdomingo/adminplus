<?php
/**
 * @package     Nucleon Plus Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComAdminplusTemplateHelperOrder extends KTemplateHelperUi
{
    const STATUS_PENDING      = 'pending';
    const STATUS_PROCESSING   = 'processing';
    const STATUS_PAYMENT      = 'awaiting_payment';
    const STATUS_VERIFIED     = 'verified';
    const STATUS_SHIPPED      = 'shipped';
    const STATUS_DELIVERED    = 'delivered';
    const STATUS_COMPLETED    = 'completed';
    const STATUS_CANCELLED    = 'cancelled';

    const INVOICE_STATUS_SENT = 'sent';
    const INVOICE_STATUS_PAID = 'paid';

    const SHIPPING_METHOD_NA   = 'na';
    const SHIPPING_METHOD_XEND = 'xend';

    const PAYMENT_METHOD_CASH      = 'cash';
    const PAYMENT_METHOD_DRAGONPAY = 'dragonpay';

    public static $status_messages = array(
        self::STATUS_PENDING    => 'Pending',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_PAYMENT    => 'Awaiting payment',
        self::STATUS_VERIFIED   => 'Verified',
        self::STATUS_SHIPPED    => 'Shipped',
        self::STATUS_DELIVERED  => 'Delivered',
        self::STATUS_COMPLETED  => 'Completed',
    );

    public static $invoice_status_messages = array(
        self::INVOICE_STATUS_SENT => 'Sent',
        self::INVOICE_STATUS_PAID => 'Paid',
    );

    public static $payment_method_messages = array(
        self::PAYMENT_METHOD_CASH      => 'Cash',
        self::PAYMENT_METHOD_DRAGONPAY => 'Dragonpay',
    );
}
