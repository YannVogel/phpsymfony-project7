<?php

namespace App\DataFixtures;

use App\DataFixtures\DataProvider;
use App\Entity\Client;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ClientUserFixtures extends Fixture
{
    private DataProvider $dataProvider;
    private const NUMBER_OF_CLIENT = 3;
    private const MIN_USER_BY_CLIENT = 20;
    private const MAX_USER_BY_CLIENT = 50;

    public function __construct(DataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    public function load(ObjectManager $manager)
    {
        if(self::NUMBER_OF_CLIENT <= 0) {
            throw new \Exception('NUMBER_OF_CLIENT must be a positive integer.');
        }

        if(self::MIN_USER_BY_CLIENT <= 0 || self::MAX_USER_BY_CLIENT <= 0) {
            throw new \Exception('MIN_USER_BY_CLIENT and MAX_USER_BY_CLIENT must be positive integers.');
        }

        if (self::MIN_USER_BY_CLIENT > self::MAX_USER_BY_CLIENT) {
            throw new \Exception('MIN_USER_BY_CLIENT must be equal or lower than MAX_USER_BY_CLIENT.');
        }
        $faker = Factory::create('fr-FR');

        for ($i = 1; $i <= self::NUMBER_OF_CLIENT; $i++) {
            $client = new Client();

            $client->setName('Client' . $i)
                ->setPassword(password_hash('motdepasse', PASSWORD_BCRYPT))
                ->setMail(strtolower($client->getName() . '@bilemo.com'))
                ->setLogo('https://randomuser.me/api/portraits/lego/' . mt_rand(0,8) . '.jpg');

            for ($j = 1; $j <= mt_rand(self::MIN_USER_BY_CLIENT, self::MAX_USER_BY_CLIENT); $j++) {
                $user = new User();

                $user->setCivility($this->getRandomCivility())
                    ->setLastName($faker->lastName)
                    ->setFirstName($faker->firstName($user->getCivility() === 'm' ? 'male' : 'female'))
                    ->setAge(mt_rand(18,99))
                    ->setCity($faker->randomElement($this->dataProvider->getArrayOfCities()))
                    ->setClient($client);

                $manager->persist($user);
            }

            $manager->persist($client);
        }

        $manager->flush();
    }

    public function getRandomCivility() : string
    {
        return mt_rand(0,1) ? 'm' : 'f';
    }
}
