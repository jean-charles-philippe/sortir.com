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
        $session = new Session();
        $session->set('campusSelected', $request->request->get("campus"));
        $session->set('hostSelected', $request->request->get("sortHost"));
        $session->set('dateFinishedSelected', $request->request->get("sortDateFinished"));

        if($request->request->get("sortHost")){
            return $this->render('vacation/index.html.twig', [
                'vacations' => $vacationRepository->findBy(array("campus"=>$request->request->get("campus"), "organiser"=>$this->getUser())),
                'campuses' => $campusRepository->findAll(),
            ]);
        }else if($request->request->get("sortDateFinished")){
            return $this->render('vacation/index.html.twig', [
                'vacations' => $vacationRepository->findByCampusAndDateFinished($request->request->get("campus")),
                'campuses' => $campusRepository->findAll(),
            ]);
        } else{
            return $this->render('vacation/index.html.twig', [
                'vacations' => $vacationRepository->findBy(array("campus"=>$request->request->get("campus"))),
                'campuses' => $campusRepository->findAll(),
            ]);
        }



    }

    #[Route('/member/inscription/{id}', name: 'home_inscription')]
    public function inscription(Request $request, VacationRepository $vr, int $id): Response
    {
        $vacation =$vr->find($id);
        $booked = $vacation->getBooked();
        $free = $vacation->getPlaceNumber();
        $limitDate = $vacation->getVacationLimitDate();

        if ( $vacation->getParticipants()->contains($this->getUser()) || $booked >= $free || $limitDate <= new \DateTime('now'))
        {
            $this->addFlash("warning", "L'inscription n'est pas possible!");
            return $this->redirectToRoute('home_member');
        }else
        {
            $vacation->addParticipant($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $vacation->setBooked($booked +1);
            $entityManager->persist($vacation);
            $entityManager->flush();


            $this->addFlash("success", "Vous êtes inscrit à la sortie!");
            return $this->redirectToRoute('home_member');
        }
    }


}
