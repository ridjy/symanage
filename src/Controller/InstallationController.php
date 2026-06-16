<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class InstallationController extends AbstractController
{
    #[Route('/install', name: 'app_installation')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        if ($userRepository->adminExists()) {
            return $this->redirectToRoute('app_login');
        }

        $user = new User();
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            if (!is_string($plainPassword) || trim($plainPassword) === '') {
                $form->get('plainPassword')->addError(new FormError('Le mot de passe ne peut pas être vide.'));
            } else {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
                $user->setRoles(['ROLE_ADMIN']);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Administrateur créé avec succès.');

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('installation/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/user/new', name: 'app_admin_user_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            if (!is_string($plainPassword) || trim($plainPassword) === '') {
                $form->get('plainPassword')->addError(new FormError('Le mot de passe ne peut pas être vide.'));
            } else {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
                $user->setRoles(['ROLE_ADMIN']);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Administrateur créé avec succès.');

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->renderForm('admin/new_user.html.twig', [
            'form' => $form,
        ]);
    }

}
