<?php

namespace App\Controller;

use App\Entity\Character;
use App\Form\CharacterType;
use App\Repository\CharacterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CharacterController extends AbstractController
{
    /**
     * @Route("/", name="character_index", methods={"GET"})
     */
    public function index(CharacterRepository $characterRepository): Response
    {
        return $this->render('character/index.html.twig', [
            'characters' => $characterRepository->findAll(),
        ]);
    }

        /**
     * @Route("/char/score", name="character_score", methods={"GET"})
     */
    public function score(CharacterRepository $characterRepository): Response
    {
        return $this->render('character/score.html.twig', [
            'characters' => $characterRepository->findAllByScore(),
        ]);
    }

    /**
     * @Route("/char/new", name="character_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $character = new Character();
        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($character);
            $entityManager->flush();

            return $this->redirectToRoute('character_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('character/new.html.twig', [
            'character' => $character,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/char/{id}", name="character_show", methods={"GET"})
     */
    public function show(Character $character): Response
    {
        return $this->render('character/show.html.twig', [
            'character' => $character,
        ]);
    }

    /**
     * @Route("/char/{id}/edit", name="character_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Character $character, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $character->setModifiedAt(new \DateTime());
            $entityManager->flush();

            return $this->redirectToRoute('character_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('character/edit.html.twig', [
            'character' => $character,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/char/{id}", name="character_delete", methods={"POST"})
     */
    public function delete(Request $request, Character $character, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$character->getId(), $request->request->get('_token'))) {
            $entityManager->remove($character);
            $entityManager->flush();
        }

        return $this->redirectToRoute('character_index', [], Response::HTTP_SEE_OTHER);
    }
}
