<?php

declare(strict_types=1);

namespace App\Form\Task;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class TaskHandler
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly TaskRepository $taskRepository
    ) {
    }

    public function prepare(Task $data, array $options = []): FormInterface
    {
        return $this->formFactory->create(TaskForm::class, $data, $options);
    }

    public function handle(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Task $task */
            $task = $form->getData();
            $this->taskRepository->save($task);

            return true;
        }

        return false;
    }
}
