<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Filter\OrSearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource]
#[ApiFilter(OrSearchFilter::class, properties: ['id' => 'exact', 'vendorCode' => 'partial', 'name' => 'partial'])]
class Product
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    public string $vendorCode = '';

    #[ORM\Column]
    #[Assert\NotBlank]
    public string $name = '';

    public function getId(): ?int
    {
        return $this->id;
    }
}
