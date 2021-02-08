<?php

namespace App\Controller;

use App\Entity\Vacation;
use App\Form\VacationType;
use App\Repository\CampusRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;
use App\Repository\VacationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

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
        $session->set('bookedSelected', $request->request->get("sortBooked"));
        $session->set('notBookedSelected', $request->request->get("sortNotBooked"));
        $campus = $campusRepository->findAll();

        if($request->request->get("sortHost")){
            return $this->render('vacation/index.html.twig', [
                'vacations' => $vacationRepository->findBy(array("campus"=>$request->request->get("campus"), "organiser"=>$this->getUser())),
                'campuses' => $campus,
            ]);
        }if($request->request->get("sortDateFinished")){
            return $this->render('vacation/index.html.twig', [
                'vacations' => $vacationRepository->findByCampusAndDateFinished($request->request->get("campus")),
                'campuses' => $campus,
            ]);

        } if($request->request->get("sortBooked")){
            return $this->render('vacation/index.html.twig', [
                'vacations' => $vacationRepository->findBookedByCampusUser($request->request->get("campus"), $this->getUser()),
                'campuses' => $campus,
            ]);

        } if($request->request->get("sortNotBooked")) {
            return $this->render('vacation/index.html.twig', [
                'vacations' => $vacationRepository->findNotBookedByCampusUser($request->request->get("campus"), $this->getUser()),
                'campuses' => $campus,
            ]);
        } if($request->request->get("word_content") != null) {
            return $this->render('vacation/index.html.twig', [
                'vacations' => $vacationRepository->findByWord($request->request->get("campus"), $request->request->get('word_content')),
                'campuses' => $campus,
            ]);


        }


            return $this->render('vacation/index.html.twig', [
                'vacations' => $vacationRepository->findByCampus($request->request->get("campus")),
                'campuses' => $campus,
            ]);




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

    #[Route('/member/publish/{id}', name: 'home_publish')]
    public function publish(Request $request, VacationRepository $vr, int $id, StateRepository $sr): Response
    {
        $vacation =$vr->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $state = $sr->find(1);
        $vacation->setState($state);
        $entityManager->persist($vacation);
        $entityManager->flush();


        $this->addFlash("success", "Votre sortie est maintenant ouverte!");
        return $this->redirectToRoute('home_member');
    }

    #[Route('/member/desist/{id}', name: 'home_desist')]
    public function desist(Request $request, VacationRepository $vr, int $id, StateRepository $sr): Response
    {
        $vacation =$vr->find($id);
        $booked = $vacation->getBooked();
        $vacationDate = $vacation->getVacationDate();
        $limitDate = $vacation->getVacationLimitDate();

        if ( $vacationDate > new \DateTime('now') and $limitDate > new \DateTime('now')){
            $vacation->setBooked($booked -1);
            $vacation->removeParticipant($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash("success", "Votre participation a été annulée!");
            return $this->redirectToRoute('home_member');

        }else if ($vacationDate > new \DateTime('now') and $limitDate < new \DateTime('now')){
            $vacation->removeParticipant($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash("success", "Votre participation a été annulée! Votre place ne peut être réofferte!");
            return $this->redirectToRoute('home_member');
        }

        $this->addFlash("success", "Vous ne pouvez plus annuler votre participation! Contactez directement l'organisateur.");
        return $this->redirectToRoute('home_member');
    }

    #[Route('/member/cancel/{id}', name: 'home_cancel')]
    public function cancel(Request $request, VacationRepository $vr, int $id, StateRepository $sr): Response
    {
        $vacation =$vr->find($id);
        $state = $sr->find(6);
        $vacation->setState($state);

        if ($request->request->get("Enregistrer") && $request->request->get("motif")) {
            $vacation->setDescription($request->request->get("motif"));
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success", "La sortie a bien été annulée!");
            return $this->redirectToRoute('home_member');
        }
        return $this->render('vacation/edit.html.twig', [
            'vacation' => $vacation,
        ]);
    }


}
