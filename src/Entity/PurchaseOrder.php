<?php

namespace App\Entity;

use App\Repository\PurchaseOrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PurchaseOrderRepository::class)
 */
class PurchaseOrder
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adress;

    /**
     * @ORM\Column(type="string", length=255)z
     */
    private $pc;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;



    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="purchaseOrders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=PurchaseOrderProduct::class, mappedBy="PurchaseOrders")
     */
    private $purchaseOrderProducts;

    /**
     * @ORM\Column(type="integer")
     */
    private $total;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->purchaseOrderProducts = new ArrayCollection();
    }

   

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPc(): ?string
    {
        return $this->pc;
    }

    public function setPc(string $pc): self
    {
        $this->pc = $pc;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getValidated(): ?bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated): self
    {
        $this->validated = $validated;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|PurchaseOrderProduct[]
     */
    public function getPurchaseOrderProducts(): Collection
    {
        return $this->purchaseOrderProducts;
    }

    public function addPurchaseOrderProduct(PurchaseOrderProduct $purchaseOrderProduct): self
    {
        if (!$this->purchaseOrderProducts->contains($purchaseOrderProduct)) {
            $this->purchaseOrderProducts[] = $purchaseOrderProduct;
            $purchaseOrderProduct->setPurchaseOrders($this);
        }

        return $this;
    }

    public function removePurchaseOrderProduct(PurchaseOrderProduct $purchaseOrderProduct): self
    {
        if ($this->purchaseOrderProducts->contains($purchaseOrderProduct)) {
            $this->purchaseOrderProducts->removeElement($purchaseOrderProduct);
            // set the owning side to null (unless already changed)
            if ($purchaseOrderProduct->getPurchaseOrders() === $this) {
                $purchaseOrderProduct->setPurchaseOrders(null);
            }
        }

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    
}
