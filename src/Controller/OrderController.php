<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Repository\OrderItemsRepository;
use App\Repository\OrdersRepository;
use App\Repository\StocksRepository;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/api/order/list", name="order_list", methods={"GET"})
     */
    public function list(Request $request, OrdersRepository $ordersRepository): Response
    {
        $result = [];
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $filters = $request->get('filters', []);
        $orders = $ordersRepository->list($filters, $page, $limit);
        /** @var Orders $order */
        foreach ($orders as $order) {
            $items = [];
            $warehouse = [];
            foreach ($order->getOrderItems() as $item) {
                $items = ['id' => $item->getId(), 'productId' => $item->getProductId()->getId()];
            }
            if ($order->getWarehouseId()) {
                $warehouse = ['id' => $order->getWarehouseId()->getId(), 'name' => $order->getWarehouseId()->getName()];
            }
            $result[] = [
                'id' => $order->getId(),
                'customer' => $order->getCustomer(),
                'createdAt' => $order->getCreatedAt()->format('d.m.y H:i'),
                'status' => $order->getStatus(),
                'warehouse' => $warehouse,
                'items' => $items
            ];
        }
        return new JsonResponse($result);
    }

    /**
     * @Route("/api/order/{id}/status/canceled", name="order_status_canceled", methods={"PATCH"})
     */
    public function canceled(Request $request,
                          OrdersRepository $ordersRepository,
                          EntityManagerInterface $entityManager,
                          OrderItemsRepository $orderItemsRepository,
                          StocksRepository $stocksRepository
    ): Response
    {
        $order = $ordersRepository->find($request->get('id'));
        $products = [];
        $countProduct = [];
        $items = $orderItemsRepository->findBy(['orderId' => $order->getId()]);
        foreach ($items as $item) {
            $products[] = $item->getId();
            $countProduct[$item->getProductId()->getId()] = $item->getCount();
        }
        $stocks = $stocksRepository->findBy(['productId' => $products, 'warehouseId' => $order->getWarehouseId()->getId()]);
        foreach ($stocks as $stock) {
            $stock->setStock($stock->getStock() + $countProduct[$stock->getProductId()->getId()]);
            $entityManager->persist($stock);
        }
        $order->setStatus(Orders::STATUS_CANCELED)->setCompletedAt(null);
        $entityManager->persist($order);
        $entityManager->flush();
        return new JsonResponse(['result' => 'ok']);
    }

    /**
     * @Route("/api/order/{id}/status/done", name="order_status_done", methods={"PATCH"})
     */
    public function done(Request $request,
                         OrdersRepository $ordersRepository,
                         EntityManagerInterface $entityManager
    ): Response
    {
        $order = $ordersRepository->find($request->get('id'));
        $order->setStatus(Orders::STATUS_COMPLETED)->setCompletedAt(new \DateTime());
        $entityManager->persist($order);
        $entityManager->flush();
        return new JsonResponse(['result' => 'ok']);
    }

    /**
     * @Route("/api/order/{id}/status/active", name="order_status_active", methods={"PATCH"})
     */
    public function active(Request $request,
                           OrdersRepository $ordersRepository,
                           EntityManagerInterface $entityManager,
                           OrderItemsRepository $orderItemsRepository,
                           OrderService $orderService
    ): Response
    {
        $order = $ordersRepository->find($request->get('id'));
        $items = $orderItemsRepository->findBy(['orderId' => $order->getId()]);
        $orderService->writeOffFromStock($items, $order);
        $order->setStatus(Orders::STATUS_ACTIVE)->setCompletedAt(null);
        $entityManager->persist($order);
        $entityManager->flush();
        return new JsonResponse(['result' => 'ok']);
    }

    /**
     * @Route("/api/order/add", name="order_add", methods={"GET"})
     * Лучше сделать PUT
     */
    public function add(Request $request, EntityManagerInterface $entityManager, OrderService $orderService, OrderItemsRepository $orderItemsRepository): Response
    {
        $orderDetails = $request->get('order', []);
        $order = new Orders();
        $order->setCustomer($orderDetails['customer'])->setWarehouseId($orderDetails['warehouseId']);
        $items = $orderItemsRepository->findBy(['orderId' => $orderDetails['itemIds']]);
        $orderService->writeOffFromStock($items, $order);
        $entityManager->persist($order);
        $entityManager->flush();
        return new JsonResponse(['result' => 'ok']);
    }
}
