<?php

namespace App\Entity;

use App\Repository\PurchaseOrderProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PurchaseOrderProductRepository::class)
 */
class PurchaseOrderProduct
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $qty;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="purchaseOrderProducts")
     */
    private $products;

    /**
     * @ORM\ManyToOne(targetEntity=PurchaseOrder::class, inversedBy="purchaseOrderProducts")
     */
    private $PurchaseOrders;

    public function getPurchaseOrderProductId(): ?int
    {
        return $this->id;
    }

    public function getQty(): ?int
    {
        return $this->qty;
    }

    public function setQty(int $qty): self
    {
        $this->qty = $qty;

        return $this;
    }

    public function getProducts(): ?Product
    {
        return $this->products;
    }

    public function setProducts(?Product $products): self
    {
        $this->products = $products;

        return $this;
    }

    public function getPurchaseOrders(): ?PurchaseOrder
    {
        return $this->PurchaseOrders;
    }

    public function setPurchaseOrders(?PurchaseOrder $PurchaseOrders): self
    {
        $this->PurchaseOrders = $PurchaseOrders;

        return $this;
    }
}