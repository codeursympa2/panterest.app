<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Security;

/**
 * decom@IsGranted("ROLE_USER")
 */
class AccountController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em=$em;
    }
    /**
     * @Route("/account", name="account",methods="GET")
     * @IsGranted("ROLE_USER")
     */
    public function show(): Response
    {
        return $this->render('account/show.html.twig');
    }
    /**
     * @Route("/account/edit", name="account_edit",methods="GET|PATCH")
     * On specifie que pour changer les information du compte il faut etre connecté
     * sans le cookie remember me. Pour plus de sécurité
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function edit(Request $request,Security $security): Response
    {
        //dd($security->IsGranted("IS_REMEMBERED"));
        $user=$this->getUser();

        $form=$this->createForm(UserFormType::class,$user,[
            'method'=>'PATCH'
        ]);
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
      * PATCH pour une petite modification
     * @Route("/account/change-password", name="account_change_pass",methods="GET|PATCH")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function change_password(Request $request,UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user=$this->getUser();
        $form=$this->createForm(ChangePasswordFormType::class,null,['current_password_is_required'=>true
        ,'method'=>'PATCH'
    ]);
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
