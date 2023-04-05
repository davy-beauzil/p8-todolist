<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Task;
use App\Form\Task\TaskHandler;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    public function __construct(
        private readonly TaskHandler $taskHandler,
        private readonly TaskRepository $taskRepository,
    ) {
    }

    #[Route(path: '/tasks', name: 'task_list')]
    public function list(): Response
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $this->taskRepository->findAll(),
        ]);
    }

    #[Route(path: '/tasks/create', name: 'task_create')]
    public function create(Request $request): Response
    {
        $task = new Task();
        $form = $this->taskHandler->prepare($task);
        $isCreated = $this->taskHandler->handle($form, $request);

        if ($isCreated) {
            $this->addFlash('success', 'La tâche a été bien été ajoutée.');
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/tasks/{id}/edit', name: 'task_edit')]
    public function edit(Task $task, Request $request): Response
    {
        $form = $this->taskHandler->prepare($task);
        $isUpdated = $this->taskHandler->handle($form, $request);

        if ($isUpdated) {
            $this->addFlash('success', 'La tâche a bien été modifiée.');
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route(path: '/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggle(Task $task): Response
    {
        $task->isDone = ! $task->isDone;
        $this->taskRepository->save($task);
        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->title));

        return $this->redirectToRoute('task_list');
    }

    #[Route(path: '/tasks/{id}/delete', name: 'task_delete')]
    public function delete(Task $task): Response
    {
        $this->taskRepository->remove($task);
        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
