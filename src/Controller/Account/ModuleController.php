<?php

declare(strict_types=1);

namespace App\Controller\Account;

use App\Controller\AbstractToolsController;
use App\Entity\Account\Module;
use App\Form\Type\Account\ModuleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/modules", name="module") */
class ModuleController extends AbstractToolsController
{
    /**
     * @Route(
     *     "/",
     *     methods={"GET"},
     *     name="_list",
     *     options={
     *         "module": {"name": "parameters", "title": "Paramètres"},
     *         "displayed": true,
     *         "title": "Module"
     *     }
     * )
     */
    public function getModules(Request $request): Response
    {
        $module = new Module();
        $form = $this->createForm(ModuleType::class, $module);
        $form->handleRequest($request);

        return $this->render(
            'Account/Modules.html.twig',
            [
                'form' => $form->createView(),
                'items' => $this->findBy(Module::class, []),
                'action' => '',
            ],
            null,
            $request
        );
    }

    /**
     * @Route("/{moduleId}", methods={"GET"}, name="_details")
     */
    public function getModule(Request $request, int $moduleId): Response
    {
        /** @var Module|null $module */
        $module = $this->findOne(Module::class, $moduleId);
        if (null === $module) {
            throw new NotFoundHttpException('Le module n\'existe pas');
        }

        $form = $this->createForm(ModuleType::class, $module);
        $form->get('identifier')->setData($module->getIdentifier());
        $form->handleRequest($request);

        return $this->render(
            'Account/Modules.html.twig',
            [
                'form' => $form->createView(),
                'items' => $this->findBy(Module::class, []),
                'action' => $module->getId(),
            ],
            null,
            $request
        );
    }

    /**
     * @Route("/{moduleId}", methods={"POST"}, name="_update")
     */
    public function createModule(Request $request, int $moduleId): Response
    {
        /** @var Module|null $module */
        $module = $this->findOne(Module::class, $moduleId);

        if (null === $module) {
            throw new NotFoundHttpException('Le module n\'existe pas');
        }

        $form = $this->createForm(ModuleType::class, $module);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->update();

            return $this->redirectToRoute('module_list');
        }

        return $this->render(
            'Account/Modules.html.twig',
            [
                'form' => $form->createView(),
                'items' => $this->findBy(Module::class, []),
                'action' => $module->getId(),
            ],
            null,
            $request
        );
    }
}
