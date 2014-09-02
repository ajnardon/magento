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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Customer wishlist xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Wishlist extends Mage_Wishlist_Block_Customer_Wishlist
{
    /**
     * Render customer wishlist xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $wishlistXmlObj = Mage::getModel('xmlconnect/simplexml_element', '<wishlist></wishlist>');
        $hasMoreItems = 0;
        /**
         * Apply offset and count
         */
        $request= $this->getRequest();
        $offset = (int)$request->getParam('offset', 0);
        $count  = (int)$request->getParam('count', 0);
        $offset = $offset < 0 ? 0 : $offset;
        $count  = $count <= 0 ? 1 : $count;
        if ($offset + $count < $this->getWishlistItems()->getSize()) {
            $hasMoreItems = 1;
        }
        $this->getWishlistItems()->getSelect()->limit($count, $offset);

        $wishlistXmlObj->addAttribute('items_count', $this->getWishlistItemsCount());
        $wishlistXmlObj->addAttribute('has_more_items', $hasMoreItems);

        if ($this->hasWishlistItems()) {
            /**
             * @var Mage_Wishlist_Model_Mysql4_Product_Collection
             */
            foreach ($this->getWishlistItems() as $item) {
                /** @var $item Mage_Wishlist_Model_Item */
                $itemXmlObj = $wishlistXmlObj->addChild('item');

                $itemXmlObj->addChild('item_id', $item->getWishlistItemId());

                $itemXmlObj->addChild('entity_id', $item->getProductId());
                $itemXmlObj->addChild('entity_type_id', $item->getProduct()->getTypeId());
                $itemXmlObj->addChild('name', $wishlistXmlObj->xmlentities(strip_tags($item->getName())));
                $itemXmlObj->addChild('in_stock', (int)$item->getProduct()->isInStock());
                $itemXmlObj->addChild('is_salable', (int)$item->getProduct()->getIsSalable());
                /**
                 * If product type is grouped than it has options as its grouped items
                 */
                if ($item->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE) {
                    $item->getProduct()->setHasOptions(true);
                }
                $itemXmlObj->addChild('has_options', (int)$item->getProduct()->getHasOptions());

                $icon = $this->helper('catalog/image')->init($item->getProduct(), 'small_image')
                    ->resize(Mage::helper('xmlconnect/image')->getImageSizeForContent('product_small'));

                $iconXml = $itemXmlObj->addChild('icon', $icon);

                $file = Mage::helper('xmlconnect')->urlToPath($icon);
                $iconXml->addAttribute('modification_time', filemtime($file));

                $description = $wishlistXmlObj->xmlentities(strip_tags($item->getDescription()));
                $itemXmlObj->addChild('description', $description);

                $addedDate = $wishlistXmlObj->xmlentities($this->getFormatedDate($item->getAddedAt()));
                $itemXmlObj->addChild('added_date', $addedDate);

                if ($this->getChild('product_price')) {
                    $this->getChild('product_price')->setProduct($item->getProduct())
                        ->setProductXmlObj($itemXmlObj)
                        ->collectProductPrices();
                }

                if (!$item->getProduct()->getRatingSummary()) {
                    Mage::getModel('review/review')
                        ->getEntitySummary($item->getProduct(), Mage::app()->getStore()->getId());
                }
                $ratingSummary = (int)$item->getProduct()->getRatingSummary()->getRatingSummary();
                $itemXmlObj->addChild('rating_summary', round($ratingSummary / 10));
                $itemXmlObj->addChild('reviews_count', $item->getProduct()->getRatingSummary()->getReviewsCount());
            }
        }

        return $wishlistXmlObj->asNiceXml();
    }
}
