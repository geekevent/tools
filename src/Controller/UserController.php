<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\User as Forms;
use App\Mailer\Mail\AccountCreated;
use App\Mailer\SendInBlueMailer;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route(name: 'user_')]
class UserController extends AbstractController
{
    private const ROLE = 'ROLE_ORGA';

    #[Route(path: '/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/reset/{token}', name: 'password_reset')]
    public function passwordReset(Request $request, string $token, UserRepository $repository): Response
    {
        $user = $repository->findOneBy([
            'resetToken' => $token,
        ]);

        if (null === $user) {
            throw new NotFoundHttpException('Ce token n\'existe pas');
        }

        $form = $this->createForm(Forms\ResetPassword::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->resetToken = null;

            $repository->flush();
            $this->addFlash(
                'notice',
                'Votre mot de passe a été crée'
            );

            return $this->redirectToRoute('user_login');
        }

        return $this->render(
            'Account/PasswordSet.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/reset', name: 'reset_token')]
    public function generateResetToken(): Response
    {
        return new Response();
    }

    #[Route(path: '/admin/me', name: 'me')]
    public function me(Request $request, UserRepository $repository): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(Forms\Me::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->flush();
        }

        return $this->render('Account/me.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/admin/user', name: 'create')]
    #[IsGranted(self::ROLE)]
    public function create(Request $request, UserRepository $repository, AccountCreated $created, SendInBlueMailer $mailer)
    {
        $user = new User();

        $form = $this->createForm(Forms\Create::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->persist($user);
            $mail = $created->createMailData($user, sprintf('%s://%s', $request->getScheme(), $request->getHttpHost()));
            $repository->flush();
            $mailer->send($mail);

            return $this->redirectToRoute('user_create');
        }

        return $this->render('Account/Accounts.html.twig', [
            'form' => $form->createView(),
            'items' => $repository->findAll(),
        ]);
    }

    #[Route(path: '/admin/user/{id}', name: 'update', requirements: [
        'id' => '\d+'
    ])]
    #[Entity(User::class, expr: 'repository.find(id)')]
    #[IsGranted(self::ROLE)]
    public function update(Request $request, UserRepository $repository, User $user)
    {
        $form = $this->createForm(Forms\Create::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->flush();

            return $this->redirectToRoute('user_create');
        }

        return $this->render('Account/Accounts.html.twig', [
            'form' => $form->createView(),
            'items' => $repository->findAll(),
        ]);
    }

    #[Route(path: '/admin/user/{id}/delete', name: 'delete', requirements: [
        'id' => '\d+'
    ])]
    #[Entity(User::class, expr: 'repository.find(id)')]
    #[IsGranted(self::ROLE)]
    public function delete(UserRepository $repository, User $user): Response
    {
        $repository->remove($user);
        $repository->flush();

        return $this->redirectToRoute('user_create');
    }
}
