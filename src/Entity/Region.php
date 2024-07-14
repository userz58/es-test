<?php

namespace App\Entity;

use App\Repository\RegionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 *  https://github.com/wapmorgan/Morphos
 */
#[ORM\Table(name: 'regions')]
#[ORM\Entity(repositoryClass: RegionRepository::class)]
class Region
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $name = null;

    #[ORM\Column(length: 6)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 50)]
    private ?string $country = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $federalDistrict = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $regionType = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $region = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $areaType = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $area = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $cityType = null;

    #[ORM\Column(length: 100)]
    private ?string $city = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $settlementType = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $settlement = null;

    #[ORM\Column(length: 255)]
    private ?string $klardId = null;

    #[ORM\Column(length: 255)]
    private ?string $fiasId = null;

    #[ORM\Column]
    private ?int $fiasLevel = null;

    #[ORM\Column]
    private bool $capitalMarker = false;

    #[ORM\Column(length: 30)]
    private ?string $okato = null;

    #[ORM\Column(length: 30)]
    private ?string $oktmo = null;

    #[ORM\Column]
    private ?int $taxOffice = null;

    #[ORM\Column(length: 10)]
    private ?string $timezone = null;

    #[ORM\Column(length: 11)]
    private ?string $geoLat = null;

    #[ORM\Column(length: 11)]
    private ?string $geoLon = null;

    #[ORM\Column]
    private ?int $population = null;

    #[ORM\Column(length: 4)]
    private ?int $foundationYear = null;

    #[ORM\Column]
    private array $cases = [];

    #[ORM\Column]
    private ?bool $enabled = true;

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function toggleEnabled(): static
    {
        $this->enabled = !$this->enabled;

        return $this;
    }

    public function getCases(): array
    {
        return $this->cases;
    }

    public function getCase(string $caseName): string
    {
        return $this->cases[$caseName];
    }

    public function setCases(array $cases): static
    {
        $this->cases = $cases;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getFederalDistrict(): ?string
    {
        return $this->federalDistrict;
    }

    public function setFederalDistrict(string $federalDistrict): static
    {
        $this->federalDistrict = $federalDistrict;

        return $this;
    }

    public function getRegionType(): ?string
    {
        return $this->regionType;
    }

    public function setRegionType(string $regionType): static
    {
        $this->regionType = $regionType;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getAreaType(): ?string
    {
        return $this->areaType;
    }

    public function setAreaType(?string $areaType): static
    {
        $this->areaType = $areaType;

        return $this;
    }

    public function getArea(): ?string
    {
        return $this->area;
    }

    public function setArea(?string $area): static
    {
        $this->area = $area;

        return $this;
    }

    public function getCityType(): ?string
    {
        return $this->cityType;
    }

    public function setCityType(?string $cityType): static
    {
        $this->cityType = $cityType;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getSettlementType(): ?string
    {
        return $this->settlementType;
    }

    public function setSettlementType(string $settlementType): static
    {
        $this->settlementType = $settlementType;

        return $this;
    }

    public function getSettlement(): ?string
    {
        return $this->settlement;
    }

    public function setSettlement(?string $settlement): static
    {
        $this->settlement = $settlement;

        return $this;
    }

    public function getKlardId(): ?string
    {
        return $this->klardId;
    }

    public function setKlardId(string $klardId): static
    {
        $this->klardId = $klardId;

        return $this;
    }

    public function getFiasId(): ?string
    {
        return $this->fiasId;
    }

    public function setFiasId(string $fiasId): static
    {
        $this->fiasId = $fiasId;

        return $this;
    }

    public function getFiasLevel(): ?int
    {
        return $this->fiasLevel;
    }

    public function setFiasLevel(int $fiasLevel): static
    {
        $this->fiasLevel = $fiasLevel;

        return $this;
    }

    public function isCapitalMarker(): bool
    {
        return $this->capitalMarker;
    }

    public function setCapitalMarker(bool $capitalMarker): static
    {
        $this->capitalMarker = $capitalMarker;

        return $this;
    }

    public function getOkato(): ?int
    {
        return $this->okato;
    }

    public function setOkato(string $okato): static
    {
        $this->okato = $okato;

        return $this;
    }

    public function getOktmo(): ?string
    {
        return $this->oktmo;
    }

    public function setOktmo(string $oktmo): static
    {
        $this->oktmo = $oktmo;

        return $this;
    }

    public function getTaxOffice(): ?int
    {
        return $this->taxOffice;
    }

    public function setTaxOffice(int $taxOffice): static
    {
        $this->taxOffice = $taxOffice;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getGeoLat(): ?string
    {
        return $this->geoLat;
    }

    public function setGeoLat(string $geoLat): static
    {
        $this->geoLat = $geoLat;

        return $this;
    }

    public function getGeoLon(): ?string
    {
        return $this->geoLon;
    }

    public function setGeoLon(string $geoLon): static
    {
        $this->geoLon = $geoLon;

        return $this;
    }

    public function getPopulation(): ?int
    {
        return $this->population;
    }

    public function setPopulation(int $population): static
    {
        $this->population = $population;

        return $this;
    }

    public function getFoundationYear(): ?int
    {
        return $this->foundationYear;
    }

    public function setFoundationYear(int $foundationYear): static
    {
        $this->foundationYear = $foundationYear;

        return $this;
    }
}
