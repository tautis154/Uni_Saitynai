<?php

namespace App\Entity;

use App\Repository\DoctorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=DoctorRepository::class)
 * @ORM\Table(name="`doctor`")
 */
class Doctor implements PasswordAuthenticatedUserInterface
{
    /**
     * @Groups("doctor")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("doctor")
     * @ORM\Column(type="string", length=180, unique=true)
     * @Serializer\Groups("newAction")
     */
    private $username;

    /**
     * @Groups("doctor")
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Ignore()
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="doctor")
     */
    private $reviews;

    /**
     * @Groups("doctor")
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Ignore()
     * @ORM\OneToMany(targetEntity=Visit::class, mappedBy="doctor")
     */
    private $visits;

    /**
     * @Ignore()
     * @ORM\ManyToMany(targetEntity=DoctorMedicine::class, mappedBy="doctor")
     */
    private $doctorMedicines;

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
     *
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @Ignore()
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @Ignore()
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
}
