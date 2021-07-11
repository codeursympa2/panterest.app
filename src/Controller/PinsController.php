<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 *Decommenter@IsGranted("ROLE_ADMIN",null,"Accès non autorisé")
 */
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
        #$this->denyAccessUnlessGranted('ROLE_ADMIN',null,'Accès non autorisé');
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
    public function create_pin(Request $request,UserRepository $user): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error','Veillez vous connecter');
            return $this->redirectToRoute('app_login');
        }
       $pin=new pin;
       $form= $this->createForm(PinType::class,$pin,['method'=>'POST']);
           
        //recuperation données du formulaire
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            //comme on est au niveau d'un controller on utilise $this->getUser()
            //pour acceder à l'utilisateur
            $anrchi=$this->getUser();
            $pin->setUser($anrchi);
            $this->em->persist($pin);
            $this->em->flush();
            //ajout du message flush
            $this->addFlash("success","Pin crée avec succès");

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
        $this->addFlash("success","Modification du pin effcetuée");
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
            $this->addFlash('info ','Pin supprimé avec succès');
        }
        return $this->redirectToRoute('home');
    }
    
}
