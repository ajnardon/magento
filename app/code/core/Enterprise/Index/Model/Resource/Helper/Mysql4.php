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
 * @category    Enterprise
 * @package     Enterprise_Index
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Mysql resource helper
 *
 * @category    Enterprise
 * @package     Enterprise_Index
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Index_Model_Resource_Helper_Mysql4 extends Enterprise_Index_Model_Resource_Helper_Abstract
{
    /**
     * Set lock
     *
     * @param string $name
     * @return bool
     */
    public function setLock($name)
    {
        return (bool) $this->_getWriteAdapter()->query("SELECT GET_LOCK(?, ?);", array($name, self::LOCK_GET_TIMEOUT))
            ->fetchColumn();
    }

    /**
     * Release lock
     *
     * @param string $name
     * @return bool
     */
    public function releaseLock($name)
    {
        return (bool) $this->_getWriteAdapter()->query("SELECT RELEASE_LOCK(?);", array($name))->fetchColumn();
    }

    /**
     * Is lock exists
     *
     * @param string $name
     * @return bool
     */
    public function isLocked($name)
    {
        return (bool) $this->_getWriteAdapter()->query("SELECT IS_USED_LOCK(?);", array($name))->fetchColumn();
    }
}