<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
       return $this->redirectToRoute("app_login");
    }

    #[Route('/member', name: 'home_member')]
    public function index_member(): Response
    {
        return $this->render('home/index_member.html.twig');
    }
}
