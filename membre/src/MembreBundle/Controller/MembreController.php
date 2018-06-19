<?php

namespace MembreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use MembreBundle\Entity\User;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class MembreController extends Controller
{
    public function indexAction()
    {
      return $this->redirectToRoute('membre_login');
    }

    public function loginAction(Request $request)
    {
  
    // Si le visiteur est déjà identifié, on le redirige vers l'accueil
    if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
     return $this->redirectToRoute('home');
    }

   $authenticationUtils = $this->get('security.authentication_utils');

   // get the login error if there is one
    $error = $authenticationUtils->getLastAuthenticationError();

    // last username entered by the user
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('@Membre/Membre/login.html.twig', array(
        'last_username' => $lastUsername,
        'error'         => $error,
    ));
    }
    
    public function registrationAction(Request $request)
    {
        $user= new User();
       // create the form 
       $form=$this->get('form.factory')->createBuilder(FormType::class, $user)
            ->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
      ->add('save',SubmitType::class)
      ->getForm()
    ;
       $form->handleRequest($request);    
   // handle the form 
    if($form->isSubmitted() && $form->isValid())
    {
    
       // Encode the new users password
            $encoder = $this->get('security.password_encoder');
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
             
           
       // save
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        // 
        return $this->redirectToRoute('membre_login');
    } 

       return $this->render('@Membre/Membre/registration.html.twig',array('form'=> $form->createView(),
   ));
  
    }
    public function homeAction()
    {
      $repository = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('MembreBundle:User');

      $users = $repository->findAll();
      return $this->render('@Membre/Membre/home.html.twig',array('users'=>$users));
    }

    public function deleteAction($id)
    {
      
      
      $currentUserId =$this->getUser()->getId();
      
      $repository=$this
      ->getDoctrine()
      ->getManager()
      ->getRepository('MembreBundle:User');
      $user=$repository->find($id);
      if(!$user)
        { return $this->redirectToRoute('home'); }
      $em=$this->getDoctrine()->getManager();
      $em->remove($user);
      $em->flush();
      if($currentUserId==$id){ return $this->redirectToRoute('logout');} 
      return $this->redirectToRoute('home');
    }

    public function updateAction( Request $request,$id)
    {
       $user=$this->getDoctrine()->getManager()->getRepository('MembreBundle:User')->find($id);
      // create the form 
       $form=$this->get('form.factory')->createBuilder(FormType::class, $user)
            ->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
      ->getForm()
    ;
       $form->handleRequest($request);
       // handle the form 
     if($form->isSubmitted() && $form->isValid())
     {
    
      // Encode the new users password
            $encoder = $this->get('security.password_encoder');
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
             
           
       // save
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('home');
     }
     return $this->render('@Membre/Membre/update_form.html.twig',array('form'=>$form->createView()));

  }

  public function addAction(Request $request)
  {
    $user= new User();
       // create the form 
       $form=$this->get('form.factory')->createBuilder(FormType::class, $user)
            ->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
      ->add('save',SubmitType::class)
      ->getForm()
    ;
       $form->handleRequest($request);    
   // handle the form 
    if($form->isSubmitted() && $form->isValid())
    {
    
       // Encode the new users password
            $encoder = $this->get('security.password_encoder');
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
             
           
       // save
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        // 
        return $this->redirectToRoute('home');
    } 
       
       return $this->render('@Membre/Membre/add.html.twig',array('form'=> $form->createView(),
   ));
  }

}
