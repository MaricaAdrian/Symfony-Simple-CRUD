<?php

    namespace App\Controller;

    use App\Entity\Admin;

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
    use Symfony\Component\Form\Extension\Core\Type\PasswordType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


    class AdminLoginController extends Controller
    {



        /**
        * @Route("/login_admin", name="admin_login")
        * @Method({"GET", "POST"})
        */
        public function admin_login(AuthenticationUtils $authenticationUtils)
        {
            if ($this->isGranted('IS_AUTHENTICATED_FULLY') && $this->isGranted('ROLE_ADMIN'))
            {
                return $this->redirectToRoute('article_viewall');
            }

            // get the login error if there is one
           $error = $authenticationUtils->getLastAuthenticationError();

            // last username entered by the user
            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render('security/admin_login.html.twig', array(
                'last_username' => $lastUsername,
                'error'         => $error,
            ));
        }


        /**
        * @Route("/admin/logout", name="admin_logout")
        * @Method({"GET"})
        */
        public function admin_logout()
        {

        }


        /**
        * @Route("/admin/profile", name="admin_profile")
        * @Method({"GET"})
        */
        public function admin_profile()
        {
            $admin = $this->getUser();
            $admin = $this->getDoctrine()->getRepository(Admin::class)->find($admin->getID());
            return $this->render('security/admin_profile.html.twig', array('admin' => $admin));
        }

        /**
        * @Route("/admin/profile/edit", name="admin_profile_edit")
        * @Method({"GET"})
        */
        public function admin_profile_edit(Request $request, UserPasswordEncoderInterface $passwordEncoder)
        {
            $admin = $this->getUser();
            $admin = $this->getDoctrine()->getRepository(Admin::class)->find($admin->getID());


            $form = $this->createFormBuilder($admin)
            ->add('email', EmailType::class, array('attr' => array('class' => 'form-control')))
            ->add('plainPassword', RepeatedType::class, array(
               'type' => PasswordType::class,
               'first_options'  => array('label' => 'Password', 'attr' => array('class' => 'form-control')),
               'second_options' => array('label' => 'Repeat Password', 'attr' => array('class' => 'form-control')),

            ))
            ->add('save', SubmitType::class, array('label' => 'Edit profile', 'attr' => array('class' => 'btn btn-primary mt-3')))
            ->getForm();


            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $password = $passwordEncoder->encodePassword($admin, $admin->getPlainPassword());
                $admin->setPassword($password);
                $admin = $form->getData();
                $db = $this->getDoctrine()->getManager();
                $db->persist($admin);
                $db->flush();
                $this->get('session')->getFlashBag()->add('message', 'Profile edited successfully');
                return $this->redirectToRoute('admin_profile');
            }

            return $this->render('security/admin_profile_edit.html.twig', array('form' => $form->createView(),
                                                                           'admin' => $admin));
        }


    }

?>
