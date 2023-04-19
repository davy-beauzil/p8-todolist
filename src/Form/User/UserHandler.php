<?php

declare(strict_types=1);

namespace App\Form\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserHandler
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly UserRepository $userRepository
    ) {
    }

    public function prepare(User $data, array $options = []): FormInterface
    {
        return $this->formFactory->create(UserForm::class, $data, $options);
    }

    public function handle(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            if ($user->password === null) {
                $form->addError(new FormError('Vous devez saisir un mot de passe.'));
                return false;
            }

            $user->password = $this->hasher->hashPassword($user, $user->password);
            $this->userRepository->save($user);

            return true;
        }

        return false;
    }
}
