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
 * @package     Mage_Rss
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Review form block
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Block_Catalog_Review extends Mage_Rss_Block_Abstract
{

    /**
     * Cache tag constant for feed reviews
     *
     * @var string
     */
    const CACHE_TAG = 'block_html_rss_catalog_review';

    protected function _construct()
    {
        $this->setCacheTags(array(self::CACHE_TAG));
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_review');
        $this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
        $newurl = Mage::getUrl('rss/catalog/review');
        $title = Mage::helper('rss')->__('Pending product review(s)');

        $rssObj = Mage::getModel('rss/rss');
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                );
        $rssObj->_addHeader($data);

        $reviewModel = Mage::getModel('review/review');

        $collection = $reviewModel->getProductCollection()
            ->addStatusFilter($reviewModel->getPendingStatus())
            ->addAttributeToSelect('name', 'inner')
            ->setDateOrder();

        Mage::dispatchEvent('rss_catalog_review_collection_select', array('collection' => $collection));

        Mage::getSingleton('core/resource_iterator')
            ->walk($collection->getSelect(), array(array($this, 'addReviewItemXmlCallback')), array('rssObj'=> $rssObj, 'reviewModel'=> $reviewModel));
        return $rssObj->createRssXml();
    }

    public function addReviewItemXmlCallback($args)
    {
        $rssObj = $args['rssObj'];
        $reviewModel = $args['reviewModel'];
        $row = $args['row'];

        $store = Mage::app()->getStore($row['store_id']);
        $urlModel = Mage::getModel('core/url')->setStore($store);
        $productUrl = $urlModel->getUrl('catalog/product/view', array('id'=>$row['entity_id']));
        $reviewUrl = Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product_review/edit/', array('id'=>$row['review_id'], '_secure' => true, '_nosecret'=>true));
        $storeName = $store->getName();

        $description = '<p>'.
        $this->__('Product: <a href="%s">%s</a> <br/>',$productUrl,$row['name']).
        $this->__('Summary of review: %s <br/>',$row['title']).
        $this->__('Review: %s <br/>', $row['detail']).
        $this->__('Store: %s <br/>', $storeName ).
        $this->__('click <a href="%s">here</a> to view the review',$reviewUrl).
        '</p>'
        ;
        $data = array(
                'title'         => $this->__('Product: "%s" review By: %s',$row['name'],$row['nickname']),
                'link'          => 'test',
                'description'   => $description,
                );
        $rssObj->_addEntry($data);
    }
}
