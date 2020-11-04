<?php

declare(strict_types=1);

namespace App\Controller\Account;

use App\Controller\AbstractToolsController;
use App\Entity\Account\Role;
use App\Form\Type\Account\RoleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/roles", name="role") */
class RoleController extends AbstractToolsController
{
    /**
     * @Route(
     *     "/",
     *     methods={"GET"},
     *     name="_list",
     *     options={
     *         "module": {"name": "parameters", "title": "Paramètres"},
     *         "displayed": true,
     *         "title": "Role"
     *     }
     * )
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
            ],
            null,
            $request
        );
    }

    /**
     * @Route("/{roleId}", methods={"GET"}, name="_details")
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
            ],
            null,
            $request
        );
    }

    /**
     * @Route("/", methods={"POST"}, name="_create")
     */
    public function createRole(Request $request): Response
    {
        $role = new Role();

        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->getData();
            $this->save($role);

            return $this->redirectToRoute('role_list');
        }

        return $this->render(
            'Account/Roles.html.twig',
            [
                'form' => $form->createView(),
                'items' => $this->findBy(Role::class, []),
                'action' => '',
            ],
            null,
            $request
        );
    }

    /**
     * @Route("/{roleId}", methods={"POST"}, name="_update")
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

            return $this->redirectToRoute('role_list');
        }

        return $this->render(
            'Account/Roles.html.twig',
            [
                'form' => $form->createView(),
                'items' => $this->findBy(Role::class, []),
                'action' => $role->getId(),
            ],
            null,
            $request
        );
    }

    /**
     * @Route("/{roleId}/delete", methods={"GET"}, name="_delete")
     */
    public function deleteRole(Request $request, int $roleId): Response
    {
        /** @var Role|null $role */
        $role = $this->findOne(Role::class, $roleId);
        if (null === $role) {
            throw new NotFoundHttpException('Le role n\'existe pas');
        }

        $this->delete($role);

        return $this->redirectToRoute('role_list');
    }
}
