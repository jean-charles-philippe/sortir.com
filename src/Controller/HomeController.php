<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Repository\VacationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{


    #[Route('/', name: 'home')]
    public function index(): Response
    {
       return $this->redirectToRoute("app_login");
    }


    #[Route('/member', name: 'home_member')]
    public function index_member(Request $request, VacationRepository $vacationRepository,CampusRepository $campusRepository): Response
    {
        return $this->render('vacation/index.html.twig', [
            'vacations' => $vacationRepository->findBy(array("campus"=>$request->request->get("campus"))),
            'campuses' => $campusRepository->findAll(),
            'campusSelected' => $request->request->get("campus"),
        ]);
    }
}
