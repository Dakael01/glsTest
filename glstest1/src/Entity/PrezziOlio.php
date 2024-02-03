<?php

namespace App\Entity;

use App\Repository\PrezziOlioRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrezziOlioRepository::class)]
class PrezziOlio{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $data = null;


    #[ORM\Column]
    private ?float $prezzo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(string $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getPrezzo(): ?float
    {
        return $this->prezzo;
    }

    public function setPrezzo(float $prezzo): static
    {
        $this->prezzo = $prezzo;

        return $this;
    }
}
