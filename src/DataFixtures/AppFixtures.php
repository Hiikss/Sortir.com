<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Place;
use App\Entity\State;
use App\Entity\Trip;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        //CAMPUS
        $campus1 = new Campus();
        $campus1->setName('Saint-Herblain');
        $manager->persist($campus1);
        
        $campus2 = new Campus();
        $campus2->setName('Chartres-de-Bretagne');
        $manager->persist($campus2);
        
        $campus3 = new Campus();
        $campus3->setName('La Roche-sur-Yon');
        $manager->persist($campus3);

        //CITY
        $city1 = new City();
        $city1->setName('Paris');
        $city1->setPostalCode(75000);
        $manager->persist($city1);

        $city2 = new City();
        $city2->setName('Nantes');
        $city2->setPostalCode(44000);
        $manager->persist($city2);

        //PLACE
        $place1 = new Place();
        $place1->setName('Chez John');
        $place1->setStreet('35 rue de la paix');
        $place1->setLatitude(10);
        $place1->setLongitude(10);
        $place1->setCity($city1);
        $manager->persist($place1);

        $place2 = new Place();
        $place2->setName('Musée d\'art');
        $place2->setStreet('41 rue du musée');
        $place2->setLatitude(3);
        $place2->setLongitude(8);
        $place2->setCity($city1);
        $manager->persist($place2);

        $place3 = new Place();
        $place3->setName('Cinéma du quartier');
        $place3->setStreet('21 rue du cinéma');
        $place3->setLatitude(31);
        $place3->setLongitude(12);
        $place3->setCity($city2);
        $manager->persist($place3);

        //USER
        $user1 = new User();
        $user1->setEmail('john.doe@test.com');
        $user1->setRoles(['ROLE_USER']);
        $user1->setPassword($this->hasher->hashPassword($user1, 'mdp123'));
        $user1->setPseudo('JoDo');
        $user1->setLastname('Doe');
        $user1->setFirstname('John');
        $user1->setTelephone(0102030405);
        $user1->setActive(true);
        $user1->setCampus($campus1);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('admin@admin.com');
        $user2->setRoles(['ROLE_ADMIN']);
        $user2->setPassword($this->hasher->hashPassword($user1, 'admin123'));
        $user2->setPseudo('admin');
        $user2->setLastname('admin');
        $user2->setFirstname('admin');
        $user2->setTelephone(0102030405);
        $user2->setActive(true);
        $user2->setCampus($campus1);
        $manager->persist($user2);

        $user3 = new User();
        $user3->setEmail('toto@test.com');
        $user3->setRoles(['ROLE_USER']);
        $user3->setPassword($this->hasher->hashPassword($user1, 'mdp123'));
        $user3->setPseudo('toto');
        $user3->setLastname('Titi');
        $user3->setFirstname('Toto');
        $user3->setTelephone(0102030405);
        $user3->setActive(true);
        $user3->setCampus($campus2);
        $manager->persist($user3);

        //STATE
        $state1 = new State();
        $state1->setLabel('Créée');
        $manager->persist($state1);

        $state2 = new State();
        $state2->setLabel('Ouverte');
        $manager->persist($state2);

        $state3 = new State();
        $state3->setLabel('Clôturée');
        $manager->persist($state3);

        $state4 = new State();
        $state4->setLabel('Activité en cours');
        $manager->persist($state4);

        $state5 = new State();
        $state5->setLabel('Passée');
        $manager->persist($state5);

        $state6 = new State();
        $state6->setLabel('Annulée');
        $manager->persist($state6);

        //TRIP
        $trip1 = new Trip();
        $trip1->setState($state5);
        $trip1->setOrganizer($user1);
        $trip1->setPlace($place1);
        $trip1->setCampus($campus1);
        $trip1->setName('Jardinage');
        $trip1->setStartDateTime(new DateTime('2023-04-16 14:00:00'));
        $trip1->setDuration(90);
        $trip1->setLimitEntryDate(new DateTime('2023-04-13'));
        $trip1->setMaxRegistrationsNb(5);
        $trip1->setTripInfos('Du jardinage');
        $manager->persist($trip1);

        $trip2 = new Trip();
        $trip2->setState($state2);
        $trip2->setOrganizer($user2);
        $trip2->setPlace($place2);
        $trip2->setCampus($campus1);
        $trip2->setName('Visite au musée');
        $trip2->setStartDateTime(new DateTime('2023-04-28 15:00:00'));
        $trip2->setDuration(120);
        $trip2->setLimitEntryDate(new DateTime('2023-04-27'));
        $trip2->setMaxRegistrationsNb(15);
        $trip2->setTripInfos('Visite au musée des arts');
        $trip2->addRegisteredUser($user1);
        $trip2->addRegisteredUser($user3);
        $manager->persist($trip2);

        $trip3 = new Trip();
        $trip3->setState($state2);
        $trip3->setOrganizer($user2);
        $trip3->setPlace($place3);
        $trip3->setCampus($campus1);
        $trip3->setName('Jardinage');
        $trip3->setStartDateTime(new DateTime('2023-04-16 14:30:00'));
        $trip3->setDuration(180);
        $trip3->setLimitEntryDate(new DateTime('2023-04-27'));
        $trip3->setMaxRegistrationsNb(10);
        $trip3->setTripInfos('Séance cinéma pour aller voir le dernier Spielberg');
        $manager->persist($trip3);

        $manager->flush();
    }
}