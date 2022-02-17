<?php

namespace App\Service;

use App\Entity\Character;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Psr\Container\ContainerInterface;


class FightService
{

    public function __construct(
        SessionInterface $session,
        EntityManagerInterface $em,
        ContainerInterface $container
    )
    {
        $this->em = $em;
        $this->characterRepository = $em->getRepository(Character::class);
        $this->session = $session;
        $this->container = $container;
    }

    /**
     * Gestion de la sélection d'un personnage
     * @param Request $request
     * @return Array 
     */
    public function selectChar(Request $request)
    {

        // Récupération des personnages sélectionnés et de leur nombre
        $selectedCharacters = $this->session->get('selected_characters');
        $countCharacters    = count($selectedCharacters);

        // Vérfication du nombre maximum de personnages
        if (isset($selectedCharacters) && $countCharacters >= 4)
            return [
                'success' => false,
                'html'    => 'Nombre maximum de personnages atteind'
            ];
        else {
            $idCharacter = $request->request->get('character');
            $character   = $this->characterRepository->findOneById($idCharacter);
            $selectedCharacters[] = $character;
        }

        // Détermination de l'équipe
        if (($countCharacters++) <= 1)
            $team = 'f_team';
        else 
            $team = 's_team';

        // Mise en session des personnages sélectionnés
        $this->session->set('selected_characters', $selectedCharacters);

        return [
            'success' => true,
            'team'    => $team,
            'html'    => $this->container->get('twig')->render('character/card.html.twig', [
                'character' => $character,
                'editing'   => false
            ])
        ];
    }

    /**
     * Construction des équipes
     * @return Array $team
     */
    public function setTeams()
    {
        
        // Encapsule les équipes
        foreach ($this->getSessChar() as $key => $char) {

            if ($key <= 1) $team[1][] = $char;
            else $team[2][] = $char;

            $entity = $this->characterRepository->findOneById($char->getId());
            $entity->setFight($entity->getFight() + 1);

        }

        // Récupère et additionne les statistiques par équipe
        foreach ($team as $i => $chars) {

            $team[$i]['id'] = $i;

            foreach ($chars as $char) {

                $team[$i]['strong'] = ((!isset($team[$i]['strong'])) ? 0 : $team[$i]['strong']) + $char->getStrong();
                $team[$i]['guard']  = ((!isset($team[$i]['guard'])) ? 0 : $team[$i]['guard']) + $char->getGuard();
                $team[$i]['speed']  = ((!isset($team[$i]['speed'])) ? 0 : $team[$i]['speed']) + $char->getSpeed();
                $team[$i]['health'] = ((!isset($team[$i]['health'])) ? 0 : $team[$i]['health']) + $char->getHealth();
            
            }

        }

        $this->em->flush();

        return $team;
    }

    /**
     * Gestion du combat
     * @param Array $team
     * @return Array $logs
     */
    public function setFight(array $team)
    {

        // Systeme de combat
        $logs     = [];
        $round    = 1;
        $attacker = 1;

        while ($team[1]['health'] > 0 && $team[2]['health'] > 0) {
            
            if ($attacker < 2) 
                $defender = $attacker + 1;
            else 
                $defender = $attacker - 1;

            $att    = $team[$attacker]['strong'];
            $def    = $team[$defender]['guard'] + $team[$defender]['speed'];
            $per    = (int)($att / $def * 100);
            $luck   = rand(25, 75);
            $tot    = ($per+$luck) / 2;
            $fight  = rand(0, 99);

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

        return $logs;
    }

    /**
     * Gestion et affichage des résultats de fin de partie
     * @param Array $winner
     * @return Array $html
     */
    public function setScore(array $winner)
    {

        for($i = 0; $i < 2; $i++) {

            $html[$i] = $this->container->get('twig')->render('character/card.html.twig', [
                'character' => $winner[$i],
                'editing' => false
            ]);
            
            $entity = $this->characterRepository->findOneById($winner[$i]->getId());

            $entity->setVictory($entity->getVictory() + 1);
            $entity->setExperience($entity->getExperience() + 35);

        }

        $this->em->flush();

        return $html;
    }

    /**
     * Initialise la session 'selected_characters'
     * @return Bool
     */
    public function resetSession()
    {
        return $this->session->set('selected_characters', []);
    }

    /**
     * Retourne le contenu de la session 'selected_characters'
     * @return Array
     */
    public function getSessChar()
    {
        if (!is_null($this->session->get('selected_characters')))
            return $this->session->get('selected_characters');
    }
}