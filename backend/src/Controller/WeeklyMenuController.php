<?php

namespace App\Controller;

use App\Entity\WeeklyDay;
use App\Entity\WeeklyMenu;
use App\Form\WeeklyMenuType;
use App\Repository\WeeklyMenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/weekly/menu')]
class WeeklyMenuController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/', name: 'app_weekly_menu_index', methods: ['GET'])]
    public function index(WeeklyMenuRepository $weeklyMenuRepository): Response
    {
        return $this->render('weekly_menu/index.html.twig', [
            'weeklyMenus' => $weeklyMenuRepository->findAll(),
        ]);
    }

    #[Route('/créer', name: 'app_weekly_menu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, WeeklyMenuRepository $weeklyMenuRepository): Response
    {
        $weeklyMenu = new WeeklyMenu();
        $form = $this->createForm(WeeklyMenuType::class, $weeklyMenu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->em->persist($weeklyMenu);            
            $this->em->flush();

            $this->addFlash('success', 'le menu a bien été créé');

            return $this->redirectToRoute('app_weekly_menu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('weekly_menu/new.html.twig', [
            'weekly_menu' => $weeklyMenu,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_weekly_menu_show', methods: ['GET'])]
    public function show(WeeklyMenu $weeklyMenu): Response
    {
        return $this->render('weekly_menu/show.html.twig', [
            'weeklyMenu' => $weeklyMenu,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_weekly_menu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, WeeklyMenu $weeklyMenu, WeeklyMenuRepository $weeklyMenuRepository): Response
    {
        $form = $this->createForm(WeeklyMenuType::class, $weeklyMenu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $weeklyDays = $form->get('weeklyDay')->getData();
            
            foreach ($weeklyDays as $weeklyDay) {
                $newWeeklyDay = new WeeklyDay;
                $newWeeklyDay->setDay($weeklyDay->getDay());
                $newWeeklyDay->setBreakfast($weeklyDay->getBreakfast());
                $newWeeklyDay->setLunch($weeklyDay->getLunch());
                $newWeeklyDay->setDinner($weeklyDay->getDinner());

                if (false === $weeklyMenu->getWeeklyDay()->contains($weeklyDay)) {
                    $weeklyMenu->getWeeklyDay()->removeElement($weeklyMenu);
                }

                $weeklyMenu->addWeeklyDay($newWeeklyDay);
                $this->em->persist($newWeeklyDay);
            }
            
            $this->em->persist($weeklyMenu);
            $this->em->flush();

            $this->addFlash('success', 'Le menu a bien été modifié');

            return $this->redirectToRoute('app_weekly_menu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('weekly_menu/edit.html.twig', [
            'weeklyMenu' => $weeklyMenu,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/supprimer/{id}', name: 'app_weekly_menu_delete', methods: ['POST'])]
    public function delete(Request $request, WeeklyMenu $weeklyMenu, WeeklyMenuRepository $weeklyMenuRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$weeklyMenu->getId(), $request->request->get('_token'))) {
            $weeklyMenuRepository->remove($weeklyMenu, true);
        }

        $this->addFlash('success', 'le menu a bien été supprimé');

        return $this->redirectToRoute('app_weekly_menu_index', [], Response::HTTP_SEE_OTHER);
    }
}
