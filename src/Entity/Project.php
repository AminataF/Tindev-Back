<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class Project
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"browse_projects","project_read", "user_read", "user_profile"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"browse_projects","project_read", "add_project", "user_read", "user_profile"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"browse_projects","project_read", "add_project","user_read", "user_profile"})
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"browse_projects","project_read", "add_project", "user_read", "user_profile"})
     */
    private $date;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"browse_projects","project_read", "add_project", "user_read", "user_profile"})
     */
    private $techno;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"browse_projects","project_read", "add_project", "user_read", "user_profile"})
     */
    private $github;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"browse_projects","project_read", "add_project", "user_read", "user_profile"})
     */
    private $urlProject;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"browse_projects","project_read", "add_project", "user_read", "user_profile"})
     */
    private $upload;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * 
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $UpdateAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="projects")
     * @Groups({"add_project", "browse_projects", "project_read"})
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?int
    {
        return $this->date;
    }

    public function setDate(?int $date): self
    {
        $this->date = $date;

        return $this;
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

    public function getGithub(): ?string
    {
        return $this->github;
    }

    public function setGithub(?string $github): self
    {
        $this->github = $github;

        return $this;
    }

    public function getUrlProject(): ?string
    {
        return $this->urlProject;
    }

    public function setUrlProject(?string $urlProject): self
    {
        $this->urlProject = $urlProject;

        return $this;
    }

    public function getUpload(): ?string
    {
        return $this->upload;
    }

    public function setUpload(?string $upload): self
    {
        $this->upload = $upload;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->UpdateAt;
    }

    public function setUpdateAt(?\DateTimeInterface $UpdateAt): self
    {
        $this->UpdateAt = $UpdateAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
