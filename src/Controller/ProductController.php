<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/api/product/list/stock", name="api_product_list_stock", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        $results = [];
        $products = $productRepository->findProductListStock();
        /** @var Product $product */
        foreach ($products as $product) {
            $warehouse = [];
            foreach ($product->getStocks() as $stock) {
                $warehouse[$stock->getWarehouseId()->getId()] = [
                    'id' => $stock->getWarehouseId()->getId(),
                    'name' => $stock->getWarehouseId()->getName(),
                    'stock' => empty($warehouse[$stock->getWarehouseId()->getId()]) ?
                        $stock->getStock() :
                        $warehouse[$stock->getWarehouseId()->getId()]['stock'] + $stock->getStock(),
                ];
            }
            $results[$product->getId()] = ['id' => $product->getId(), 'name' => $product->getName(), 'warehouse' => $warehouse];
        }

        return new JsonResponse($results);
    }
}