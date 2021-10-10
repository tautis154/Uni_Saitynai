<?php

namespace App\Entity;

use App\Repository\DoctorMedicineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DoctorMedicineRepository::class)
 */
class DoctorMedicine
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=Doctor::class, inversedBy="doctorMedicines")
     */
    private $doctor;

    /**
     * @ORM\ManyToMany(targetEntity=Medicine::class, inversedBy="doctorMedicines")
     */
    private $medicine;


    public function __construct()
    {
        $this->doctor = new ArrayCollection();
        $this->medicine = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Doctor[]
     */
    public function getDoctor(): Collection
    {
        return $this->doctor;
    }

    public function addDoctor(Doctor $doctor): self
    {
        if (!$this->doctor->contains($doctor)) {
            $this->doctor[] = $doctor;
        }

        return $this;
    }

    public function removeDoctor(Doctor $doctor): self
    {
        $this->doctor->removeElement($doctor);

        return $this;
    }

    /**
     * @return Collection|Medicine[]
     */
    public function getMedicine(): Collection
    {
        return $this->medicine;
    }

    public function addMedicine(Medicine $medicine): self
    {
        if (!$this->medicine->contains($medicine)) {
            $this->medicine[] = $medicine;
        }

        return $this;
    }

    public function removeMedicine(Medicine $medicine): self
    {
        $this->medicine->removeElement($medicine);

        return $this;
    }
}
