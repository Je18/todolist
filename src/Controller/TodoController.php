<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\ChekboxType;
use App\Form\DoneType;
use App\Form\TodoType;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/todo")
 */
class TodoController extends AbstractController
{
    /**
     * @Route("/", name="app_todo_index", methods={"GET", "POST"})
     */
    public function index(TodoRepository $todoRepository, Request $request): Response
    {
        $form = $this->createForm(DoneType::class);
        $form->handleRequest($request);
        $check = 0;
        if ($form->isSubmitted() && $form->isValid()) {
            $check = $form->get('done')->getData();
        }

        $orderby = $request->query->get('orderby') ?? "name";
        $order = $request->query->get('order') ?? "ASC";
        if ($order === "ASC") {
            $order = "DESC";
        } else {
            $order = "ASC";
        }

        return $this->render('todo/index.html.twig', [
            'order' => $order,
            'todos' => $todoRepository->findAllOrdered($order, $orderby, $check),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="app_todo_new", methods={"GET", "POST"})
     */
    public function new(Request $request, TodoRepository $todoRepository): Response
    {
        $todo = new Todo();
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $todoRepository->add($todo, true);

            return $this->redirectToRoute('app_todo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('todo/new.html.twig', [
            'todo' => $todo,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_todo_show", methods={"GET"})
     */
    public function show(Todo $todo): Response
    {
        dump($todo);
        return $this->render('todo/show.html.twig', [
            'todo' => $todo,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_todo_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Todo $todo, TodoRepository $todoRepository): Response
    {
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $todoRepository->add($todo, true);

            return $this->redirectToRoute('app_todo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('todo/edit.html.twig', [
            'todo' => $todo,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/update/{id}", name="app_todo_update", methods={"GET"})
     */
    public function update(Request $request, Todo $todo, TodoRepository $todoRepository, EntityManagerInterface $em): Response
    {
        $todo->setDone(!$todo->isDone());
        $em->persist($todo);
        $em->flush();
        return $this->json(["Status" => "succes", "id" => $todo->getId(), "NewStatus" => $todo->isDone()]);
    }

    /**
     * @Route("/search", name="app_todo_search", methods={"POST"})
     */
    public function search(Request $request, TodoRepository $todoRepository, SerializerInterface $serializer, EntityManagerInterface $em): Response
    {
        $payload = json_decode($request->getContent(), true);
        $todos = $todoRepository->search($payload['terms']);
        // $todos = $todoRepository->findAll();
        $jsonContent = $serializer->serialize($todos, 'json');
        return new Response($jsonContent);
    }

    /**
     * @Route("/{id}", name="app_todo_delete", methods={"POST"})
     */
    public function delete(Request $request, Todo $todo, TodoRepository $todoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $todo->getId(), $request->request->get('_token'))) {
            $todoRepository->remove($todo, true);
        }

        return $this->redirectToRoute('app_todo_index', [], Response::HTTP_SEE_OTHER);
    }
}
