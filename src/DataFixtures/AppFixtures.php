<?php

namespace App\DataFixtures;

use App\Entity\Site;
use App\Entity\User;
use App\Repository\SiteRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use PhpParser\Node\Expr\Array_;


class AppFixtures extends Fixture
{


    public function load(ObjectManager $manager): void

    {
        $faker = Factory::create('fr_FR');
        $sites = array();

        $sites[0] = new Site();
        $sites[0]->setNomSite('Nantes');
        $manager->persist($sites[0]);
        $sites[1] = new Site();
        $sites[1]->setNomSite('Rennes');
        $manager->persist($sites[1]);
        $sites[2] = new Site();
        $sites[2]->setNomSite('Niort');
        $manager->persist($sites[2]);



        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');
        //création utilisateurs
        $users= Array();
        for($i = 0; $i<50; $i++){
            $users[$i]= new User();
            $users[$i]->setSite($faker->randomElement($sites));
            $users[$i]->setEmail($faker->email);
            $users[$i]->setPassword('$2y$13$/AiesTRrDLzQbX.elkrNR.xqLeHfg6/g8RCKbfNpOR40gvupa4Cbe');
            $users[$i]->setPseudo($faker->userName);
            $users[$i]->setNom($faker->name);
            $users[$i]->setPrenom($faker->lastName);
            $users[$i]->setTelephone('0606060606');
            $users[$i]->setActif($faker->boolean("TRUE"));
            $manager->persist($users[$i]);
        }

        $manager->flush();
    }
}
