<?php
namespace App\DataFixtures;

use App\Entity\User;
use Faker\Factory;
use Faker\Generator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture implements FixtureGroupInterface
{
 		private Generator $faker;
    public function __construct(
      private readonly UserPasswordHasherInterface $passwordHasher
    ) 
    {
      $this->faker = Factory::create("fr_FR");
    }
    public function load(ObjectManager $manager): void
    
    {
      $isActiveArray= [true, false];
      
      for ($i = 0; $i < 40; $i++) {
        
        $isActive= array_rand($isActiveArray, 1);
        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($user, "UserUser1!");
        $user->setName($this->faker->words(1, true));
        $user->setEmail($this->faker->freeEmail());
        $user->setPassword($hashedPassword);
        $user->setRoles(["ROLE_USER"]);
        $user->setIsActive($isActive);
        
        $manager->persist($user);
      }
        $manager->flush();
    }
    public static function getGroups(): array 
    {
      return ['group3'];
    }
}