<?php

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;

interface TagAwareInterface
{
    public function getTags(): ArrayCollection;

    public function removeTag(TagInterface $tag): static;

    public function addTag(TagInterface $tag): static;

    /**
     * Get a string with tags linked to the entity
     */
    public function getTagCodes(): array;
}
