<?php

namespace App\Controller;

use App\Entity\Note;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends AbstractController
{
    /**
     * @Route("/notes", name="notes_list")
     */
    public function list(Request $request, NoteRepository $notes): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $q = $request->query->get('q');
        $status = $request->query->get('status');
        $category = $request->query->get('category');

        $items = $notes->searchByUserAndCriteria($this->getUser(), $q, $status, $category);

        return $this->render('note/list.html.twig', ['notes' => $items, 'q' => $q, 'status' => $status, 'category' => $category]);
    }

    /**
     * @Route("/notes/create", name="notes_create")
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($request->isMethod('POST')) {
            $note = new Note();
            $note->setTitle((string) $request->request->get('title'));
            $note->setContent((string) $request->request->get('content'));
            $note->setCategory((string) $request->request->get('category'));
            $note->setStatus((string) $request->request->get('status'));
            $note->setUser($this->getUser());

            $em->persist($note);
            $em->flush();

            return $this->redirectToRoute('notes_list');
        }

        return $this->render('note/create.html.twig');
    }
}
