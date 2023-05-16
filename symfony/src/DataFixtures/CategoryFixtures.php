<?php
namespace App\DataFixtures;

use App\Entity\Category;
use Faker\Factory;
use Faker\Generator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class CategoryFixtures extends Fixture implements FixtureGroupInterface
{
 		private Generator $faker;
    public function __construct() 
    {
      $this->faker = Factory::create("fr_FR");
    }
    public function load(ObjectManager $manager): void
    
    {
      for ($i = 0; $i < 15; $i++) {
        
        $category = new Category();
        $category->setName($this->faker->sentence(2, true));
        $manager->persist($category);
      }
        $manager->flush();
    }
    public static function getGroups(): array 
    {
      return ['group1'];
    }
}