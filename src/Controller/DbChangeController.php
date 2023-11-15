<?php

namespace App\Controller;

use App\Repository\DbChangeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DbChangeController extends AbstractController
{
    /**
     * @Route("/api/db/change", name="app_db_change")
     * В дальнейшем можно сделать по фильтрам и  нас будет возжность получать изменения конкретной сущности для клиентской части
     */
    public function index(DbChangeRepository $dbChangeRepository): Response
    {
        $dbChanges = $dbChangeRepository->findByExampleField();
        return new JsonResponse($dbChanges);
    }
}
