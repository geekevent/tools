<?php

declare(strict_types=1);

namespace App\Controller\Admin\Account;

use App\Controller\AbstractToolsController;
use App\Entity\Account\Account;
use App\Form\Type\Account\AccountType;
use App\Form\Type\Account\PasswordResetType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/admin/accounts", name="account") */
class AccountController extends AbstractToolsController
{
    /**
     * @Route(
     *     "",
     *     methods={"GET"},
     *     name="_list",
     *     options={
     *         "displayed": true,
     *         "title": "Compte"
     *     }
     * )
     */
    public function getRoles(Request $request): Response
    {
        $account = new Account();
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        return $this->render(
            'Account/Accounts.html.twig',
            [
                'form' => $form->createView(),
                'items' => $this->findBy(Account::class, []),
                'action' => '',
            ],
            null,
            $request
        );
    }

    /**
     * @Route(
     *     "",
     *     methods={"POST"},
     *     name="_create"
     * )
     */
    public function createAccount(Request $request): Response
    {
        $account = new Account();
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $account = $form->getData();
            $this->save($account);

            return $this->redirectToRoute('account_list');
        }

        return $this->render(
            'Account/Accounts.html.twig',
            [
                'form' => $form->createView(),
                'items' => $this->findBy(Account::class, []),
                'action' => '',
            ],
            null,
            $request
        );
    }

    /**
     * @Route(
     *     "/{token}",
     *     methods={"get", "post"},
     *     name="_reset"
     * )
     */
    public function resetPassword(Request $request, string $token): Response
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
            'Account/PasswordReset.html.twig',
            [
                'isNew' => null === $account->getPassword(),
                'form' => $form->createView(),
            ],
            null,
            $request
        );
    }
}
