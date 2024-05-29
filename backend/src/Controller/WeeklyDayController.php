<?php

namespace App\Controller;

use App\Entity\WeeklyDay;
use App\Form\WeeklyDay1Type;
use App\Repository\WeeklyDayRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/weekly/day')]
class WeeklyDayController extends AbstractController
{
    #[Route('/', name: 'app_weekly_day_index', methods: ['GET'])]
    public function index(WeeklyDayRepository $weeklyDayRepository): Response
    {
        return $this->render('weekly_day/index.html.twig', [
            'weekly_days' => $weeklyDayRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_weekly_day_new', methods: ['GET', 'POST'])]
    public function new(Request $request, WeeklyDayRepository $weeklyDayRepository): Response
    {
        $weeklyDay = new WeeklyDay();
        $form = $this->createForm(WeeklyDay1Type::class, $weeklyDay);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $weeklyDayRepository->save($weeklyDay, true);

            return $this->redirectToRoute('app_weekly_day_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('weekly_day/new.html.twig', [
            'weekly_day' => $weeklyDay,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_weekly_day_show', methods: ['GET'])]
    public function show(WeeklyDay $weeklyDay): Response
    {
        return $this->render('weekly_day/show.html.twig', [
            'weekly_day' => $weeklyDay,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_weekly_day_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, WeeklyDay $weeklyDay, WeeklyDayRepository $weeklyDayRepository): Response
    {
        $form = $this->createForm(WeeklyDay1Type::class, $weeklyDay);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $weeklyDayRepository->save($weeklyDay, true);

            return $this->redirectToRoute('app_weekly_day_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('weekly_day/edit.html.twig', [
            'weekly_day' => $weeklyDay,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_weekly_day_delete', methods: ['POST'])]
    public function delete(Request $request, WeeklyDay $weeklyDay, WeeklyDayRepository $weeklyDayRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$weeklyDay->getId(), $request->request->get('_token'))) {
            $weeklyDayRepository->remove($weeklyDay, true);
        }

        return $this->redirectToRoute('app_weekly_day_index', [], Response::HTTP_SEE_OTHER);
    }
}
