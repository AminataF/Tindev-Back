<?php

namespace App\Entity;

use App\Repository\YearExperienceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=YearExperienceRepository::class)
 */

class YearExperience
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"browse_yearExperience","user_browse", "user_read", "user_profile"})
     * 
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"browse_yearExperience","user_browse", "user_read", "user_profile"})
     */
    private $yearExp;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="yearExp")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYearExp(): ?string
    {
        return $this->yearExp;
    }

    public function setYearExp(?string $yearExp): self
    {
        $this->yearExp = $yearExp;

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
            $this->users[] = $user;
            $user->setYearExp($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getYearExp() === $this) {
                $user->setYearExp(null);
            }
        }

        return $this;
    }

}
