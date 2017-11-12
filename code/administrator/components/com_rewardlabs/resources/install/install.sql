# Dump of table #__rewardlabs_accounts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__rewardlabs_accounts`;

CREATE TABLE `#__rewardlabs_accounts` (
  `rewardlabs_account_id` varchar(50) NOT NULL DEFAULT '',
  `app` varchar(255) DEFAULT NULL,
  `app_entity` varchar(255) DEFAULT NULL,
  `sponsor_id` varchar(50) DEFAULT NULL COMMENT 'Sponsor''s account_number',
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'Name of the user from users table',
  `type` varchar(50) NOT NULL COMMENT 'Name of the user from users table',
  `CustomerRef` int(11) NOT NULL COMMENT 'Customer reference in QBO',
  `PrintOnCheckName` varchar(100) NOT NULL,
  `note` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'new',
  `bank_name` varchar(50) NOT NULL,
  `bank_account_number` varchar(50) NOT NULL,
  `bank_account_name` varchar(50) NOT NULL,
  `bank_account_type` varchar(50) NOT NULL,
  `bank_account_branch` varchar(50) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `postal_code` varchar(255) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`rewardlabs_account_id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `nucleonplus_account_id` (`rewardlabs_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table #__rewardlabs_cartitems
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__rewardlabs_cartitems`;


# Dump of table #__rewardlabs_carts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__rewardlabs_carts`;



# Dump of table #__rewardlabs_cities
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__rewardlabs_cities`;



# Dump of table #__rewardlabs_configs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__rewardlabs_configs`;

CREATE TABLE `#__rewardlabs_configs` (
  `rewardlabs_config_id` int(11) NOT NULL AUTO_INCREMENT,
  `item` varchar(255) NOT NULL COMMENT 'Configuration item',
  `value` longtext NOT NULL COMMENT 'Configuration item value',
  PRIMARY KEY (`rewardlabs_config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `#__rewardlabs_configs` WRITE;
/*!40000 ALTER TABLE `#__rewardlabs_configs` DISABLE KEYS */;

INSERT INTO `#__rewardlabs_configs` (`rewardlabs_config_id`, `item`, `value`)
VALUES
  (1,'claim_request','enabled'),
  (2,'dragonpay','{\r\n    \"merchant_id\":\"NUCLEON\",\r\n    \"password\":\"UoJ9sRhC4JpRXGg\",\n    \"password_test\":\"eRGTsJ73DcjkL2J\",\r\n    \"payout_api_key\":\"e846ea3860e3f56edea70687091dfb7ce2f024c9\",\r\n    \"url_test\":\"http://test.dragonpay.ph/Pay.aspx\",\r\n    \"url_prod\":\"https://gw.dragonpay.ph/Pay.aspx\",\r\n    \"payout_url_prod\":\"https://gw.dragonpay.ph/DragonPayWebService/PayoutService.asmx\",\r\n    \"payout_url_test\":\"http://test.dragonpay.ph/DragonPayWebService/PayoutService.asmx\",\r\n    \"merchant_service_test\":\"http://test.dragonpay.ph/DragonPayWebService/MerchantService.asmx\",\r\n    \"merchant_service_prod\":\"https://gw.dragonpay.ph/DragonPayWebService/MerchantService.asmx\",\r\n    \"currency\":\"PHP\",\r\n    \"#__user\":\"jebb.domingo\",\r\n    \"#__password\":\"0053269nucleonplus\"\r\n}'),
  (3,'qbo_local','{\r\n    \"ACCOUNT_INVENTORY_ASSET\":\"124\",\r\n    \"ACCOUNT_COGS\":\"126\",\r\n    \"ACCOUNT_SALES_INCOME\":\"125\",\r\n    \"ACCOUNT_REVENUE\":\"151\",\r\n    \"ACCOUNT_ONLINE_PAYMENTS\":\"150\",\r\n    \"ACCOUNT_BANK_REF\":\"128\",\r\n    \"ACCOUNT_CHECKING_REF\":\"129\",\r\n    \"ACCOUNT_UNDEPOSITED_REF\":\"92\",\r\n    \"ACCOUNT_CHARGES\":\"131\",\r\n    \"ACCOUNT_EXPENSE_DELIVERY\":\"139\",\r\n    \"ACCOUNT_REBATES\":\"147\",\r\n    \"ACCOUNT_DIRECT_REFERRAL_BONUS\":\"146\",\r\n    \"ACCOUNT_PATRONAGE\":\"135\",\r\n    \"ACCOUNT_PATRONAGE_FLUSHOUT\":\"138\",\r\n    \"ACCOUNT_REFERRAL_DIRECT\":\"133\",\r\n    \"ACCOUNT_REFERRAL_DIRECT_FLUSHOUT\":\"140\",\r\n    \"ACCOUNT_REFERRAL_INDIRECT\":\"134\",\r\n    \"ACCOUNT_REFERRAL_INDIRECT_FLUSHOUT\":\"137\",\r\n    \"ACCOUNT_INCOME_SHIPPING\":\"SHIPPING_ITEM_ID\",\r\n    \"ACCOUNT_REBATES_EXPENSE\":\"163\",\r\n    \"ACCOUNT_DIRECT_REFERRAL_EXPENSE\":\"161\",\r\n    \"ACCOUNT_INDIRECT_REFERRAL_EXPENSE\":\"162\",\r\n    \"ACCOUNT_REBATES_LIABILITY\":\"158\",\r\n    \"ACCOUNT_DIRECT_REFERRAL_LIABILITY\":\"159\",\r\n    \"ACCOUNT_INDIRECT_REFERRAL_LIABILITY\":\"160\",\r\n    \"ACCOUNT_CHARGES_EXPENSE\":\"164\",\r\n    \"ACCOUNT_CHARGES_LIABILITY\":\"167\",\r\n    \"STORE_ANGONO\":\"1\",\r\n    \"UNILEVEL_COUNT\":\"20\",\r\n    \"CONFIG_CONSUMER_KEY\":\"qyprdramIqMIseQbKmPYEzituYJmPe\",\r\n    \"CONFIG_CONSUMER_SECRET\":\"hUbviBNbSHTKWgibmZWminuIdEllxdJ2BkqO3kJY\",\r\n    \"CONFIG_SANDBOX\":true,\r\n    \"CONFIG_OAUTH_URL\":\"http://joomla.box/nucleonplus/administrator/components/com_qbsync/quickbooks/qbo/docs/partner_platform/example_app_ipp_v3/oauth.php\",\r\n    \"CONFIG_OAUTH_SUCCESS_URL\":\"http://joomla.box/nucleonplus/administrator/components/com_qbsync/quickbooks/qbo/docs/partner_platform/example_app_ipp_v3/success.php\",\r\n    \"CONFIG_DSN\":\"mysqli://root:root@localhost/nucleonp_example_app_ipp_v3\",\r\n    \"CONFIG_ENCRYPTION_KEY\":\"bcde1234\",\r\n    \"CONFIG_USERNAME\":\"DO_NOT_CHANGE_ME\",\r\n    \"CONFIG_TENANT\":\"b7rmcac9tq\",\r\n    \"CONFIG_ONLINE_PURCHASE_ENABLED\":true\r\n}'),
  (4,'qbo_staging','{ \"ACCOUNT_REVENUE\":\"151\", \"ACCOUNT_ONLINE_PAYMENTS\"           :\"150\",\r     \"ACCOUNT_BANK_REF\"                  :\"128\",\r     \"ACCOUNT_CHECKING_REF\"              :\"129\",\r     \"ACCOUNT_UNDEPOSITED_REF\"           :\"92\",\r     \"ACCOUNT_CHARGES\"                   :\"131\",\r     \"ACCOUNT_EXPENSE_DELIVERY\"          :\"139\",\r     \"ACCOUNT_REBATES\"                   :\"147\",\r     \"ACCOUNT_DIRECT_REFERRAL_BONUS\"     :\"146\",\r     \"ACCOUNT_PATRONAGE\"                 :\"135\",\r     \"ACCOUNT_PATRONAGE_FLUSHOUT\"        :\"138\",\r     \"ACCOUNT_REFERRAL_DIRECT\"           :\"133\",\r     \"ACCOUNT_REFERRAL_DIRECT_FLUSHOUT\"  :\"140\",\r     \"ACCOUNT_REFERRAL_INDIRECT\"         :\"134\",\r     \"ACCOUNT_REFERRAL_INDIRECT_FLUSHOUT\":\"137\",\r     \"ACCOUNT_INCOME_SHIPPING\"           :\"SHIPPING_ITEM_ID\",\r     \"STORE_ANGONO\"                      :\"1\",\r     \"UNILEVEL_COUNT\"                    :\"20\",\r     \"CONFIG_CONSUMER_KEY\"               :\"qyprdramIqMIseQbKmPYEzituYJmPe\",\r     \"CONFIG_CONSUMER_SECRET\"            :\"hUbviBNbSHTKWgibmZWminuIdEllxdJ2BkqO3kJY\",\r     \"CONFIG_SANDBOX\"                    :true,\r     \"CONFIG_OAUTH_URL\"                  :\"http://www.nucleonplus.com/administrator/components/com_qbsync/quickbooks/qbo/docs/partner_platform/example_app_ipp_v3/oauth.php\",\r     \"CONFIG_OAUTH_SUCCESS_URL\"          :\"http://www.nucleonplus.com/administrator/components/com_qbsync/quickbooks/qbo/docs/partner_platform/example_app_ipp_v3/success.php\",\r     \"CONFIG_DSN\"                        :\"mysqli://admin:administrator@nucleonplus-test-db-instance.cjitsulcoyi6.us-west-2.rds.amazonaws.com/example_app_ipp_v3\",\r     \"CONFIG_ENCRYPTION_KEY\"             :\"bcde1234\",\r     \"CONFIG_USERNAME\"                   :\"DO_NOT_CHANGE_ME\",\r     \"CONFIG_TENANT\"                     :\"b7rmcac9tq\",\r     \"CONFIG_ONLINE_PURCHASE_ENABLED\"    :true\r }'),
  (5,'qbo_production','{\r     \"ACCOUNT_REVENUE\":\"111\",\r     \"ACCOUNT_ONLINE_PAYMENTS\":\"91\",\r     \"ACCOUNT_BANK_REF\":\"80\",\r     \"ACCOUNT_CHECKING_REF\":\"81\",\r     \"ACCOUNT_UNDEPOSITED_REF\":\"92\",\r     \"ACCOUNT_CHARGES\":\"90\",\r     \"ACCOUNT_REBATES\":\"110\",\r     \"ACCOUNT_DIRECT_REFERRAL_BONUS\":\"108\",\r     \"ACCOUNT_PATRONAGE\":\"82\",\r     \"ACCOUNT_PATRONAGE_FLUSHOUT\":\"86\",\r     \"ACCOUNT_REFERRAL_DIRECT\":\"85\",\r     \"ACCOUNT_REFERRAL_DIRECT_FLUSHOUT\":\"87\",\r     \"ACCOUNT_REFERRAL_INDIRECT\":\"89\",\r     \"ACCOUNT_REFERRAL_INDIRECT_FLUSHOUT\":\"88\",\r     \"ACCOUNT_INCOME_SHIPPING\":\"SHIPPING_ITEM_ID\",\r     \"STORE_ANGONO\":\"1\",\r     \"UNILEVEL_COUNT\":\"20\",\r     \"CONFIG_CONSUMER_KEY\":\"qyprdtp6RForlIeWspFVWidMBNogar\",\r     \"CONFIG_CONSUMER_SECRET\":\"Jy0UznNdAi7n5KHdlrzjO8FKyDBiMqaOI2HBpehD\",\r     \"CONFIG_SANDBOX\":false,\r     \"CONFIG_OAUTH_URL\":\"http://www.nucleonplus.com/administrator/components/com_qbsync/quickbooks/qbo/docs/partner_platform/example_app_ipp_v3/oauth.php\",\r     \"CONFIG_OAUTH_SUCCESS_URL\":\"http://www.nucleonplus.com/administrator/components/com_qbsync/quickbooks/qbo/docs/partner_platform/example_app_ipp_v3/success.php\",\r     \"CONFIG_DSN\":\"mysqli://admin:Nuc$1001@nucleonplus-db-instance.cjitsulcoyi6.us-west-2.rds.amazonaws.com/app_ipp_v3\",\r     \"CONFIG_ENCRYPTION_KEY\":\"bcde1234\",\r     \"CONFIG_USERNAME\":\"DO_NOT_CHANGE_ME\",\r     \"CONFIG_TENANT\":\"b7rmcac9w5\",\r     \"CONFIG_ONLINE_PURCHASE_ENABLED\":true\r }'),
  (6,'payout_run_date','2017-09-10'),
  (7,'payout_min_amount','5'),
  (8,'woocommerce_webhook_secret','pass');

/*!40000 ALTER TABLE `#__rewardlabs_configs` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table #__rewardlabs_employeeaccounts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__rewardlabs_employeeaccounts`;




# Dump of table #__rewardlabs_httplogs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__rewardlabs_httplogs`;

CREATE TABLE `#__rewardlabs_httplogs` (
  `rewardlabs_httplog_id` int(11) NOT NULL AUTO_INCREMENT,
  `referrer` varchar(255) DEFAULT '',
  `message` text,
  `url_query` text,
  `request_data` text,
  `created_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL,
  PRIMARY KEY (`rewardlabs_httplog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table #__rewardlabs_orderitems
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__rewardlabs_orderitems`;

CREATE TABLE `#__rewardlabs_orderitems` (
  `rewardlabs_orderitem_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `ItemRef` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_price` decimal(10,2) NOT NULL,
  `item_image` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `drpv` decimal(10,2) NOT NULL,
  `irpv` decimal(10,2) NOT NULL,
  `stockist` decimal(10,2) NOT NULL,
  `rebates` decimal(10,2) NOT NULL,
  `charges` decimal(10,2) NOT NULL,
  PRIMARY KEY (`rewardlabs_orderitem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table #__rewardlabs_orders
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__rewardlabs_orders`;

CREATE TABLE `#__rewardlabs_orders` (
  `rewardlabs_order_id` int(11) NOT NULL AUTO_INCREMENT,
  `app` varchar(255) DEFAULT NULL,
  `app_entity` varchar(255) DEFAULT NULL,
  `account` varchar(50) NOT NULL DEFAULT '',
  `order_status` varchar(50) NOT NULL,
  `invoice_status` varchar(50) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `shipping_method` varchar(50) NOT NULL,
  `tracking_reference` text NOT NULL,
  `recipient_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` int(11) NOT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'Philippines',
  `postal_code` int(11) NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL,
  `couriers` text NOT NULL,
  `payment_mode` smallint(6) NOT NULL COMMENT 'dragonpay',
  `payment_charge` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `note` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_on` datetime NOT NULL,
  `processed_by` int(11) NOT NULL,
  `processed_on` datetime NOT NULL,
  `shipped_by` int(11) NOT NULL,
  `shipped_on` datetime NOT NULL,
  `SalesReceiptRef` int(11) NOT NULL,
  PRIMARY KEY (`rewardlabs_order_id`),
  KEY `account_id` (`account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table #__rewardlabs_payouts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__rewardlabs_payouts`;

CREATE TABLE `#__rewardlabs_payouts` (
  `rewardlabs_payout_id` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(50) NOT NULL DEFAULT '',
  `direct_referrals` decimal(10,2) NOT NULL COMMENT 'Total amount of referral bonus and rebates',
  `indirect_referrals` decimal(10,2) NOT NULL COMMENT 'Total amount of referral bonus and rebates',
  `rebates` decimal(10,2) NOT NULL COMMENT 'Total amount of referral bonus and rebates',
  `status` varchar(50) DEFAULT NULL,
  `payout_method` varchar(255) NOT NULL DEFAULT 'pickup',
  `void` smallint(1) NOT NULL DEFAULT '0',
  `payout_service_result` int(11) NOT NULL COMMENT 'dragonpay',
  `date_processed` datetime NOT NULL,
  `date_disbursed` datetime NOT NULL,
  `run_date` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_on` datetime NOT NULL,
  PRIMARY KEY (`rewardlabs_payout_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table #__rewardlabs_provinces
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__rewardlabs_provinces`;



# Dump of table #__rewardlabs_rewards
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__rewardlabs_rewards`;

CREATE TABLE `#__rewardlabs_rewards` (
  `rewardlabs_reward_id` int(11) NOT NULL AUTO_INCREMENT,
  `item` int(11) NOT NULL COMMENT 'The Order''s Reward of other Member that pays the account_id',
  `account` varchar(50) NOT NULL DEFAULT '' COMMENT 'Account ID of the Referrer',
  `type` varchar(50) NOT NULL DEFAULT '' COMMENT 'dr => direct referral, ir => indirect referral. Based on the Reward of reward_id',
  `points` decimal(10,2) NOT NULL,
  `void` smallint(1) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_on` datetime NOT NULL,
  PRIMARY KEY (`rewardlabs_reward_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
