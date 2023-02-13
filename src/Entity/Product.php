<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Review;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
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
    private $label;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     */
    private $releaseDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock;

    /**
     * @ORM\Column(type="float")
     */
    private $price;


    /**
     * @ORM\Column(type="boolean")
     */
    private $new;

   
    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="product", cascade={"remove"})
     */
    private $reviews;

    /**
     * @ORM\ManyToMany(targetEntity=Genre::class, inversedBy="products")
     */
    private $genres;

    /**
     * @ORM\ManyToMany(targetEntity=Platform::class, mappedBy="products")
     */
    private $platforms;

    /**
     * @ORM\ManyToOne(targetEntity=Conceptor::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $conceptor;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Picture::class, mappedBy="product" ,
     * orphanRemoval=true, cascade={"persist"})
     */
    private $pictures;

    /**
     * @ORM\OneToMany(targetEntity=PurchaseOrderProduct::class, mappedBy="products")
     */
    private $purchaseOrderProducts;

   
   
    /**
     * Permet de récupérer le commentaire d'un auteur par rapport à une annonce
     *
     * @param User $user
     * @return Review|null
     */
    public function getReviewsFromAuthor(User $user){

        // $user = $this->getReviews()->getUser();
        foreach($this->reviews as $review) {
            if($review->getUser() === $user) return $review;

            // else{
            //     return $this->getReviews();
            // }
        }

        return null;
    }


     /**
     * Permet de récupérer le commentaire d'un auteur par rapport à une annonce
     *
     * @param User $user
     * @return PurchaseOrderProduct|null
     */
    public function getPurchaseFromAuthor(User $user){

        // $user = $this->getReviews()->getUser();
        foreach($this->purchaseOrderProducts as $pop) {
            if($pop->getPurchaseOrders()->getUser() === $user) return $pop;

            // else{
            //     return $this->getReviews();
            // }
        }

        return null;
    }

    /**
     * Permet d'obtenir la moyenne globale des notes pour cette annonce
     *
     * @return float
     */
    public function getAvgNote() {
        // Calculer la somme des notations
        $sum = array_reduce($this->reviews->toArray(), function($total, $review) {
            return $total + $review->getNote();
        }, 0);

        // Faire la division pour avoir la moyenne
        if(count($this->reviews) > 0) return $sum / count($this->reviews);

        return 0;
    }

    
   

    public function __construct()
    {
        $this->carts = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->genres = new ArrayCollection();
        $this->platforms = new ArrayCollection();
        $this->pictures = new ArrayCollection();
        $this->purchaseOrderProducts = new ArrayCollection();

    }

    
    public function __toString()
    {
        return $this->label;
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }



    public function getNew(): ?bool
    {
        return $this->new;
    }

    public function setNew(bool $new): self
    {
        $this->new = $new;

        return $this;
    }



    /**
     * @return Collection|Review[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setProduct($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            // set the owning side to null (unless already changed)
            if ($review->getProduct() === $this) {
                $review->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Genre[]
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres[] = $genre;
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        if ($this->genres->contains($genre)) {
            $this->genres->removeElement($genre);
        }

        return $this;
    }

    /**
     * @return Collection|Platform[]
     */
    public function getPlatforms(): Collection
    {
        return $this->platforms;
    }

    public function addPlatform(Platform $platform): self
    {
        if (!$this->platforms->contains($platform)) {
            $this->platforms[] = $platform;
            $platform->addProduct($this);
        }

        return $this;
    }

    public function removePlatform(Platform $platform): self
    {
        if ($this->platforms->contains($platform)) {
            $this->platforms->removeElement($platform);
            $platform->removeProduct($this);
        }

        return $this;
    }

    public function getConceptor(): ?Conceptor
    {
        return $this->conceptor;
    }

    public function setConceptor(?Conceptor $conceptor): self
    {
        $this->conceptor = $conceptor;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Picture[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setProduct($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->contains($picture)) {
            $this->pictures->removeElement($picture);
            // set the owning side to null (unless already changed)
            if ($picture->getProduct() === $this) {
                $picture->setProduct(null);
            }
        }

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
            $purchaseOrderProduct->setProducts($this);
        }

        return $this;
    }

    public function removePurchaseOrderProduct(PurchaseOrderProduct $purchaseOrderProduct): self
    {
        if ($this->purchaseOrderProducts->contains($purchaseOrderProduct)) {
            $this->purchaseOrderProducts->removeElement($purchaseOrderProduct);
            // set the owning side to null (unless already changed)
            if ($purchaseOrderProduct->getProducts() === $this) {
                $purchaseOrderProduct->setProducts(null);
            }
        }

        return $this;
    }



}
