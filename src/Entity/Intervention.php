<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\InterventionRepository;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: InterventionRepository::class)]
class Intervention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'])]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'])]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'])]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message_optionnel = null;

    #[ORM\Column(length: 255)]
    private ?string $sujet = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $users;


    public function __construct()
    {
        $this->dateCreation =   new \DateTime();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getMessageOptionnel(): ?string
    {
        return $this->message_optionnel;
    }

    public function setMessageOptionnel(?string $message_optionnel): self
    {
        $this->message_optionnel = $message_optionnel;

        return $this;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }
}
