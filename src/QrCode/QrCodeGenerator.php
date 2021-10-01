<?php


namespace App\QrCode;


use App\Entity\Enigma;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class QrCodeGenerator
{
    private RouterInterface $router;

    private BuilderInterface $builder;

    public function __construct(RouterInterface $router, BuilderInterface $builder)
    {
        $this->router = $router;
        $this->builder = $builder;
    }

    public function generate(Enigma $enigma): ResultInterface
    {
        $code = uniqid();
        $enigma->code = $code;

        return $this->builder
            ->data($this->router->generate(
                name:'visitor_enigma_check',
                parameters: [
                    $code => 'enigma'
                ],
                referenceType:UrlGeneratorInterface::ABSOLUTE_URL
            ))
            ->size(350)
            ->margin(20)
            ->backgroundColor(new Color(255, 255 ,255 ))
            ->foregroundColor(new Color(0, 0, 0))
            ->build();
    }
}