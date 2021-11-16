<?php

namespace App\Entity;

use App\Repository\MedicineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=MedicineRepository::class)
 */
class Medicine
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @Ignore()
     * @ORM\OneToMany(targetEntity=Visit::class, mappedBy="medicine", cascade={"persist", "remove"},  fetch="EAGER")
     */
    private $visits;

    /**
     * @Ignore()
     * @ORM\ManyToMany(targetEntity=DoctorMedicine::class, mappedBy="medicine",  fetch="EAGER")
     */
    private $doctorMedicines;

    public function __construct()
    {
        $this->visits = new ArrayCollection();
        $this->doctorMedicines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return Collection|Visit[]
     */
    public function getVisits(): Collection
    {
        return $this->visits;
    }

    public function addVisit(Visit $visit): self
    {
        if (!$this->visits->contains($visit)) {
            $this->visits[] = $visit;
            $visit->setMedicine($this);
        }

        return $this;
    }

    public function removeVisit(Visit $visit): self
    {
        if ($this->visits->removeElement($visit)) {
            // set the owning side to null (unless already changed)
            if ($visit->getMedicine() === $this) {
                $visit->setMedicine(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DoctorMedicine[]
     */
    public function getDoctorMedicines(): Collection
    {
        return $this->doctorMedicines;
    }

    public function addDoctorMedicine(DoctorMedicine $doctorMedicine): self
    {
        if (!$this->doctorMedicines->contains($doctorMedicine)) {
            $this->doctorMedicines[] = $doctorMedicine;
            $doctorMedicine->addMedicine($this);
        }

        return $this;
    }

    public function removeDoctorMedicine(DoctorMedicine $doctorMedicine): self
    {
        if ($this->doctorMedicines->removeElement($doctorMedicine)) {
            $doctorMedicine->removeMedicine($this);
        }

        return $this;
    }
}
