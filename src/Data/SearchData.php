<?php

namespace App\Data;;

class SearchData
{
    /**
     * @var integer
     */
    public int $page = 1;

    /**
     * @var string
     */
    public ?string $q = '';

    /**
     * @var \DateTimeImmutable|null
     */
    public ?\DateTimeImmutable $programDateAt = null;
}