<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user_read", "user_profile", "user_match_read", "browse_projects", "project_read", "browse_competence", "competence_read","user_browse", "latest_users", "add_project"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user_browse", "user_read", "user_profile", "user_match_read"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=20)
     * @Groups({"user_browse", "user_read", "latest_users", "user_match_read", "browse_projects", "project_read", "add_project", "user_profile"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=35)
     * @Groups({"user_browse", "user_read", "latest_users", "user_match_read", "user_profile"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     * @Groups({"user_browse", "user_read", "user_match_read", "user_profile"})

     */
    private $town;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"user_browse", "user_read", "user_profile"})
     */
    private $cv;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"user_browse", "user_read", "user_profile"})
     */
    private $github;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"user_browse", "user_read", "user_profile"})
     */
    private $linkedin;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"user_browse", "user_read", "user_profile"})
     */
    private $portfolio;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"user_browse", "user_read", "latest_users", "user_match_read", "user_profile"})
     */
    private $profilePicture;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"user_browse", "user_read", "user_profile"})
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"user_browse", "user_read", "user_profile"})
     */
    private $pricing;


    /**
     * 
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"user_browse", "user_read"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"user_browse"})
     */
    private $updateAt;

    /**
     * @ORM\OneToMany(targetEntity=Project::class, mappedBy="user")
     * @Groups({"user_browse", "user_read", "user_profile"})
     */
    private $projects;

    /**
     * @ORM\ManyToMany(targetEntity=Competence::class, mappedBy="user")
     * @Groups({"user_browse", "user_read", "user_match_read", "latest_users", "user_profile"})
     */
    private $competences;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="like2")
     */
    private $like1;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="like1")
     */
    private $like2;

    /**
     * @ORM\OneToMany(targetEntity=UserMatch::class, mappedBy="userMatcher")
     */
    private $matchesGiven;

    /**
     * @ORM\OneToMany(targetEntity=UserMatch::class, mappedBy="userMatched")
     */
    private $matchesReceived;

    /**
     * @ORM\ManyToOne(targetEntity=Job::class, inversedBy="users")
     * @Groups({"user_browse","user_read", "user_profile", "latest_users"})
     */
    private $job;

    /**
     * @ORM\ManyToOne(targetEntity=YearExperience::class, inversedBy="users")
     * @Groups({"user_browse", "user_read", "user_profile"})
     */
    private $yearExp;

    /**
     * @ORM\ManyToOne(targetEntity=Availability::class, inversedBy="users")
     * @Groups({"user_browse", "user_read", "latest_users", "user_profile"})
     */
    private $availability;



    public function __construct()
    {
        
        $this->projects = new ArrayCollection();
        $this->competences = new ArrayCollection();
        $this->like1 = new ArrayCollection();
        $this->like2 = new ArrayCollection();
        $this->matchesGiven = new ArrayCollection();
        $this->matchesReceived = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function setTown(string $town): self
    {
        $this->town = $town;

        return $this;
    }

    public function getCv(): ?string
    {
        return $this->cv;
    }

    public function setCv(?string $cv): self
    {
        $this->cv = $cv;

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

    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    public function setLinkedin(?string $linkedin): self
    {
        $this->linkedin = $linkedin;

        return $this;
    }

    public function getPortfolio(): ?string
    {
        return $this->portfolio;
    }

    public function setPortfolio(?string $portfolio): self
    {
        $this->portfolio = $portfolio;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

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

    public function getPricing(): ?int
    {
        return $this->pricing;
    }

    public function setPricing(?int $pricing): self
    {
        $this->pricing = $pricing;

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
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setUser($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getUser() === $this) {
                $project->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Competence>
     */
    public function getCompetences(): Collection
    {
        return $this->competences;
    }

    public function addCompetence(Competence $competence): self
    {
        if (!$this->competences->contains($competence)) {
            $this->competences[] = $competence;
            $competence->addUser($this);
        }

        return $this;
    }

    public function removeCompetence(Competence $competence): self
    {
        if ($this->competences->removeElement($competence)) {
            $competence->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getLike1(): Collection
    {
        return $this->like1;
    }

    public function addLike1(self $like1): self
    {
        if (!$this->like1->contains($like1)) {
            $this->like1[] = $like1;
        }

        return $this;
    }

    public function removeLike1(self $like1): self
    {
        $this->like1->removeElement($like1);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getLike2(): Collection
    {
        return $this->like2;
    }

    public function addLike2(self $like2): self
    {
        if (!$this->like2->contains($like2)) {
            $this->like2[] = $like2;
            $like2->addLike1($this);
        }

        return $this;
    }

    public function removeLike2(self $like2): self
    {
        if ($this->like2->removeElement($like2)) {
            $like2->removeLike1($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, UserMatch>
     */
    public function getMatchesGiven(): Collection
    {
        return $this->matchesGiven;
    }

    public function addMatchesGiven(UserMatch $matchesGiven): self
    {
        if (!$this->matchesGiven->contains($matchesGiven)) {
            $this->matchesGiven[] = $matchesGiven;
            $matchesGiven->setUserMatcher($this);
        }

        return $this;
    }

    public function removeMatchesGiven(UserMatch $matchesGiven): self
    {
        if ($this->matchesGiven->removeElement($matchesGiven)) {
            // set the owning side to null (unless already changed)
            if ($matchesGiven->getUserMatcher() === $this) {
                $matchesGiven->setUserMatcher(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserMatch>
     */
    public function getMatchesReceived(): Collection
    {
        return $this->matchesReceived;
    }

    public function addMatchesReceived(UserMatch $matchesReceived): self
    {
        if (!$this->matchesReceived->contains($matchesReceived)) {
            $this->matchesReceived[] = $matchesReceived;
            $matchesReceived->setUserMatched($this);
        }

        return $this;
    }

    public function removeMatchesReceived(UserMatch $matchesReceived): self
    {
        if ($this->matchesReceived->removeElement($matchesReceived)) {
            // set the owning side to null (unless already changed)
            if ($matchesReceived->getUserMatched() === $this) {
                $matchesReceived->setUserMatched(null);
            }
        }

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getYearExp(): ?YearExperience
    {
        return $this->yearExp;
    }

    public function setYearExp(?YearExperience $yearExp): self
    {
        $this->yearExp = $yearExp;

        return $this;
    }

    public function getAvailability(): ?Availability
    {
        return $this->availability;
    }

    public function setAvailability(?Availability $availability): self
    {
        $this->availability = $availability;

        return $this;
    }


}
