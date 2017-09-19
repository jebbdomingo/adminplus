<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

/**
 * 
 * @author Jebb Domingo <https://github.com/jebbdomingo>
 */
interface ComRewardlabsAccountingTransferInterface
{
    /**
     * @param integer $entity_Id
     * @param decimal $amount
     *
     * @return mixed
     */
    public function depositOnlinePayment($entity_Id, $amount);

    /**
     * @param integer $entity_Id
     * @param decimal $amount
     *
     * @return mixed
     */
    public function rebatesCheck($entity_Id, $amount);

    /**
     * @param integer $entity_Id
     * @param decimal $amount
     *
     * @return mixed
     */
    public function directReferralCheck($entity_Id, $amount);

    /**
     * @param integer $entity_Id
     * @param decimal $amount
     *
     * @return mixed
     */
    public function indirectReferralCheck($entity_Id, $amount);
}