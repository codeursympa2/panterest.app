<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(PinRepository $pr): Response
    {
        $pins=$pr->findBy([],['createdAt'=>'DESC']);
        return $this->render('pins/index.html.twig',compact('pins'));
    }
    /**
     * @Route("/pin/view/{id<[0-9]+>}", name="view_pin")
     */
    public function view_pin(Pin $pin): Response
    {
        return $this->render('pins/view_pin.html.twig',compact('pin'));
    }
}
