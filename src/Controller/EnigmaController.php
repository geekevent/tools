<?php


namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Enigma;
use App\Entity\User;
use App\Form\Enigma\Create;
use App\Form\Answer\Create as CreateAnswer;
use App\QrCode\QrCodeGenerator;
use App\Repository\AnswerRepository;
use App\Repository\EnigmaRepository;
use App\Repository\VisitorAnswerRepository;
use Dompdf\Dompdf;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Color\Color;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/enigma', name: 'enigma_')]
class EnigmaController extends AbstractController
{
    private const ROLE = 'ROLE_ORGA';

    #[Route(path: '', name: 'add')]
    #[IsGranted(self::ROLE)]
    public function add(Request $request, EnigmaRepository $repository): Response
    {
        $enigma = new Enigma();

        $form = $this->createForm(Create::class, $enigma);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->persist($enigma);
            $repository->flush();

            return $this->redirectToRoute('enigma_add');
        }


        return $this->render('Enigma/lists.html.twig', [
            'items' => $repository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}', name: 'update', requirements: ['id' => '\d+'])]
    #[Entity(Enigma::class, expr: 'repository.find(id)')]
    #[IsGranted(self::ROLE)]
    public function update(Request $request, Enigma $enigma, EnigmaRepository $repository): Response
    {
        $form = $this->createForm(Create::class, $enigma);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->flush();

            return $this->redirectToRoute('enigma_add');
        }


        return $this->render('Enigma/lists.html.twig', [
            'items' => $repository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/delete', name: 'delete', requirements: ['id' => '\d+'])]
    #[Entity(Enigma::class, expr: 'repository.find(id)')]
    #[IsGranted(self::ROLE)]
    public function delete(Enigma $enigma, EnigmaRepository $repository): Response
    {
        $repository->remove($enigma);
        $repository->flush();

        return $this->redirectToRoute('enigma_add');
    }

    #[Route(path: '/{enigma}/answer/', name: 'answer_add', requirements: ['enigma' => '\d+'])]
    #[Entity(Enigma::class, expr: 'repository.find(enigma)')]
    #[IsGranted(self::ROLE)]
    public function addAnswer(Request $request, Enigma $enigma, AnswerRepository $repository)
    {
        $answer = new Answer();

        $form = $this->createForm(CreateAnswer::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $answer->enigma = $enigma;
            $repository->persist($answer);
            $repository->flush();

            return $this->redirectToRoute('enigma_answer_add', ['enigma' => $enigma->id]);
        }


        return $this->render('Enigma/Answer/lists.html.twig', [
            'items' => $repository->findAll(),
            'form' => $form->createView(),
            'enigma' => $enigma,
        ]);
    }

    #[Route(path: '/{enigma}/answer/{id}', name: 'answer_update', requirements: ['id' => '\d+', 'enigma' => '\d+'])]
    #[Entity(Answer::class, expr: 'repository.find(id)')]
    #[IsGranted(self::ROLE)]
    public function updateAnswer(Request $request, Answer $answer, AnswerRepository $repository)
    {
        $form = $this->createForm(CreateAnswer::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->flush();

            return $this->redirectToRoute('enigma_answer_add', ['enigma' => $answer->enigma->id]);
        }


        return $this->render('Enigma/Answer/lists.html.twig', [
            'items' => $repository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{enigma}/answer/{id}/delete', name: 'answer_delete', requirements: ['id' => '\d+', 'enigma' => '\d+'])]
    #[Entity(Answer::class, expr: 'repository.find(id)')]
    #[IsGranted(self::ROLE)]
    public function deleteAnswer(Answer $answer, AnswerRepository $repository)
    {
        $repository->remove($answer);
        $repository->flush();

        return $this->redirectToRoute('enigma_answer_add', ['enigma' => $answer->enigma->id]);
    }

    #[Route(path: '/{id}/qrCode', name: 'qrCode', requirements: ['id' => '\d+'])]
    #[Entity(Enigma::class, expr: 'repository.find(id)')]
    #[IsGranted(self::ROLE)]
    public function getQrCode(EnigmaRepository $repository, Enigma $enigma, QrCodeGenerator $generator): Response
    {
        $qrCode = $generator->generate($enigma);
        $repository->flush();

        return new QrCodeResponse($qrCode);
    }

    #[Route(path: '/check', name: 'check')]
    #[IsGranted(User::ROLE_STAFF_ACCUEIL)]
    public function checkResponse(Request $request, VisitorAnswerRepository $repository): Response
    {
        $email = $request->request->get('email');
        $email = empty($email) ? null : $email;

        $answers = [];
        if (null !== $email) {
            $answers = $repository->findBy(['email' => $email]);
        }

        return $this->render('Enigma/check.html.twig', [
            'answers' => $answers,
        ]);
    }
}
