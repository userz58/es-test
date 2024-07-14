<?php

namespace App\Entity;

use App\Catalog\Repository\CatalogFamilyRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Table(name: 'catalog_families')]
#[ORM\Entity(repositoryClass: CatalogFamilyRepository::class)]
class CatalogFamily
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $attributeAsLabel = null;

    #[ORM\Column(length: 100)]
    private ?string $attributeAsImage = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(nullable: true)]
    private \DateTimeImmutable $createdAt;

    #[Gedmo\Timestampable]
    #[ORM\Column(nullable: true)]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
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

    public function getAttributeAsLabel(): ?string
    {
        return $this->attributeAsLabel;
    }

    public function setAttributeAsLabel(string $attributeAsLabel): self
    {
        $this->attributeAsLabel = $attributeAsLabel;

        return $this;
    }

    public function getAttributeAsImage(): ?string
    {
        return $this->attributeAsImage;
    }

    public function setAttributeAsImage(string $attributeAsImage): self
    {
        $this->attributeAsImage = $attributeAsImage;

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
}
