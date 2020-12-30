<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    const CATEGORIES = [
        'Action',
        'Aventure',
        'Animation',
        'Fantastique',
        'Horreur',
    ];
    public function load(ObjectManager $manager)
    {
        $slugify = new Slugify;
        foreach (self::CATEGORIES as $key => $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);
            $this->addReference($slugify->slugify($category->getName()), $category);
        }
        $manager->flush();
    }
}
