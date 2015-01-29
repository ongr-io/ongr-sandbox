<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\DemoMagentoBundle\Modifiers;

use ONGR\ConnectionsBundle\EventListener\AbstractImportModifyEventListener;
use ONGR\ConnectionsBundle\Pipeline\Item\AbstractImportItem;
use ONGR\ConnectionsBundle\Pipeline\ItemSkipException;
use ONGR\MagentoConnectorBundle\Entity\CatalogProductEntity;

/**
 * Modifies entities to match ongr product mapping.
 */

class ProductStockModifier extends AbstractImportModifyEventListener
{
    /**
     * @var int
     */
    protected $storeId;

    /**
     * @param int $storeId
     */
    public function __construct($storeId)
    {
        $this->storeId = $storeId;
    }

    /**
     * {@inheritdoc}
     */
    protected function modify(AbstractImportItem $eventItem)
    {
        /** @var CatalogProductEntity $entity */
        $entity = $eventItem->getEntity();
        $this->isItemInStock($entity);
    }


    /**
     * Checks if item is in stock. If it is imports.
     *
     * @param CatalogProductEntity $entity
     *
     * @throws ItemSkipException
     */
    public function isItemInStock(CatalogProductEntity $entity)
    {
        $priceArray = [];
        $prices = $entity->getPrices();
        foreach ($prices as $price) {
            if( $price->getPrice() !== null) {
                $priceArray[] =  $price->getPrice();
                break;
            }
        }

        if (count($priceArray) == 0) {
            throw new ItemSkipException('Product ' . $entity->getId() . ' is out of stock, so it wont be imported.');
        }
    }
}