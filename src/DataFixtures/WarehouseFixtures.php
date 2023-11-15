<?php

namespace App\DataFixtures;

use App\Entity\Warehouse;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WarehouseFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $warehouse = new Warehouse();
            $warehouse->setName("Warehouse $i");
            $manager->persist($warehouse);
        }

        $manager->flush();
    }
}
