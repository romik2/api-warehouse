<?php

namespace App\Service;

use App\Entity\Orders;
use App\Repository\StocksRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    private EntityManagerInterface $entityManager;
    private StocksRepository $stocksRepository;

    public function __construct(EntityManagerInterface $entityManager, StocksRepository $stocksRepository)
    {
        $this->entityManager = $entityManager;
        $this->stocksRepository = $stocksRepository;
    }


    public function writeOffFromStock(array $items, Orders $order)
    {
        $products = [];
        $countProduct = [];
        foreach ($items as $item) {
            $products[] = $item->getId();
            $countProduct[$item->getProductId()->getId()] = $item->getCount();
        }
        $stocks = $this->stocksRepository->findBy(['productId' => $products, 'warehouseId' => $order->getWarehouseId()->getId()]);
        foreach ($stocks as $stock) {
            $stock->setStock($stock->getStock() - $countProduct[$stock->getProductId()->getId()]);
            $this->entityManager->persist($stock);
        }
    }
}