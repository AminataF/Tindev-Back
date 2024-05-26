<?php

namespace App\Entity;

use App\Repository\UserMatchRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserMatchRepository::class)
 */
class UserMatch
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user_match_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15)
     * @Groups({"user_match_read"})
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="matchesGiven")
     * @Groups({"user_match_read"})
     */
    private $userMatcher;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="matchesReceived")
     * @Groups({"user_match_read"})
     */
    private $userMatched;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"user_match_read"})
     */
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUserMatcher(): ?User
    {
        return $this->userMatcher;
    }

    public function setUserMatcher(?User $userMatcher): self
    {
        $this->userMatcher = $userMatcher;

        return $this;
    }

    public function getUserMatched(): ?User
    {
        return $this->userMatched;
    }

    public function setUserMatched(?User $userMatched): self
    {
        $this->userMatched = $userMatched;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
