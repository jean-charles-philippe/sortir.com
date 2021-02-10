<?php

namespace App\Controller;


use App\Entity\Vacation;
use App\Form\VacationType;
use App\Repository\StateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('member/vacation')]
class VacationController extends AbstractController
{


    #[Route('/new', name: 'vacation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, StateRepository $sr): Response
    {
        $vacation = new Vacation();
        $state = $sr->find(2);
        $vacation->setState($state);
        $vacation->setCampus($this->getUser()->getCampus());
        $form = $this->createForm(VacationType::class, $vacation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $vacation->setOrganiser($this->getUser());
            $vacation->setCampus($this->getUser()->getCampus());
            $state = $sr->find(1);
            $form->get('saveAndAdd')->isClicked()
                ? $vacation->setState($state)
                : 'task_success';
            $entityManager->persist($vacation);
            $entityManager->flush();
            $this->addFlash("success", "Votre sortie a bien été enregistrée!");
            return $this->redirectToRoute('home_member', ["campus"=> $vacation->getCampus()]);
        }

        return $this->render('vacation/new.html.twig', [
            'vacation' => $vacation,
            'form' => $form->createView(),
        ]);
    }






    #[Route('/{id}', name: 'vacation_show', methods: ['GET'])]
    public function show(Vacation $vacation): Response
    {
        $participants = $vacation->getParticipants()->toArray();
        return $this->render('vacation/show.html.twig', [
            'vacation' => $vacation,
            'participants' => $participants,
        ]);
    }

    #[Route('/{id}/edit', name: 'vacation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vacation $vacation): Response
    {
        $form = $this->createForm(VacationType::class, $vacation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('vacation_index');
        }

        return $this->render('vacation/edit.html.twig', [
            'vacation' => $vacation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'vacation_delete', methods: ['DELETE'])]
    public function delete(Request $request, Vacation $vacation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vacation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($vacation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('vacation_index');
    }



}
