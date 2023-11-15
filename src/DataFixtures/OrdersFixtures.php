<?php

namespace App\DataFixtures;

use App\Entity\Orders;
use App\Entity\Warehouse;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrdersFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $warehouse = $manager->getRepository(Warehouse::class)->find($i);
            $orders = new Orders();
            $orders
                ->setCustomer("Customer $i")
                ->setWarehouseId($warehouse);
            $status = Orders::STATUSES[rand(0,2)];
            if ($status == Orders::STATUS_COMPLETED) {
                $orders->setCompletedAt(new \DateTime());
            }
            $orders->setStatus($status);
            $manager->persist($orders);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            WarehouseFixtures::class,
        ];
    }
}
