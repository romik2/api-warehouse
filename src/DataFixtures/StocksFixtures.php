<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Stocks;
use App\Entity\Warehouse;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class StocksFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $warehouse = $manager->getRepository(Warehouse::class)->find($i);
            $product = $manager->getRepository(Product::class)->find($i);
            $stocks = new Stocks();
            $stocks->setProductId($product)->setWarehouseId($warehouse)->setStock(rand(10, 100));
            $manager->persist($stocks);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProductFixtures::class,
            WarehouseFixtures::class,
        ];
    }
}
