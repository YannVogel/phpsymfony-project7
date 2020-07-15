<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"list"})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=1)
     * @Groups({"detail", "list"})
     * @Assert\NotBlank(message="Civility should not be blank.")
     * @Assert\Regex("/^m|f$/", message="Civility must be 'm' or 'f'.")
     */
    private string $civility;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"detail", "list"})
     * @Assert\NotBlank(message="First name should not be blank.")
     * @Assert\Regex("/^[a-zA-Z -éèàç]$/", message="First name should not contain special nor digit character.")
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"detail", "list"})
     * @Assert\NotBlank(message="Last name should not be blank.")
     * @Assert\Regex("/^[a-zA-Z -éèàç]$/", message="Last name should not contain special nor digit character.")
     */
    private string $lastName;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"detail"})
     * @Assert\NotBlank(message="Age should not be blank.")
     * @Assert\Regex("/\d/", message="Age must be an integer.")
     */
    private int $age;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"detail"})
     * @Assert\NotBlank(message="City should not be blank.")
     * @Assert\Regex("/^[a-zA-Z -éèàç']$/", message="City should not contain special nor digit character.")
     */
    private string $city;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Client $client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCivility(): ?string
    {
        return $this->civility;
    }

    public function setCivility(string $civility): self
    {
        $this->civility = $civility;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
