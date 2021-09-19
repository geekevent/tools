<?php


namespace App\Controller;

use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeEnlarge;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route(path: '/admin/qrcode', name: 'qrcode_')]
class QrCodeController extends AbstractController
{
    #[Route(path: '', name: 'create')]
    public function create(BuilderInterface $customQrCodeBuilder, PngWriter $pngWriter): Response
    {
        $qrCode = $customQrCodeBuilder
//            ->data($this->generateUrl('main_home', referenceType:UrlGeneratorInterface::ABSOLUTE_URL))
            ->data('https://japansun.fr')
            ->size(400)
            ->margin(20)
            ->logoResizeToHeight(150)
            ->backgroundColor(new Color(229, 28 ,32 ))
            ->foregroundColor(new Color(0, 0, 0))
            ->logoPath('assets/small.png')
            ->build();

        $qrCode->saveToFile('test.png');

        return $this->render('QrCode/create.html.twig', [
            'qrCode' => $qrCode,
        ]);
    }
}