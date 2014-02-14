<?php

namespace Demo\SiteBundle\Entity\Catalog;

use Doctrine\ORM\Mapping as ORM;
use SmartCore\Bundle\UnicatBundle\Model\ItemModel;

/**
 * @ORM\Entity
 * @ORM\Table(name="catalog_items")
 */
class Item extends ItemModel
{
    /**
     * @var Category[]
     *
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="items", cascade={"persist"})
     * @ORM\JoinTable(name="catalog_items_categories_relations")
     */
    protected $categories;

    /**
     * @var Category[]
     *
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="itemsSingle", cascade={"persist"})
     * @ORM\JoinTable(name="catalog_items_categories_relations_single")
     */
    protected $categoriesSingle;
}