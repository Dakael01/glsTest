<?php

namespace App\Entity;

use App\Repository\LibroRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LibroRepository::class)]
class Libro
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 255)]
    private ?string $titolo = null;
    #[ORM\Column(length: 255)]
    private ?string $autore = null;
    #[ORM\Column(length: 255)]
    private ?string $anno = null;
    #[ORM\Column(length: 255)]
    private ?string $genere = null;
    #[ORM\Column]
    private ?float $prezzo = 10.00;

    public function getId(): ?int{
        return $this->id;
    }

    public function getTitolo(): ?string{
        return $this->titolo;
    }

    public function setTitolo(string $titolo): self{
        $this->titolo = $titolo;
        return $this;
    }

    public function getAutore(): ?string{
        return $this->autore;
    }

    public function setAutore(string $autore): self{
        $this->autore = $autore;
        return $this;
    }

    public function getAnno(): ?string{
        return $this->anno;
    }

    public function setAnno(string $anno): self{
        $this->anno = $anno;
        return $this;
    }

    public function getGenere(): ?string{
        return $this->genere;
    }

    public function setGenere(string $genere): self{
        $this->genere = $genere;
        return $this;
    }

    public function getPrezzo(): ?float{
        return $this->prezzo;
    }

    public function setPrezzo(float $prezzo): self{
        $this->prezzo = $prezzo;
        return $this;
    }

    public function calcolaSconto($percentualeSconto){
        return $this->prezzo - ($this->prezzo * $percentualeSconto / 100);
    }
}
