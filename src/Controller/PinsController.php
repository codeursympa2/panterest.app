<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em=$em;
    }
    /**
     * @Route("/", name="home",methods="GET")
     */
    public function index(PinRepository $pr): Response
    {
        $pins=$pr->findBy([],['createdAt'=>'DESC']);
        return $this->render('pins/index.html.twig',compact('pins'));
    }
    /**
     * @Route("/pin/view/{id<[0-9]+>}", name="view_pin", methods="GET")
     */
    public function view_pin(Pin $pin): Response
    {
        return $this->render('pins/view_pin.html.twig',compact('pin'));
    }

     /**
     * @Route("/pin/create", name="create_pin",methods="GET|POST")
     */
    public function create_pin(Request $request): Response
    {
       $pin=new pin;
       $form= $this->createFormBuilder($pin)
            ->add('title',TextType::class)
            ->add('description',TextareaType::class)
            ->getForm();
        //recuperation données du formulaire
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            $this->em->persist($pin);
            $this->em->flush();
           return  $this->redirectToRoute('home');
        }
        return $this->render('pins/create_pin.html.twig',['form'=>$form->createView()]);
    }
    /**
     * put pour une mis à jour
     * @Route("/pin/{id<[0-9]+>}/edit",name="edit_pin",methods="GET|PUT")
     */
    public function edit_pin(Pin $pin,HttpFoundationRequest $request): Response
    {
      $form=$this->createForm(PinType::class,$pin,['method'=>'PUT']);
          
       $form->handleRequest($request);

       if($form->isSubmitted()&& $form->isValid()){
        $this->em->flush();
       return  $this->redirectToRoute('home');
    }
        return $this->render('pins/edit_pin.html.twig',['form'=>$form->createView(),'pin'=>$pin]);
    }
    /**
     * @Route("/pin/{id<[0-9]+>}/delete",name="delete_pin",methods="DELETE")
     */
    public function delete_pin(Pin $pin,Request $request):Response
    {
        $csrf=$this->isCsrfTokenValid('pin.delete' . $pin->getId(),$request->request->get('montoken'));
        if ($csrf== True){
            $this->em->remove($pin);
            $this->em->flush();
            //message flash
            //$this->addFlash('info ','Pin supprimé avec succès');
        }
        return $this->redirectToRoute('home');
    }
    
}
