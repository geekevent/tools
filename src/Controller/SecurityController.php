<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Account\Account;
use App\Form\Type\Account\PasswordResetType;
use App\Service\Email\AccountResetEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/** @Route("", name="app") */
class SecurityController extends AbstractToolsController
{
    private AccountResetEmail $resetMailService;

    public function __construct(EntityManagerInterface $entityManager, AccountResetEmail $resetMailService)
    {
        parent::__construct($entityManager);
        $this->resetMailService = $resetMailService;
    }

    /**
     * @Route("/login", name="_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
            ]
        );
    }

    /**
     * @Route("/logout", name="_logout")
     */
    public function logout(): Response
    {
        return $this->render(
            'security/login.html.twig',
            []
        );
    }

    /**
     * @Route(
     *     "/reset/{token}",
     *     methods={"get", "post"},
     *     name="_set"
     * )
     */
    public function setPassword(Request $request, string $token): Response
    {
        /** @var Account|null $account */
        $account = $this->findOneBy(Account::class, ['resetToken' => $token]);

        if (null === $account) {
            throw new NotFoundHttpException('Aucun compte associé a ce token');
        }
        $form = $this->createForm(PasswordResetType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $account->setResetToken(null);
            $this->update();
        }

        return $this->render(
            'Account/PasswordSet.html.twig',
            [
                'isNew' => null === $account->getPassword(),
                'form' => $form->createView(),
            ],
            null,
            $request
        );
    }

    /**
     * @Route(
     *     "/reset",
     *     methods={"get", "post"},
     *     name="_reset"
     * )
     */
    public function resetPassword(Request $request): Response
    {
        $login = $request->get('login', false);
        if (false !== $login) {
            $this->addFlash('notice', 'Un mail vous as été envoyé pour modifier votre mot de passe');
            /** @var Account|null $account */
            $account = $this->findOneBy(Account::class, [
                'login' => $login,
            ]);
            if (null === $account) {
                return $this->redirectToRoute('app_login');
            }

            $email = $this->resetMailService->createMailData($account);
            $this->resetMailService->send($email);
            $account->setPassword(null);
            $this->update();

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'Account/PasswordReset.html.twig',
            [
                'login' => '',
            ]
        );
    }
}
