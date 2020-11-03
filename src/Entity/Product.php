<?php

namespace App\Entity;

use App\Entity\Category;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $unit;

    /**
     * @ORM\Column(type="float")
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=category::class, inversedBy="products")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="products")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $product_ref;

    /**
     * @ORM\ManyToMany(targetEntity=Pack::class, mappedBy="product")
     */
    private $packs;

    public function __construct()
    {
        $this->product_id = new ArrayCollection();
        $this->packs = new ArrayCollection();
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

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory(?Category $category)
    {
        $this->category = $category;

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
     * @return Collection|order[]
     */
    public function getProductId(): Collection
    {
        return $this->product_id;
    }

    public function addProductId(order $productId): self
    {
        if (!$this->product_id->contains($productId)) {
            $this->product_id[] = $productId;
        }

        return $this;
    }

    public function removeProductId(order $productId): self
    {
        $this->product_id->removeElement($productId);

        return $this;
    }

    public function getProductRef(): ?string
    {
        return $this->product_ref;
    }

    public function setProductRef(string $product_ref): self
    {
        $this->product_ref = $product_ref;

        return $this;
    }

    /**
     * @return Collection|Pack[]
     */
    public function getPacks(): Collection
    {
        return $this->packs;
    }

    public function addPack(Pack $pack): self
    {
        if (!$this->packs->contains($pack)) {
            $this->packs[] = $pack;
            $pack->addProduct($this);
        }

        return $this;
    }

    public function removePack(Pack $pack): self
    {
        if ($this->packs->removeElement($pack)) {
            $pack->removeProduct($this);
        }

        return $this;
    }
    
}
