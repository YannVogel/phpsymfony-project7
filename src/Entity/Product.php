<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @UniqueEntity(
 *     "name",
 *     message="Un produit portant ce nom existe déjà !"
 * )
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"list"})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"detail", "list"})
     */
    private string $name;

    /**
     * @ORM\Column(type="float")
     * @Groups({"detail", "list"})
     */
    private float $price;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"detail", "list"})
     */
    private bool $dualSim;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"detail", "list"})
     */
    private bool $microSd;

    /**
     * @ORM\Column(type="float")
     * @Groups({"detail", "list"})
     */
    private float $screenSize;

    /**
     * @ORM\Column(type="float")
     * @Groups({"detail", "list"})
     */
    private float $cameraResolution;

    /**
     * @ORM\Column(type="float")
     * @Groups({"detail", "list"})
     */
    private float $weight;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"detail", "list"})
     */
    private bool $usbTypeC;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"detail", "list"})
     */
    private int $yearsOfWarranty;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"detail", "list"})
     */
    private bool $jackPlug;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"detail", "list"})
     */
    private bool $frontCamera;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"detail", "list"})
     */
    private bool $backCamera;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"detail", "list"})
     */
    private int $ram;

    /**
     * @ORM\ManyToOne(targetEntity=Battery::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"detail"})
     */
    private ?Battery $battery;

    /**
     * @ORM\ManyToOne(targetEntity=Brand::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"detail"})
     */
    private ?Brand $brand;

    /**
     * @ORM\ManyToOne(targetEntity=Os::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"detail"})
     */
    private ?Os $os;

    /**
     * @ORM\ManyToOne(targetEntity=ScreenResolution::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"detail"})
     */
    private ?ScreenResolution $screenResolution;

    /**
     * @ORM\ManyToOne(targetEntity=ScreenTechnology::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"detail"})
     */
    private ?ScreenTechnology $screenTechnology;

    /**
     * @ORM\ManyToOne(targetEntity=SimSize::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"detail"})
     */
    private ?SimSize $simSize;

    /**
     * @ORM\ManyToOne(targetEntity=Storage::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"list", "detail"})
     */
    private ?Storage $storage;

    /**
     * @ORM\ManyToMany(targetEntity=WirelessTechnology::class, inversedBy="products")
     * @Groups({"detail"})
     */
    private Collection $wirelessTechnology;

    /**
     * @ORM\OneToMany(targetEntity=Illustration::class, mappedBy="product", orphanRemoval=true)
     * @Groups({"list", "detail"})
     */
    private Collection $illustrations;

    /**
     * @ORM\ManyToMany(targetEntity=Color::class, mappedBy="product")
     * @Groups({"detail"})
     */
    private Collection $colors;

    public function __construct()
    {
        $this->wirelessTechnology = new ArrayCollection();
        $this->illustrations = new ArrayCollection();
        $this->colors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBattery(): ?Battery
    {
        return $this->battery;
    }

    public function setBattery(?Battery $battery): self
    {
        $this->battery = $battery;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getOs(): ?Os
    {
        return $this->os;
    }

    public function setOs(?Os $os): self
    {
        $this->os = $os;

        return $this;
    }

    public function getScreenResolution(): ?ScreenResolution
    {
        return $this->screenResolution;
    }

    public function setScreenResolution(?ScreenResolution $screenResolution): self
    {
        $this->screenResolution = $screenResolution;

        return $this;
    }

    public function getScreenTechnology(): ?ScreenTechnology
    {
        return $this->screenTechnology;
    }

    public function setScreenTechnology(?ScreenTechnology $screenTechnology): self
    {
        $this->screenTechnology = $screenTechnology;

        return $this;
    }

    public function getSimSize(): ?SimSize
    {
        return $this->simSize;
    }

    public function setSimSize(?SimSize $simSize): self
    {
        $this->simSize = $simSize;

        return $this;
    }

    public function getStorage(): ?Storage
    {
        return $this->storage;
    }

    public function setStorage(?Storage $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * @return Collection|WirelessTechnology[]
     */
    public function getWirelessTechnology(): Collection
    {
        return $this->wirelessTechnology;
    }

    public function addWirelessTechnology(WirelessTechnology $wirelessTechnology): self
    {
        if (!$this->wirelessTechnology->contains($wirelessTechnology)) {
            $this->wirelessTechnology[] = $wirelessTechnology;
        }

        return $this;
    }

    public function removeWirelessTechnology(WirelessTechnology $wirelessTechnology): self
    {
        if ($this->wirelessTechnology->contains($wirelessTechnology)) {
            $this->wirelessTechnology->removeElement($wirelessTechnology);
        }

        return $this;
    }

    /**
     * @return Collection|Illustration[]
     */
    public function getIllustrations(): Collection
    {
        return $this->illustrations;
    }

    public function addIllustration(Illustration $illustration): self
    {
        if (!$this->illustrations->contains($illustration)) {
            $this->illustrations[] = $illustration;
            $illustration->setProduct($this);
        }

        return $this;
    }

    public function removeIllustration(Illustration $illustration): self
    {
        if ($this->illustrations->contains($illustration)) {
            $this->illustrations->removeElement($illustration);
            // set the owning side to null (unless already changed)
            if ($illustration->getProduct() === $this) {
                $illustration->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Color[]
     */
    public function getColors(): Collection
    {
        return $this->colors;
    }

    public function addColor(Color $color): self
    {
        if (!$this->colors->contains($color)) {
            $this->colors[] = $color;
            $color->addProduct($this);
        }

        return $this;
    }

    public function removeColor(Color $color): self
    {
        if ($this->colors->contains($color)) {
            $this->colors->removeElement($color);
            $color->removeProduct($this);
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDualSim(): ?bool
    {
        return $this->dualSim;
    }

    public function setDualSim(bool $dualSim): self
    {
        $this->dualSim = $dualSim;

        return $this;
    }

    public function getMicroSd(): ?bool
    {
        return $this->microSd;
    }

    public function setMicroSd(bool $microSd): self
    {
        $this->microSd = $microSd;

        return $this;
    }

    public function getScreenSize(): ?float
    {
        return $this->screenSize;
    }

    public function setScreenSize(float $screenSize): self
    {
        $this->screenSize = $screenSize;

        return $this;
    }

    public function getCameraResolution(): ?float
    {
        return $this->cameraResolution;
    }

    public function setCameraResolution(float $cameraResolution): self
    {
        $this->cameraResolution = $cameraResolution;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getUsbTypeC(): ?bool
    {
        return $this->usbTypeC;
    }

    public function setUsbTypeC(bool $usbTypeC): self
    {
        $this->usbTypeC = $usbTypeC;

        return $this;
    }

    public function getYearsOfWarranty(): ?int
    {
        return $this->yearsOfWarranty;
    }

    public function setYearsOfWarranty(int $yearsOfWarranty): self
    {
        $this->yearsOfWarranty = $yearsOfWarranty;

        return $this;
    }

    public function getJackPlug(): ?bool
    {
        return $this->jackPlug;
    }

    public function setJackPlug(bool $jackPlug): self
    {
        $this->jackPlug = $jackPlug;

        return $this;
    }

    public function getFrontCamera(): ?bool
    {
        return $this->frontCamera;
    }

    public function setFrontCamera(bool $frontCamera): self
    {
        $this->frontCamera = $frontCamera;

        return $this;
    }

    public function getBackCamera(): ?bool
    {
        return $this->backCamera;
    }

    public function setBackCamera(bool $backCamera): self
    {
        $this->backCamera = $backCamera;

        return $this;
    }

    public function getRam(): ?int
    {
        return $this->ram;
    }

    public function setRam(int $ram): self
    {
        $this->ram = $ram;

        return $this;
    }
}
