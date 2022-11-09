<?php

namespace App\Controller;

use App\DataCollector\LdapDataCollector;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        LdapDataCollector $ldapDataCollector
    ): Response
    {
        dd($ldapDataCollector->fetchData());
    }
}
