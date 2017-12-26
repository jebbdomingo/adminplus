<?php
/**
 * @package     Nucleon Plus Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsTemplateHelperListbox extends ComKoowaTemplateHelperListbox
{
    /**
     * Account status
     * 
     * @param array $config [optional]
     * 
     * @return html
     */
    public function status(array $config = array())
    {
        $options = array();
        $options[] = $this->option(array('label' => 'New', 'value' => 'new'));
        $options[] = $this->option(array('label' => 'Active', 'value' => 'active'));
        $options[] = $this->option(array('label' => 'Suspended', 'value' => 'suspended'));

        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'status',
            'attribs'  => array('size' => false),
            'options'  => $options,
            'searchable' => false
        ));

        if($config->select2 && !$config->searchable)
        {
            $config->append(array(
                'select2_options' => array(
                    'options' => array(
                        'minimumResultsForSearch' => 'Infinity'
                    )
                )
            ));
        }

        return parent::optionlist($config);
    }

    /**
     * Bank account types list box
     * 
     * @param array $config [optional]
     * 
     * @return html
     */
    public function bankAccountTypes(array $config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'bank_account_type',
            'selected' => null,
            'select2'  => true,
            'filter'   => array(),
            'options'  => array(
                array('label' => 'Select', 'value' => null),
                array('label' => 'Savings', 'value' => 'savings'),
                array('label' => 'Check', 'value' => 'check'),
            ),
        ));

        if($config->select2 && !$config->searchable)
        {
            $config->append(array(
                'select2_options' => array(
                    'options' => array(
                        'minimumResultsForSearch' => 'Infinity'
                    )
                )
            ));
        }

        return parent::optionlist($config);
    }

    public function banks(array $config = array())
    {
        $options = array(
            array('label' => 'Asia United Bank CA/SA (limited)', 'value' => 'AUB'),
            array('label' => 'Banco de Oro CA/SA', 'value' => 'BDO'),
            array('label' => 'BPI CA/SA', 'value' => 'BPI'),
            array('label' => 'Chinabank CA/SA', 'value' => 'CBC'),
            array('label' => 'EastWest CA/SA', 'value' => 'EWB'),
            array('label' => 'Landbank CA/SA', 'value' => 'LBP'),
            array('label' => 'Metrobank CA/SA', 'value' => 'MBTC'),
            array('label' => 'PNB individual CA/SA', 'value' => 'PNB'),
            array('label' => 'RCBC CA/SA, RCBC Savings Bank CA/SA, RCBC MyWallet', 'value' => 'RCBC'),
            array('label' => 'Security Bank CA/SA', 'value' => 'SBC'),
            array('label' => 'Unionbank CA/SA, EON', 'value' => 'UBP'),
            array('label' => 'UCPB CA/SA', 'value' => 'UCPB'),
            array('label' => 'Cebuana Lhuillier Cash Pick-up', 'value' => 'CEBL'),
            array('label' => 'PSBank CA/SA', 'value' => 'PSB'),
            array('label' => 'Gcash', 'value' => 'GCSH'),
            array('label' => 'Smart Money', 'value' => 'SMRT'),
        );

        // Override options
        if (isset($config['banks']) && !empty($config['banks'])) {
            $options = $config['banks'];
        }

        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'bank',
            'selected' => null,
            'select2'  => true,
            'options'  => $options,
            'filter'   => array()
        ));

        if($config->select2 && !$config->searchable)
        {
            $config->append(array(
                'select2_options' => array(
                    'options' => array(
                        'minimumResultsForSearch' => 'Infinity'
                    )
                )
            ));
        }

        return parent::optionlist($config);
    }

    /**
     * Provides cities select box.
     *
     * @param  array|KObjectConfig $config An optional configuration array.
     * 
     * @return string The autocomplete select box.
     */
    public function cities($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'model'        => 'cities',
            'autocomplete' => true,
            'deselect'     => false,
            'prompt'       => '- '.$this->getObject('translator')->translate('Select').' -',
            'value'        => 'id',
            'label'        => 'name',
            'sort'         => '_name',
            'validate'     => false,
        ));

        return parent::_render($config);
    }

    /**
     * Generates order status list box
     * 
     * @param array $config [optional]
     * 
     * @return html
     */
    public function orderStatus(array $config = array())
    {
        $options = array();
        $options[] = $this->option(array('label' => 'Pending', 'value' => ComRewardlabsModelEntityOrder::STATUS_PENDING));
        $options[] = $this->option(array('label' => 'Awaiting Payment', 'value' => ComRewardlabsModelEntityOrder::STATUS_PAYMENT));
        $options[] = $this->option(array('label' => 'Verified', 'value' => ComRewardlabsModelEntityOrder::STATUS_VERIFIED));
        $options[] = $this->option(array('label' => 'Processing', 'value' => ComRewardlabsModelEntityOrder::STATUS_PROCESSING));
        $options[] = $this->option(array('label' => 'Shipped', 'value' => ComRewardlabsModelEntityOrder::STATUS_SHIPPED));
        $options[] = $this->option(array('label' => 'Delivered', 'value' => ComRewardlabsModelEntityOrder::STATUS_DELIVERED));
        $options[] = $this->option(array('label' => 'Cancelled', 'value' => ComRewardlabsModelEntityOrder::STATUS_CANCELLED));
        $options[] = $this->option(array('label' => 'Completed', 'value' => ComRewardlabsModelEntityOrder::STATUS_COMPLETED));

        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'order_status',
            'selected' => null,
            'options'  => $options,
            'filter'   => array()
        ));

        return parent::optionlist($config);
    }

    /**
     * Generates payout status list box
     * 
     * @param array $config [optional]
     * 
     * @return html
     */
    public function payoutStatus(array $config = array())
    {
        $options = array();

        foreach (ComRewardlabsTemplateHelperPayout::$payout_status_messages as $value => $label) {
            $options[] = $this->option(array('label' => $label, 'value' => $value));
        }

        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'status',
            'selected' => null,
            'options'  => $options,
            'filter'   => array()
        ));

        return parent::optionlist($config);
    }

    /**
     * Provides an accounts autocomplete select box.
     *
     * @param  array|KObjectConfig $config An optional configuration array.
     * @return string The autocomplete users select box.
     */
    public function accounts($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'model'    => 'accounts',
            'value'    => 'id',
            'label'    => 'user_name',
            'sort'     => 'id',
            'validate' => false
        ));

        return $this->_autocomplete($config);
    }

    /**
     * Generates product list box
     * 
     * @param array $config [optional]
     * 
     * @return html
     */
    public function productList($config = array())
    {
        $config = new KObjectConfigJson($config);

        $items   = $this->getObject('com://admin/qbsync.model.items')->fetch();
        $options = array();

        foreach ($items as $item)
        {
            if (!in_array($item->Type, ComQbsyncModelEntityItem::$item_types)) {
                continue;
            }

            $options[] = array('label' => $item->Name, 'value' => $item->ItemRef);
        }

        $config->append(array(
            'name'     => 'ItemRef',
            'selected' => null,
            'options'  => $options,
            'filter'   => array(),
            'select2'  => true,
            // 'deselect' => true,
        ));

        if($config->select2 && !$config->searchable)
        {
            $config->append(array(
                'select2_options' => array(
                    'options' => array(
                        'minimumResultsForSearch' => 'Infinity'
                    )
                )
            ));
        }

        return parent::optionlist($config);
    }

    /**
     * Generates payment method list box
     * 
     * @param array $config [optional]
     * 
     * @return html
     */
    public function paymentMethod(array $config = array())
    {
        $options = array();

        foreach (ComRewardlabsTemplateHelperOrder::$payment_method_messages as $value => $label) {
            $options[] = $this->option(array('label' => $label, 'value' => $value));
        }

        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'payment_method',
            'selected' => null,
            'options'  => $options,
            'filter'   => array()
        ));

        if($config->select2 && !$config->searchable)
        {
            $config->append(array(
                'select2_options' => array(
                    'options' => array(
                        'minimumResultsForSearch' => 'Infinity'
                    )
                )
            ));
        }

        return parent::optionlist($config);
    }

    /**
     * Generates product status list box
     * 
     * @param array $config [optional]
     * 
     * @return html
     */
    public function productStatus(array $config = array())
    {
        $options = array();

        foreach (ComRewardlabsTemplateHelperProduct::$status_messages as $value => $label) {
            $options[] = $this->option(array('label' => $label, 'value' => $value));
        }

        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'status',
            'selected' => null,
            'select2'  => true,
            'filter'   => array(),
            'options'  => $options
        ));

        if($config->select2 && !$config->searchable)
        {
            $config->append(array(
                'select2_options' => array(
                    'options' => array(
                        'minimumResultsForSearch' => 'Infinity'
                    )
                )
            ));
        }

        return parent::optionlist($config);
    }

    /**
     * Provides an accounts autocomplete select box.
     *
     * @param  array|KObjectConfig $config An optional configuration array.
     * @return string The autocomplete users select box.
     */
    public function orders($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'model'    => 'orders',
            'value'    => 'id',
            'label'    => 'id',
            'sort'     => 'id',
            'validate' => false
        ));

        return $this->_autocomplete($config);
    }
}
