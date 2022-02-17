<?php

namespace App\Controller;

use App\Entity\Character;
use App\Repository\CharacterRepository;
use App\Service\FightService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


class FightController extends AbstractController
{

    public function __construct(FightService $fightService)
    {
        $this->fightService = $fightService;
    }
    
    /**
     * @Route("/fight", name="fight")
     */
    public function index(CharacterRepository $characterRepository): Response
    {
        $this->fightService->resetSession();

        return $this->render('fight/index.html.twig', [
            'characters' => $characterRepository->findAll()
        ]);
    }

    /**
     * @Route("/check", name="check")
     */
    public function checkSelectedCharacters(): JsonResponse
    {
        return new JsonResponse([
            'selected' => $this->fightService->getSessChar()
        ]);
    }

    /**
     * @Route("/select-char", name="select-char")
     */
    public function selectChar(Request $request): JsonResponse
    {
        return new JsonResponse(
            $this->fightService->selectChar($request)
        );
    }

    /**
     * @Route("/fighting", name="fighting")
     */
    public function fighting(): JsonResponse
    {
        $team   = $this->fightService->setTeams();
        $logs   = $this->fightService->setFight($team);
        $winner = $team[$logs[count($logs)]['team']];
        $html   = $this->fightService->setScore($winner);

        return new JsonResponse([
            'team' => $winner,
            'logs' => $logs,
            'html' => $html
        ]);
    }
}
