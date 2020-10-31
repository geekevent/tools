<?php

declare(strict_types=1);

namespace App\Controller\Account;

use App\Controller\AbstractToolsController;
use App\Entity\Account\Module;
use App\Entity\Account\Role;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/roles", name="module_assignation") */
class ModuleAssignationController extends AbstractToolsController
{
    /**
     * @Route(
     *     "/modules",
     *     methods={"GET"},
     *     name="_list",
     *     options={
     *         "module": {"name": "parameters", "title": "Paramètres"},
     *         "displayed": true,
     *         "title": "Assignation de module"
     *     }
     * )
     */
    public function getRolesAndModules(Request $request): Response
    {
        /** @var Role[] $roles */
        $roles = $this->findBy(Role::class);
        $checkedModules = [];
        foreach ($roles as $role) {
            $modules = [];

            foreach ($role->getModules() as $module) {
                $modules[$module->getId()] = $module->getId();
            }

            $checkedModules[$role->getId()] = $modules;
        }

        return $this->render(
            'Account/ModuleAssignations.html.twig',
            [
                'roles' => $roles,
                'modules' => $this->findBy(Module::class, []),
                'checkedModules' => $checkedModules,
                'action' => '',
            ],
            null,
            $request
        );
    }

    /**
     * @Route(
     *     "/modules",
     *     methods={"POST"},
     *     name="_save",
     * )
     */
    public function saveRolesAndModules(Request $request): Response
    {
        $roles = $request->request->get('roles', []);
        foreach ($roles as $roleId => $modules) {
            /** @var Role $role */
            $role = $this->findOne(Role::class, $roleId);
            $role->resetModules();
            foreach ($modules as $moduleId) {
                /** @var Module $module */
                $module = $this->findOne(Module::class, $moduleId);
                $role->addModule($module);
            }
        }

        $this->update();

        return $this->redirectToRoute('module_assignation_list');
    }
}
