<?php

namespace App\Controller;

use App\DataCollector\ManagerDataCollector;
use App\Entity\Host;
use App\Repository\HostRepository;
use App\Tools\Nslookup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/save-to-db', name: 'app_save_to_db')]
    public function saveToDb(
        HostRepository $repo,
        EntityManagerInterface $em,
        ManagerDataCollector $managerDataCollector
    ): Response
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $hosts = $managerDataCollector->get();

        foreach ($hosts as $host) {
            $h = $repo->findOneBy(['uuid' => $host['uuid']]) ?: new Host();

            $h = $serializer->deserialize(json_encode($host), Host::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $h]);

            $em->persist($h);
        }

        $em->flush();

        return $this->redirectToRoute('app_home');
    }
}
