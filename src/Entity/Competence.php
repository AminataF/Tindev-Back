<?php

namespace App\Entity;

use App\Repository\CompetenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CompetenceRepository::class)
 */
class Competence
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user_browse", "user_read", "latest_users", "browse_competence", "competence_read", "add_competence", "user_profile"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Groups({"user_browse", "user_read", "latest_users", "browse_competence", "competence_read", "add_competence", "user_profile"})
     */
    private $techno;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="competences")
     * @Groups({"browse_competence", "competence_read", "add_competence"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="competence")
     * @Groups({"user_browse", "user_read", "latest_users", "browse_competence", "competence_read", "add_competence", "user_profile"})
     */
    private $category;

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTechno(): ?string
    {
        return $this->techno;
    }

    public function setTechno(?string $techno): self
    {
        $this->techno = $techno;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
