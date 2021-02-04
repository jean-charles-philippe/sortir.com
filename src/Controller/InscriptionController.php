<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Vacation;
use App\Form\InscriptionType;
use App\Repository\InscriptionRepository;
use App\Repository\VacationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('member/inscription')]
class InscriptionController extends AbstractController
{
    #[Route('/', name: 'inscription_index', methods: ['GET'])]
    public function index(InscriptionRepository $inscriptionRepository): Response
    {
        return $this->render('inscription/index.html.twig', [
            'inscriptions' => $inscriptionRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'inscription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, VacationRepository $vr, int $id, InscriptionRepository $is): Response
    {

        $inscription =$is->findBy(["vacation"=>$id, "user"=>$this->getUser()]);
        $vacation =$vr->find($id);
        $booked = $vacation->getBooked();
        $free = $vacation->getPlaceNumber();
        $limitDate = $vacation->getVacationLimitDate();

        if (!empty($inscription) OR $booked >= $free OR $limitDate <= new \DateTime('now'))
        {
            $this->addFlash("warning", "L'inscription n'est pas possible!");
            return $this->redirectToRoute('home_member');
        }else
        {
            $inscription = new Inscription();
            $inscription->setUser($this->getUser());
            $inscription->setVacation($vacation);
            $entityManager = $this->getDoctrine()->getManager();
            $vacation->setBooked($booked +1);
            $entityManager->persist($inscription);
            $entityManager->flush();

            $this->addFlash("success", "Vous êtes inscrit à la sortie!");
            return $this->redirectToRoute('home_member');

        }
    }

    #[Route('/{id}', name: 'inscription_show', methods: ['GET'])]
    public function show(Inscription $inscription): Response
    {
        return $this->render('inscription/show.html.twig', [
            'inscription' => $inscription,
        ]);
    }

    #[Route('/{id}/edit', name: 'inscription_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Inscription $inscription): Response
    {
        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('inscription_index');
        }

        return $this->render('inscription/edit.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'inscription_delete', methods: ['DELETE'])]
    public function delete(Request $request, Inscription $inscription, InscriptionRepository $is, int $id, VacationRepository $vr): Response
    {
        $inscription =$is->findBy(["vacation"=>$id, "user"=>$this->getUser()]);
        $vacation =$vr->find($id);
        $booked = $vacation->getBooked();
        $limitDate = $vacation->getVacationLimitDate();
        $vacationDate = $vacation->getVacationDate();

        if (empty($inscription) OR $limitDate < new \DateTime('now') OR $vacationDate < new \DateTime('now'))
        {
            $this->addFlash("warning", "Vous ne pouvez plus vous désister!");
            return $this->redirectToRoute('home_member');
        }else{
            if ($this->isCsrfTokenValid('delete'.$inscription->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $vacation->setBooked($booked -1);
                $entityManager->remove($inscription);
                $entityManager->flush();
            }
            $this->addFlash("success", "Vous vous êtes bien désisté!");
            return $this->redirectToRoute('home_member');
        }


    }
}
