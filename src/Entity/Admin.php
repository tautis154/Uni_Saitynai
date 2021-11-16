<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=AdminRepository::class)
 * @ORM\Table(name="`admin`")
 * @UniqueEntity(fields={"fk_user"}, message="There is already an admin with this user_id")
 */
class Admin
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"},  fetch="EAGER")
     * @ORM\JoinColumn(nullable=false, unique=true)
     */
    private $fk_user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFkUser(): ?User
    {
        return $this->fk_user;
    }

    public function setFkUser(User $fk_user): self
    {
        $this->fk_user = $fk_user;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
