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
 * @package     Mage_Wishlist
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Wishlist model resource
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Model_Resource_Wishlist extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Items count
     *
     * @var int
     */
    protected $_itemsCount             = null;

    /**
     * CustomerId field name
     *
     * @var string
     */
    protected $_customerIdFieldName    = 'customer_id';

    /**
     * Wishlist constructor
     *
     */
    protected function _construct()
    {
        $this->_init('wishlist/wishlist', 'wishlist_id');
    }

    /**
     * CustomerId field name getter
     *
     * @return string
     */
    public function getCustomerIdFieldName()
    {
        return $this->_customerIdFieldName;
    }

    /**
     * CustomerId field name setter
     *
     * @param string $fieldName
     * @return Mage_Wishlist_Model_Resource_Wishlist
     */
    public function setCustomerIdFieldName($fieldName)
    {
        $this->_customerIdFieldName = $fieldName;
        return $this;
    }

    /**
     * Fetch items count
     *
     * @param Mage_Wishlist_Model_Wishlist $wishlist
     * @return int
     */
    public function fetchItemsCount(Mage_Wishlist_Model_Wishlist $wishlist)
    {
        if (is_null($this->_itemsCount)) {
            $collection = $wishlist->getItemCollection()
                ->addStoreFilter()
                ->setVisibilityFilter();

            $this->_itemsCount = $collection->getSize();
        }

        return $this->_itemsCount;
    }
}
