<?php

namespace App\Controller;

use App\Repository\WarehouseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WarehouseController extends AbstractController
{
    /**
     * @Route("/api/warehouse/list", name="warehouse_list", methods={"GET"})
     */
    public function index(WarehouseRepository $warehouseRepository): Response
    {
        $warehouses = $warehouseRepository->findResultArray();
        return new JsonResponse($warehouses);
    }
}
