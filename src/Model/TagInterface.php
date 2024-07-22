<?php

namespace App\Model;

interface TagInterface
{
    public function getId(): int|string|null;

    public function setCode(string $code): static;

    public function getCode(): ?string;
}
