<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Billing agreement resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Billing_Agreement extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales/billing_agreement', 'agreement_id');
    }

    /**
     * Add order relation to billing agreement
     *
     * @param int $agreementId
     * @param int $orderId
     * @return Mage_Sales_Model_Resource_Billing_Agreement
     */
    public function addOrderRelation($agreementId, $orderId)
    {
        $this->_getWriteAdapter()->insert(
            $this->getTable('sales/billing_agreement_order'), array(
                'agreement_id'  => $agreementId,
                'order_id'      => $orderId
            )
        );
        return $this;
    }
}
