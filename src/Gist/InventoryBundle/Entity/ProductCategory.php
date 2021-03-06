<?php

namespace Gist\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use stdClass;

/**
 * @ORM\Entity
 * @ORM\Table(name="inv_product_category")
 */
class ProductCategory
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Brand")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
     */
    protected $brand;

    /**
     * @ORM\ManyToOne(targetEntity="Gist\MediaBundle\Entity\Upload")
     * @ORM\JoinColumn(name="photo_id", referencedColumnName="id")
     */
    protected $primary_photo;

    public function __construct()
    {
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getID()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setBrand($brand)
    {
        $this->brand = $brand;
        return $this;
    }

    public function getBrand()
    {
        return $this->brand;
    }

    public function setPrimaryPhoto($primary_photo)
    {
        $this->primary_photo = $primary_photo;
        return $this;
    }

    public function getPrimaryPhoto()
    {
        return $this->primary_photo;
    }

    public function toData()
    {
        $data = new stdClass();
        $data->id = $this->id;
        $data->name = $this->name;

        return $data;
    }
}

