<?php

namespace App\Controller;

use App\DataCollector\ManagerDataCollector;
use App\Tools\Nslookup;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        ManagerDataCollector $mdc
    ): Response
    {
        dd($mdc->get());
    }
}
