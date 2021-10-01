<?php


namespace App\Controller\Visitor;

use App\Entity\Enigma;
use App\Entity\VisitorAnswer;
use App\Repository\EnigmaRepository;
use App\Repository\VisitorAnswerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/visitor/enigma', name: 'visitor_enigma_')]
class EnigmaController extends AbstractController
{
    #[Route(path: '/check', name: 'check')]
    public function check(Request $request, EnigmaRepository $repository): Response
    {
        $parameters = array_flip($request->query->all());
        $code = $parameters['enigma'] ?? null;

        if (null === $code) {
            return $this->render('Enigma/Visitor/cheat.html.twig', [
                'message' => 'OUPS!'
            ]);
        }
        $request->getSession()->set('code', bin2hex(random_bytes(2)));

        $enigma = $repository->findOneBy(['code' => $code]);

        return $this->redirectToRoute('visitor_enigma_read', ['id' => $enigma->id]);
    }

    #[Route(path: '/{id}', name: 'read')]
    #[Entity(Enigma::class, expr: 'repository.find(id)')]
    public function read(Request $request, Enigma $enigma): Response
    {
        if (null === $request->getSession()->get('code', null)) {
            return $this->render('Enigma/Visitor/cheat.html.twig', [
                'message' => 'OUPS!'
            ]);
        }

        return $this->render('Enigma/Visitor/read.html.twig', [
            'enigma' => $enigma,
        ]);
    }

    #[Route(path: '/{id}/validate', name: 'validate', methods: [Request::METHOD_POST])]
    #[Entity(Enigma::class, expr: 'repository.find(id)')]
    public function validate(Request $request, Enigma $enigma, VisitorAnswerRepository $repository): Response
    {
        $code = $request->getSession()->get('code', null);
        if (null === $code) {
            return $this->render('Enigma/Visitor/cheat.html.twig', [
                'message' => 'OUPS!'
            ]);
        }

        $request->getSession()->set('code', null);

        $answer = $request->request->get('answer');
        if ((string) $answer !== $enigma->answer) {
            return $this->render('Enigma/Visitor/notvalid.html.twig', [
                'enigma' => $enigma,
            ]);
        }
        $email = $request->request->get('email');
        $email = empty($email)? null : $email;
        if (null !== $email) {
            $code = $repository->getCode($code);
            $visitorAnswer = $repository->findOneBy(['enigma' => $enigma, 'email' => $email]);
            if (null === $visitorAnswer) {
                $visitorAnswer = new VisitorAnswer();
            }
            $visitorAnswer->code = $code;
            $visitorAnswer->email = $email;
            $visitorAnswer->enigma = $enigma;

            $repository->persist($visitorAnswer);
            $repository->flush();
        }

        return $this->render('Enigma/Visitor/validate.html.twig', [
            'enigma' => $enigma,
            'code' => $code,
            'participate' => null !== $email,
        ]);
    }
}
