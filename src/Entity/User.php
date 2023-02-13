<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 *  @UniqueEntity(
 * fields ={"email"},
 * message="Email déjà utilisé , connectez vous !"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Type("string")(
     *     message = "Ce champ ne peut contenir des caractère numeriques")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Type("string")(
     *     message = "Ce champ ne peut contenir des caractère numeriques")
     */

    private $firstname;

    /**
     * @ORM\Column(type="string", length=255 )
     * @Assert\Email(
     *     message = "L'email '{{ value }}' n'est pas une adresse mail valide.")
     */
    private $email;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $inscriptionDate;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="4" , minMessage="Votre mot de passe doit faire au minimum 4 caractères")
    
     */
    private $password;

    /** 
    * @Assert\EqualTo(propertyPath="password" , message="Mot de passe differents")
    */
    private $confirm_password;


    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resetToken;

    /**
     * @ORM\OneToMany(targetEntity=PurchaseOrder::class, mappedBy="user", orphanRemoval=true)
     */
    private $purchaseOrders;

    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="user" , orphanRemoval=true)
     */
    private $reviews;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $activation_token;

    public function __construct()
    {
        $this->purchaseOrders = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstname;
    }

    public function setFirstName(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getInscriptionDate(): ?\DateTimeInterface
    {
        return $this->inscriptionDate;
    }

    public function setInscriptionDate(?\DateTimeInterface $inscriptionDate): self
    {
        $this->inscriptionDate = $inscriptionDate;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirm_password;
    }

    public function setConfirmPassword(string $confirm_password): self
    {
        $this->confirm_password = $confirm_password;

        return $this;
    }

    public function __toString()
    {
        return $this->getName()." ".$this->getFirstName();
    }

    /**
     * @return Collection|PurchaseOrder[]
     */
    public function getPurchaseOrders(): Collection
    {
        return $this->purchaseOrders;
    }

    public function addPurchaseOrder(PurchaseOrder $purchaseOrder): self
    {
        if (!$this->purchaseOrders->contains($purchaseOrder)) {
            $this->purchaseOrders[] = $purchaseOrder;
            $purchaseOrder->setUser($this);
        }

        return $this;
    }

    public function removePurchaseOrder(PurchaseOrder $purchaseOrder): self
    {
        if ($this->purchaseOrders->contains($purchaseOrder)) {
            $this->purchaseOrders->removeElement($purchaseOrder);
            // set the owning side to null (unless already changed)
            if ($purchaseOrder->getUser() === $this) {
                $purchaseOrder->setUser(null);
            }
        }

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
            $review->setUser($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            // set the owning side to null (unless already changed)
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    // ...
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_ANONYMOUS
        $roles[] = 'ROLE_ANONYMOUS';
                   'ROLE_USER';
                   'ROLE_ADMIN';

        return array_unique($roles);
    }

        /**
         * Set the value of roles
         *
         * @return  self
         */ 
        public function setRoles($roles)
        {
                $this->roles = $roles;

                return $this;
        }

        public function getUsername(): ?string
        {
            return $this->roles[0];
        }
    
        public function setUsername(array $roles): self
        {
            $this->roles = $roles;
    
            return $this;
        }

  public function getSalt()
  {
      
  }
  public function eraseCredentials()
  {

  }

  public function getActivationToken(): ?string
  {
      return $this->activation_token;
  }

  public function setActivationToken(?string $activation_token): self
  {
      $this->activation_token = $activation_token;

      return $this;
  }

  public function getPlainPassword(): ?string
  {
      return $this->password;
  }

  public function setPlainPassword(?string $password): self
  {
      $this->$password = $password;

      return $this;
  }
  
}
