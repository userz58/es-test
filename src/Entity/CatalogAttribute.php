<?php

namespace App\Entity;

use App\Catalog\Repository\CatalogAttributeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'catalog_attributes')]
#[ORM\Entity(repositoryClass: CatalogAttributeRepository::class)]
#[ORM\Index(name: "group_idx", fields: ["groupCode"])]
class CatalogAttribute
{
    #[ORM\OrderBy(['groupCode' => 'ASC', 'sortOrder' => 'ASC'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 100)]
    private ?string $type = null;

    #[ORM\Column(length: 100)]
    #[ORM\OrderBy(['groupCode' => 'ASC'])]
    private ?string $groupCode = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::INTEGER, options: ["default" => -1])]
    #[ORM\OrderBy(["sortOrder" => "ASC"])]
    private int $sortOrder = -1;

    #[ORM\Column(type: Types::BOOLEAN, options: ["default" => false])]
    private bool $useInGrid = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ["default" => false])]
    private bool $localizable = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ["default" => false])]
    private bool $scopable = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ["default" => false])]
    private bool $wysiwyg_enabled = false;

    public function __toString(): string
    {
        return $this->getCode();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getGroupCode(): ?string
    {
        return $this->groupCode;
    }

    public function setGroupCode(string $groupCode): self
    {
        $this->groupCode = $groupCode;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    public function isUseInGrid(): bool
    {
        return $this->useInGrid;
    }

    public function setUseInGrid(bool $useInGrid): self
    {
        $this->useInGrid = $useInGrid;

        return $this;
    }

    public function isLocalizable(): bool
    {
        return $this->localizable;
    }

    public function setLocalizable(bool $localizable): self
    {
        $this->localizable = $localizable;

        return $this;
    }

    public function isScopable(): bool
    {
        return $this->scopable;
    }

    public function setScopable(bool $scopable): self
    {
        $this->scopable = $scopable;

        return $this;
    }

    public function isWysiwygEnabled(): bool
    {
        return $this->wysiwyg_enabled;
    }

    public function setWysiwygEnabled(bool $wysiwyg_enabled): self
    {
        $this->wysiwyg_enabled = $wysiwyg_enabled;

        return $this;
    }
}
