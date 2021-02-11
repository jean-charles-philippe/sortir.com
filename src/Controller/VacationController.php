<?php

namespace App\Controller;


use App\Entity\Vacation;
use App\Form\VacationType;
use App\Repository\StateRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        $form = $this->createForm(VacationType::class, $vacation)
                        ->add('save', SubmitType::class, [
                            'label' => 'Enregistrer',
                            'attr' => ['class' => 'btn btn-dark btn-lg m-3'],
                            'row_attr' => ['class' => 'd-inline ', 'id' => '...']])
                        ->add('saveAndAdd', SubmitType::class, [
                            'label' => 'Publier',
                            'attr' => ['class' => 'btn btn-dark btn-lg m-3'],
                            'row_attr' => ['class' => 'd-inline ', 'id' => '...']]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $vacation->setOrganiser($this->getUser());
            $vacation->setCampus($this->getUser()->getCampus());
            $vacation->setBooked(0);
            $form->get('saveAndAdd')->isClicked()
                ? $vacation->setState($sr->find(1))
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
        if ($vacation->getVacationDate() > new \DateTime('now')) {
        $participants = $vacation->getParticipants()->toArray();
        return $this->render('vacation/show.html.twig', [
            'vacation' => $vacation,
            'participants' => $participants,
        ]);
        }else {
            return $this->redirectToRoute('home_member');
        }
    }

    #[Route('/{id}/edit', name: 'vacation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vacation $vacation, StateRepository $sr): Response
    {
        if ($this->getUser() == $vacation->getOrganiser()) {
            $form = $this->createForm(VacationType::class, $vacation)
                ->add('save', SubmitType::class, [
                    'label' => 'Enregistrer (non ouvert)',
                    'attr' => ['class' => 'btn btn-dark btn-lg m-3'],
                    'row_attr' => ['class' => 'd-inline ', 'id' => '...']])
                ->add('saveAndAdd', SubmitType::class, [
                    'label' => 'Publier',
                    'attr' => ['class' => 'btn btn-dark btn-lg m-3'],
                    'row_attr' => ['class' => 'd-inline ', 'id' => '...']])
                ->add('cancel', SubmitType::class, [
                    'label' => 'Annuler',
                    'attr' => ['class' => 'btn btn-dark btn-lg m-3'],
                    'row_attr' => ['class' => 'd-inline ', 'id' => '...']]);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $vacation->setCampus($this->getUser()->getCampus());
                $form->get('saveAndAdd')->isClicked()
                    ? $vacation->setState($sr->find(1))
                    : 'task_success';
                $form->get('cancel')->isClicked()
                    ? $vacation->setState($sr->find(6))
                    : 'task_success';
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash("success", "Modification effectuée!");
                return $this->redirectToRoute('home_member');
            }

            return $this->render('vacation/edit.html.twig', [
                'vacation' => $vacation,
                'form' => $form->createView(),
            ]);
        }else {
                return $this->redirectToRoute('home_member');
            }
    }


    /**
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param Vacation $vacation
     * @return Response
     */
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
