<?php

namespace App\Entity;

use App\Repository\DoctorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=DoctorRepository::class)
 * @ORM\Table(name="`doctor`")
 * @UniqueEntity(fields={"fk_user"}, message="There is already an doctor with this user_id")
 */
class Doctor
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Ignore()
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="doctor")
     */
    private $reviews;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Ignore()
     * @ORM\OneToMany(targetEntity=Visit::class, mappedBy="doctor",  cascade={"persist", "remove"})
     */
    private $visits;

    /**
     * @Ignore()
     * @ORM\ManyToMany(targetEntity=DoctorMedicine::class, mappedBy="doctor")
     */
    private $doctorMedicines;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false,  unique=true)
     */
    private $fk_user;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->visits = new ArrayCollection();
        $this->doctorMedicines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Review[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setDoctor($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getDoctor() === $this) {
                $review->setDoctor(null);
            }
        }

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
            $visit->setDoctor($this);
        }

        return $this;
    }

    public function removeVisit(Visit $visit): self
    {
        if ($this->visits->removeElement($visit)) {
            // set the owning side to null (unless already changed)
            if ($visit->getDoctor() === $this) {
                $visit->setDoctor(null);
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
            $doctorMedicine->addDoctor($this);
        }

        return $this;
    }

    public function removeDoctorMedicine(DoctorMedicine $doctorMedicine): self
    {
        if ($this->doctorMedicines->removeElement($doctorMedicine)) {
            $doctorMedicine->removeDoctor($this);
        }

        return $this;
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
}
