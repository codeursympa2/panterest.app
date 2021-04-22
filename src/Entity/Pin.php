<?php

namespace App\Entity;

use App\Entity\Traits\TimeStamp;
use App\Repository\PinRepository;
use Doctrine\ORM\Mapping as ORM;
//Pour ne pas que les champs soient vide
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PinRepository::class)
 * @ORM\Table(name="pins")
 *pour le controle de la date et heure de modification et ajout
 * @ORM\HasLifecycleCallbacks()
 * currentTimestam permet donner la date et l'heure actuel
 */
class Pin
{
    use TimeStamp;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champs est obligatoire")
     * @Assert\Length(min=3,minMessage="Saisir plus de de 3 caractere")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Ce champs est obligatoire")
     * @Assert\Length(min=10)
     */
    private $description;

   
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

}
