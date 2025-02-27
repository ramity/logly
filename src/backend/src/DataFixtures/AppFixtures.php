<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\ErrorType;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create error type enums

        $error_types = ["runtime", "resource", "promise"];

        foreach ($error_types as $error_type)
        {
            $entity = new ErrorType();
            $entity->setName($error_type);
            $manager->persist($entity);
        }

        $manager->flush();
    }
}
