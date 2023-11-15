<?php

namespace App\DataFixtures;

use App\Entity\OrderItems;
use App\Entity\Orders;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrdersItemsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $order = $manager->getRepository(Orders::class)->find($i);
            $product = $manager->getRepository(Product::class)->find($i);
            $item = new OrderItems();
            $item
                ->setProductId($product)
                ->setOrderId($order)
                ->setCount(rand(1,100));

            $manager->persist($item);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            OrdersFixtures::class,
            ProductFixtures::class,
        ];
    }
}
