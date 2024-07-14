<?php

namespace App\Builder;

use App\Entity\Region;
use morphos\Russian\GeographicalNamesInflection;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegionBuilder
{

    public function __construct(
        private SluggerInterface $slugger,
    )
    {
    }

    public function fromArray(array $data): Region
    {
        return (new Region())
            ->setName($data['address'])
            ->setPostalCode($data['postal_code'])
            ->setCountry($data['country'])
            ->setFederalDistrict($data['federal_district'])
            ->setRegionType($data['region_type'])
            ->setRegion($data['region'])
            ->setAreaType($data['area_type'])
            ->setArea($data['area'])
            ->setCityType($data['city_type'])
            ->setCity($data['city'])
            ->setSettlementType($data['settlement_type'])
            ->setSettlement($data['settlement'])
            ->setKlardId($data['kladr_id'])
            ->setFiasId($data['fias_id'])
            ->setFiasLevel($data['fias_level'])
            ->setCapitalMarker($data['capital_marker'] === 1 ? true : false)
            ->setOkato($data['okato'])
            ->setOktmo($data['oktmo'])
            ->setTaxOffice($data['tax_office'])
            ->setTimezone($data['timezone'])
            ->setGeoLat($data['geo_lat'])
            ->setGeoLon($data['geo_lon'])
            ->setPopulation($data['population'])
            ->setFoundationYear($data['foundation_year'])
            ->setCode($this->slugger->slug($data['address'])->lower()->toString())
            ->setCases(GeographicalNamesInflection::getCases($data['city']));
    }
}
