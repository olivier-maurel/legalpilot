<?php

namespace App\Controller;

use App\Entity\Character;
use App\Repository\CharacterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


class FightController extends AbstractController
{
    /**
     * @Route("/fight", name="fight")
     */
    public function index(
        Session $session,
        CharacterRepository $characterRepository
    ): Response
    {
        $session->set('selected_characters', []);

        return $this->render('fight/index.html.twig', [
            'characters' => $characterRepository->findAll(),
        ]);
    }

    /**
     * @Route("/check", name="check")
     */
    public function checkSelectedCharacters(
        Session $session
    ): JsonResponse
    {
        return new JsonResponse([
            'selected' => $session->get('selected_characters')
        ]);
    }

    /**
     * @Route("/select-char", name="select-char")
     */
    public function selectChar(
        Request $request,
        Session $session, 
        CharacterRepository $characterRepository
    ): JsonResponse
    {
        $selectedCharacters = $session->get('selected_characters');
        $countCharacters = count($selectedCharacters);

        if (isset($selectedCharacters) && $countCharacters >= 4)
            return new JsonResponse([
                'success' => false,
                'html'    => 'Nombre maximum de personnages atteind'
            ]);

        $idCharacter = $request->request->get('character');
        $character   = $characterRepository->findOneById($idCharacter);
        $selectedCharacters[] = $character;

        if (($countCharacters++) <= 1)
            $team = 'f_team';
        else 
            $team = 's_team';

        $session->set('selected_characters', $selectedCharacters);

        return new JsonResponse([
            'success' => true,
            'team'    => $team,
            'html'    => $this->renderView('character/card.html.twig', [
                'character' => $character,
                'editing'   => false
            ])
        ]);
    }

    /**
     * @Route("/fighting", name="fighting")
     */
    public function fighting(
        Session $session,
        EntityManagerInterface $em
    ): JsonResponse
    {
        $characterRepository = $em->getRepository(Character::class);

        // Encapsule les équipes
        foreach ($session->get('selected_characters') as $key => $char) {
            if ($key <= 1) $team[1][] = $char;
            else $team[2][] = $char;

            $entity = $characterRepository->findOneById($char->getId());
            $entity->setFight($entity->getFight() + 1);
        }

        // Regroupe les statistiques par équipe
        foreach ($team as $i => $chars) {
            $team[$i]['id'] = $i;
            foreach ($chars as $char) {
                $team[$i]['strong'] = ((!isset($team[$i]['strong'])) ? 0 : $team[$i]['strong']) + $char->getStrong();
                $team[$i]['guard']  = ((!isset($team[$i]['guard'])) ? 0 : $team[$i]['guard']) + $char->getGuard();
                $team[$i]['speed']  = ((!isset($team[$i]['speed'])) ? 0 : $team[$i]['speed']) + $char->getSpeed();
                $team[$i]['health'] = ((!isset($team[$i]['health'])) ? 0 : $team[$i]['health']) + $char->getHealth();
            }
        }

        $logs     = [];
        $round    = 1;
        $attacker = 1;

        while ($team[1]['health'] > 0 && $team[2]['health'] > 0) {
            
            if ($attacker < 2)
                $defender = $attacker + 1;
            else $defender = $attacker - 1;

            $att = $team[$attacker]['strong'];

            $def = $team[$defender]['guard'] + $team[$defender]['speed'];

            $per = (int)($att / $def * 100);

            $luck = rand(25, 75);

            $tot = ($per+$luck) / 2;

            $fight = rand(0, 99);

            $logs[$round] = [
                'team' => $attacker,
                'attack' => $per,
                'lucky' => $luck,
                'damage' => null,
                'health' => null
            ];

            if ($fight <= $tot) {
                $team[$defender]['health'] -= $att;
                $logs[$round]['damage'] = $att;
                $logs[$round]['health'] = $team[$defender]['health'];
            }

            $attacker = $defender;
            $round++;
        }

        $winner = $team[$logs[count($logs)]['team']];

        for($i = 0; $i < 2; $i++) {
            $html[$i] = $this->renderView('character/card.html.twig', [
                'character' => $winner[$i],
                'editing' => false
            ]);
            
            $entity = $characterRepository->findOneById($winner[$i]->getId());

            $entity->setVictory($entity->getVictory() + 1);
            $entity->setExperience($entity->getExperience() + 35);
        }

        $em->flush();

        return new JsonResponse([
            'team' => $winner,
            'logs' => $logs,
            'html' => $html
        ]);
    }
}
