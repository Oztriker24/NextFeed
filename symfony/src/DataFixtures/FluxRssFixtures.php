<?php
namespace App\DataFixtures;

use App\Entity\FluxRss;
use App\Repository\CategoryRepository;
use Faker\Factory;
use Faker\Generator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class FluxRssFixtures extends Fixture implements  FixtureGroupInterface
{
 		private Generator $faker;
    public function __construct(private CategoryRepository $categoryRepository ) 
    {
      $this->faker = Factory::create("fr_FR");
    }
    public function load(ObjectManager $manager): void
    
    {
      for ($i = 0; $i < 50; $i++) {
        $cats = $this->categoryRepository->findOneBy(["id" => rand(1,6)]);
        $fluxRss = new FluxRss();
        $fluxRss->setUrl($this->faker->url());
        $fluxRss->setDescription($this->faker->paragraph(2));
        $fluxRss->setCategory($cats);
        
        $manager->persist($fluxRss);
      }
        $manager->flush();
    }
    public static function getGroups(): array 
    {
      return ['group2'];
    }
}