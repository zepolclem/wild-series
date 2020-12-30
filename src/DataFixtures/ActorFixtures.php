<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    const ACTORS = [
        '1' => [
            'firstName' => 'Andrew',
            'lastName' => 'Lilcon',
            'program' => 'walking-dead',
        ],
        '2' => [
            'firstName' => 'Norman',
            'lastName' => 'Reedus',
            'program' => 'walking-dead',
        ],
        '3' => [
            'firstName' => 'Lauren',
            'lastName' => 'Cohan',
            'program' => 'walking-dead',
        ],

    ];
    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        $slugify = new Slugify;
        foreach (self::ACTORS as $key => $data) {
            $actor = new Actor();
            $actor->setFirstname($data['firstName']);
            $actor->setLastname($data['lastName']);
            $actor->addProgram($this->getReference($data['program'], $actor));
            $manager->persist($actor);
            $this->addReference('actor_' . $slugify->slugify($actor->getDisplayName()), $actor);
        }
        $manager->flush();
    }
}
