<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *     "mail",
 *     message="A client with this mail already exists."
 * )
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
     * @Assert\NotBlank(
     *     normalizer="trim",
     *     message="Civility should not be blank."
     * )
     * @Assert\Regex("/^m|f$/", message="Civility must be 'm' or 'f'.")
     */
    private string $civility;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"detail", "list"})
     * @Assert\NotBlank(
     *     normalizer="trim",
     *     message="First name should not be blank.")
     * @Assert\Regex("/^[a-zA-Z -éèàç]+$/", message="First name should not contain special nor digit character.")
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"detail", "list"})
     * @Assert\NotBlank(
     *     normalizer="trim",
     *     message="Last name should not be blank.")
     * @Assert\Regex("/^[a-zA-Z -éèàç]+$/", message="Last name should not contain special nor digit character.")
     */
    private string $lastName;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"detail"})
     * @Assert\NotBlank(
     *     normalizer="trim",
     *     message="Age should not be blank.")
     * @Assert\Regex("/^\d+$/", message="Age must be an integer.")
     */
    private int $age;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"detail"})
     * @Assert\NotBlank(
     *     normalizer="trim",
     *     message="City should not be blank.")
     * @Assert\Regex("/^[a-zA-Z -éèàç']+$/", message="City should not contain special nor digit character.")
     */
    private string $city;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Client $client;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"detail"})
     * @Assert\NotBlank(
     *     normalizer="trim",
     *     message="Mail should not be blank.")
     * @Assert\Email(
     *     message="Please provide a proper mail."
     * )
     */
    private string $mail;

    /**
     * @Groups({"detail", "list"})
     */
    private string $_links;

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

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getLinksMethods() : array
    {
        return [
            'self' => '/clients/' . $this->getClient()->getId() . '/users/' . $this->getId(),
            'delete' => '/clients/' . $this->getClient()->getId() . '/users/' . $this->getId(),
        ];
    }

    public function getLinks() : array
    {
        $links = [];
        foreach ($this->getLinksMethods() as $action => $uri) {
            $links = array_merge($links, [
                    $action => [ 'href' => $uri]
            ]);
        }

        return $links;
   }
}
