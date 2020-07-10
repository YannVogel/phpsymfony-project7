<?php

namespace App\DataFixtures;

use App\Entity\Battery;
use App\Entity\Brand;
use App\Entity\Color;
use App\Entity\Illustration;
use App\Entity\Os;
use App\Entity\Product;
use App\Entity\ScreenResolution;
use App\Entity\ScreenTechnology;
use App\Entity\SimSize;
use App\Entity\Storage;
use App\Entity\WirelessTechnology;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture
{
    private const NUMBER_OF_PRODUCT = 100;
    private DataProvider $dataProvider;
    private array $batteryCapacities;
    private array $wirelessTechnologyNames;
    private array $brandNames;
    private array $osNames;
    private array $screenResolutionSizes;
    private array $screenTechnologyNames;
    private array $colorNames;
    private array $simSizeNames;
    private array $storageCapacities;

    public function __construct(DataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
        $this->batteryCapacities = $this->dataProvider->getArrayOfBatteryCapacities();
        $this->wirelessTechnologyNames= $this->dataProvider->getArrayOfWirelessTechnologyNames();
        $this->brandNames = $this->dataProvider->getArrayOfBrandNames();
        $this->osNames = $this->dataProvider->getArrayOfOsNames();
        $this->screenResolutionSizes = $this->dataProvider->getArrayOfScreenResolutionSizes();
        $this->screenTechnologyNames = $this->dataProvider->getArrayOfScreenTechnologyNames();
        $this->colorNames = $this->dataProvider->getArrayOfColorNames();
        $this->simSizeNames = $this->dataProvider->getArrayOfSimSizeName();
        $this->storageCapacities = $this->dataProvider->getArrayOfStorageCapacities();
    }

    public function load(ObjectManager $manager)
    {
        if (self::NUMBER_OF_PRODUCT < 1) {
            throw new \Exception("NUMBER_OF_PRODUCT must be a positive integer.");
        }

        foreach ($this->batteryCapacities as $batteryCapacity) {
            $battery = new Battery();
            $battery->setCapacity($batteryCapacity);

            $manager->persist($battery);
        }

        foreach ($this->wirelessTechnologyNames as $wirelessTechnologyName) {
            $wirelessTechnology = new WirelessTechnology();
            $wirelessTechnology->setName($wirelessTechnologyName);

            $manager->persist($wirelessTechnology);
        }

        foreach ($this->brandNames as $brandName) {
            $brand = new Brand();
            $brand->setName($brandName);

            $manager->persist($brand);
        }

        foreach ($this->osNames as $osName) {
            $os = new Os();
            $os->setName($osName);

            $manager->persist($os);
        }

        foreach ($this->screenResolutionSizes as $screenResolutionSize) {
            $screenResolution = new ScreenResolution();
            $screenResolution->setResolution($screenResolutionSize);

            $manager->persist($screenResolution);
        }

        foreach ($this->screenTechnologyNames as $screenTechnologyName) {
            $screenTechnology = new ScreenTechnology();
            $screenTechnology->setName($screenTechnologyName);

            $manager->persist($screenTechnology);
        }

        foreach ($this->colorNames as $colorName) {
            $color = new Color();
            $color->setName($colorName);

            $manager->persist($color);
        }

        foreach ($this->simSizeNames as $simSizeName) {
            $simSize = new SimSize();
            $simSize->setName($simSizeName);

            $manager->persist($simSize);
        }

        foreach ($this->storageCapacities as $storageCapacity) {
            $storage = new Storage();
            $storage->setCapacity($storageCapacity);

            $manager->persist($storage);
        }

        $manager->flush();

        $faker = Factory::create('fr-FR');

        for ($i = 1; $i <= self::NUMBER_OF_PRODUCT; $i++) {
            $product = new Product();
            $product
                ->setPrice(mt_rand(99, 999) + 0.99)
                ->setDualSim(mt_rand(0, 1))
                ->setMicroSd(mt_rand(0, 1))
                ->setScreenSize($faker->randomFloat(1, 5, 8))
                ->setCameraResolution($faker->randomFloat(1, 8, 60))
                ->setWeight(mt_rand(150, 300))
                ->setUsbTypeC(mt_rand(0, 1))
                ->setYearsOfWarranty(mt_rand(1, 3))
                ->setJackPlug(mt_rand(0, 1))
                ->setFrontCamera(mt_rand(0, 1))
                ->setBackCamera(mt_rand(0, 1))
                ->setRam(mt_rand(1, 16))
                ->setBattery($faker->randomElement($manager->getRepository(Battery::class)->findAll()))
                ->setBrand(($faker->randomElement($manager->getRepository(Brand::class)->findAll())))
                ->setOs(($faker->randomElement($manager->getRepository(Os::class)->findAll())))
                ->setScreenResolution(($faker->randomElement($manager->getRepository(ScreenResolution::class)->findAll())))
                ->setScreenTechnology(($faker->randomElement($manager->getRepository(ScreenTechnology::class)->findAll())))
                ->setSimSize(($faker->randomElement($manager->getRepository(SimSize::class)->findAll())))
                ->setStorage(($faker->randomElement($manager->getRepository(Storage::class)->findAll())))
                ->setName($product->getBrand()->getName() . " " . $product->getOs()->getName() . " " . $product->getStorage()->getCapacity() . " Gb memory " . $product->getRam() . " Gb RAM");

            $wirelessTechnologyCount = mt_rand(1, count($manager->getRepository(WirelessTechnology::class)->findAll()));

            $newWirelessTechnologies = $faker->randomElements($manager->getRepository(WirelessTechnology::class)->findAll(), $wirelessTechnologyCount, false);

            foreach ($newWirelessTechnologies as $newWirelessTechnology) {
                $product->addWirelessTechnology($newWirelessTechnology);
            }

            $colorCount = mt_rand(1, count($manager->getRepository(Color::class)->findAll()));

            $newColors = $faker->randomElements($manager->getRepository(Color::class)->findAll(), $colorCount, false);

            $slugify = new Slugify();

            /* @var Color $newColor */
            foreach ($newColors as $newColor) {
                $product->addColor($newColor);

                $illustration = new Illustration();
                $illustration->setUrl($slugify->slugify($product->getName()) . "_" . $newColor->getName() . ".png");

                $manager->persist($illustration);

                $product->addIllustration($illustration);
            }

            $manager->persist($product);
        }

        $manager->flush();
    }
}
