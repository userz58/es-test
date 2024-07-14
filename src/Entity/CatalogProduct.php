<?php

namespace App\Entity;

use App\Catalog\Repository\CatalogProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Table(name: 'catalog_products')]
#[ORM\Entity(repositoryClass: CatalogProductRepository::class)]
#[ORM\Index(name: "sku_idx", columns: ['sku'])]
#[ORM\Index(name: "sku_enabled", columns: ['is_enabled'])]
#[ORM\Index(name: "sku_models", columns: ['parent'])]
class CatalogProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 100)]
    private ?string $sku = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[Gedmo\Slug(fields: ['name'])]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $parent = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?CatalogFamily $family = null;

    #[ORM\ManyToMany(targetEntity: CatalogCategory::class, inversedBy: 'products', fetch: 'EAGER')]
    #[ORM\JoinTable(name: 'catalog_product_category_references')]
    private Collection $categories;

    #[ORM\Column(type: 'boolean')]
    private ?bool $isEnabled = true;

    #[ORM\Column(type: 'json')]
    private array $values = [];

    #[ORM\Column(type: 'json')]
    private array $associations = [];

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[Gedmo\Timestampable]
    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(?string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function getValues(): array
    {
        return $this->values;
    }

    public function getFamily(): ?CatalogFamily
    {
        return $this->family;
    }

    public function setFamily(?CatalogFamily $family): self
    {
        $this->family = $family;

        return $this;
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    public function setParent(?string $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(CatalogCategory $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(CatalogCategory $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function selectCategoryBySlug(string $slug): ?CatalogCategory
    {
        $selected = null;

        foreach ($this->categories as $category) {
            if ($slug === $category->getSlug()) {
                return $category;
            }

            if (\str_starts_with($category->getSlug(), $slug)) {
                $selected = $category;
            }
        }

        return $selected;
    }

    public function setValues(array $values): self
    {
        $this->values = $values;

        return $this;
    }

    public function getAssociations(): array
    {
        return $this->associations;
    }

    public function setAssociations(array $associations): self
    {
        $this->associations = $associations;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setEnable(bool $enabled): self
    {
        $this->isEnabled = $enabled;

        return $this;
    }
}
