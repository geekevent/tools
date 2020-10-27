<?php

namespace App\Controller\Account;

use App\Controller\AbstractToolsController;
use App\Entity\Account\Role;
use App\Form\Type\Account\RoleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RoleController extends AbstractToolsController
{
    /**
     * @Route("/roles", methods={"GET"}, name="list_role")
     */
    public function getRoles(Request $request): Response
    {
        $role = new Role();
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        return $this->render(
            'Account/Roles.html.twig',
            [
                'form' => $form->createView(),
                'items' => $this->findBy(Role::class, []),
                'action' => '',
            ]
        );
    }

    /**
     * @Route("/roles/{roleId}", methods={"GET"}, name="role")
     */
    public function getRole(Request $request, int $roleId): Response
    {
        /** @var Role|null $role */
        $role = $this->findOne(Role::class, $roleId);
        if (null === $role) {
            throw new NotFoundHttpException('Le role n\'existe pas');
        }

        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        return $this->render(
            'Account/Roles.html.twig',
            [
                'form' => $form->createView(),
                'items' => $this->findBy(Role::class, []),
                'action' => $role->getId(),
            ]
        );
    }

    /**
     * @Route("/roles", methods={"POST"}, name="create_role")
     */
    public function createRole(Request $request): Response
    {
        $role = new Role();

        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->getData();
            $this->save($role);

            return $this->redirectToRoute('list_role');
        }

        return $this->render(
            'Account/Roles.html.twig',
            [
                'form' => $form->createView(),
                'items' => $this->findBy(Role::class, []),
                'action' => '',
            ]
        );
    }

    /**
     * @Route("/roles/{roleId}", methods={"POST"}, name="update_role")
     */
    public function updateRole(Request $request, int $roleId): Response
    {
        /** @var Role|null $role */
        $role = $this->findOne(Role::class, $roleId);
        if (null === $role) {
            throw new BadRequestHttpException('Le role n\'existe pas');
        }

        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->update();

            return $this->redirectToRoute('list_role');
        }

        return $this->render(
            'Account/Roles.html.twig',
            [
                'form' => $form->createView(),
                'items' => $this->findBy(Role::class, []),
                'action' => $role->getId(),
            ]
        );
    }

    /**
     * @Route("/roles/{roleId}/delete", methods={"GET"}, name="delete_role")
     */
    public function deleteRole(Request $request, int $roleId): Response
    {
        /** @var Role|null $role */
        $role = $this->findOne(Role::class, $roleId);
        if (null === $role) {
            throw new NotFoundHttpException('Le role n\'existe pas');
        }

        $this->delete($role);

        return $this->redirectToRoute('list_role');
    }
}
