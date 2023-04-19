<?php

declare(strict_types=1);

namespace App\Form\Task;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TaskHandler
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly TaskRepository $taskRepository,
        private readonly Security $security,
    ) {
    }

    public function prepare(Task $data, array $options = []): FormInterface
    {
        return $this->formFactory->create(TaskForm::class, $data, $options);
    }

    public function handleCreate(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);

        $user = $this->security->getUser();

        if (! $user instanceof User) {
            throw new AccessDeniedException('Vous devez être connecté pour créer une tâche.');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Task $task */
            $task = $form->getData();
            $task->author = $user;
            $this->taskRepository->save($task);

            return true;
        }

        return false;
    }

    public function handleUpdate(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);

        $user = $this->security->getUser();

        if (! $user instanceof User) {
            throw new AccessDeniedException('Vous devez être connecté pour modifier une tâche.');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Task $task */
            $task = $form->getData();
            $this->taskRepository->save($task);

            return true;
        }

        return false;
    }
}
