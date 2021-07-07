<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em=$em;
    }
    /**
     * @Route("/account", name="account",methods="GET")
     */
    public function show(): Response
    {
        return $this->render('account/show.html.twig');
    }
    /**
     * @Route("/account/edit", name="account_edit",methods="GET|POST")
     */
    public function edit(Request $request): Response
    {
        $user=$this->getUser();

        $form=$this->createForm(UserFormType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->em->flush();

            $this->addFlash(
               'success',
               'Mis à jour du profile éffectuée '
            );
            return $this->redirectToRoute('account');
        }
        return $this->render('account/edit.html.twig',[
            'form'=>$form->createView()
        ]);
    }
     /**
     * @Route("/account/change-password", name="account_change_pass",methods="GET|POST")
     */
    public function change_password(Request $request,UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user=$this->getUser();
        $form=$this->createForm(ChangePasswordFormType::class,null,['current_password_is_required'=>true]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user->setPassword(
                $passwordEncoder->encodePassword($user,$form['plainPassword']->getData())
            );
            $this->em->flush();
            $this->addFlash(
               'success',
               'Mot de passe mis à jour avec succès'
            );
            return $this->redirectToRoute("account");

        }
        return $this->render('account/change_password.html.twig',
        [
            "form"=>$form->createView()
        ]
    );
    }
}
