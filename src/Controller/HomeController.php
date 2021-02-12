<?php

namespace App\Controller;

use App\Entity\PropertySearch;
use App\Repository\CampusRepository;
use App\Repository\StateRepository;
use App\Repository\VacationRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
    public function index_member(Request $request, VacationRepository $vacationRepository, CampusRepository $campusRepository): Response
    {
        $session = new Session();
        if ($request->request->get("campus")) {
            $session->set('campusSelected', $request->request->get("campus"));
            $campusSelected = $request->request->get("campus");
        } else if ($session->get('campusSelected')) {
            $campusSelected = $session->get('campusSelected');
        } else {
            $session->set('campusSelected', $this->getUser()->getCampus()->getId('id'));
            $campusSelected = $this->getUser()->getCampus()->getId('id');
        }

        $session->set('hostSelected', $request->request->get("sortHost"));
        $session->set('dateFinishedSelected', $request->request->get("sortDateFinished"));
        $session->set('bookedSelected', $request->request->get("sortBooked"));
        $session->set('notBookedSelected', $request->request->get("sortNotBooked"));
        $campus = $campusRepository->findAll();

        $search = new PropertySearch();
        if ($request->request->get("sortHost")) {
            $search->setHost($this->getUser()->getId());
        } else $search->setHost(null);

        if ($request->request->get("sortBooked")) {
            $search->setBooked($this->getUser()->getId());
        } else $search->setBooked(null);

        if ($request->request->get("sortNotBooked")) {
            $search->setNotBooked($this->getUser()->getId());
        } else $search->setNotBooked(null);

        $search->setFinished($request->request->get("sortDateFinished"));
        $search->setDateMin($request->request->get("dateMin"));
        $search->setDatemax($request->request->get("dateMax"));
        $search->setWord($request->request->get("word_content"));
        $search->setOrganiser($this->getUser());

        if ($request->request->get("campus")) {
            $search->setCampus($request->request->get("campus"));
        } else $search->setCampus($campusSelected);

        if ($search) {
            return $this->render('vacation/index.html.twig', [
                'vacations' => $vacationRepository->findFilteredVacations($search),
                'campuses' => $campus,
            ]);
        }


        return $this->render('vacation/index.html.twig', [
            'vacations' => $vacationRepository->findByCampus($campusSelected),
            'campuses' => $campus,
        ]);

    }

    #[Route('/member/inscription/{id}', name: 'home_inscription')]
    public function inscription(VacationRepository $vr, int $id, StateRepository $sr): Response
    {
        $vacation = $vr->find($id);
        $booked = $vacation->getBooked();
        $free = $vacation->getPlaceNumber();
        $limitDate = $vacation->getVacationLimitDate();

        if ($vacation->getParticipants()->contains($this->getUser()) || $booked >= $free || $limitDate <= new DateTime('now')) {
            $this->addFlash("warning", "L'inscription n'est pas possible!");
            return $this->redirectToRoute('home_member');
        } else {
            $vacation->addParticipant($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $vacation->setBooked($booked + 1);
            $entityManager->persist($vacation);
            $entityManager->flush();


            if ($vacation->getBooked() == $vacation->getPlaceNumber()) {
                $state = $sr->find(4);
                $vacation->setState($state);
                $entityManager->persist($vacation);
                $entityManager->flush();
            }

            $this->addFlash("success", "Vous êtes inscrit à la sortie!");
            return $this->redirectToRoute('home_member');
        }
    }

    #[Route('/member/publish/{id}', name: 'home_publish')]
    public function publish(VacationRepository $vr, int $id, StateRepository $sr): Response
    {
        $vacation = $vr->find($id);
        if ($this->getUser() == $vacation->getOrganiser()) {
        $entityManager = $this->getDoctrine()->getManager();
        $vacation->setState($sr->find(1));
        $entityManager->persist($vacation);
        $entityManager->flush();


        $this->addFlash("success", "Votre sortie est maintenant ouverte!");
        return $this->redirectToRoute('home_member');
        }else {
            return $this->redirectToRoute('home_member');
        }
    }


    #[Route('/member/desist/{id}', name: 'home_desist')]
    public function desist(VacationRepository $vr, int $id, StateRepository $sr): Response
    {
        $vacation = $vr->find($id);
        if ($vacation->getParticipants()->contains($this->getUser())) {
        $booked = $vacation->getBooked();
        $vacationDate = $vacation->getVacationDate();
        $limitDate = $vacation->getVacationLimitDate();

        if ($vacationDate > new DateTime('now') and $limitDate > new DateTime('now')) {
            $vacation->setBooked($booked - 1);
            $vacation->removeParticipant($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            if ($vacation->getState()->getId() == 4) {
                $state = $sr->find(1);
                $vacation->setState($state);
                $entityManager->persist($vacation);
                $entityManager->flush();
            }

            $this->addFlash("success", "Votre participation a été annulée!");
            return $this->redirectToRoute('home_member');

        } else if ($vacationDate > new DateTime('now') and $limitDate < new DateTime('now')) {
            $vacation->setBooked($booked - 1);
            $vacation->removeParticipant($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash("success", "Votre participation a été annulée! Votre place ne peut être réofferte!");
            return $this->redirectToRoute('home_member');
        }

        $this->addFlash("success", "Vous ne pouvez plus annuler votre participation! Contactez directement l'organisateur.");
        return $this->redirectToRoute('home_member');
        }else {
            return $this->redirectToRoute('home_member');
        }
    }


    #[Route('/member/cancel/{id}', name: 'home_cancel')]
    public function cancel(Request $request, VacationRepository $vr, int $id, StateRepository $sr): Response
    {

        $vacation = $vr->find($id);
        if ($this->getUser() == $vacation->getOrganiser()) {
            $form = $this->createFormBuilder($vacation)
                ->add('name', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                    'label' => 'Nom',
                    'disabled' => true])
                ->add('vacationDate', DateType::class, [
                    'label' => 'Date de la sortie',
                    'disabled' => true])
                ->add('campus', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                    'disabled' => true])
                ->add('location', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                    'disabled' => true])
                ->add('description', TextareaType::class, [
                    'label' => 'Ajouter le motif',])
                ->add('save', SubmitType::class, [
                    'label' => 'Annuler',
                    'attr' => ['class' => 'btn btn-dark btn-lg m-3'],
                    'row_attr' => ['class' => 'd-inline ', 'id' => '...']])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $vacation->setState($sr->find(6));
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash("success", "La sortie a bien été annulée!");
                return $this->redirectToRoute('home_member');
            }
            return $this->render('vacation/cancel.html.twig', [
                'vacation' => $vacation,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('home_member');
        }
    }
}