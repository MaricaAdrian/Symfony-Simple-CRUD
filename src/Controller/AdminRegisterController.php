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

    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
    use Symfony\Component\Form\Extension\Core\Type\PasswordType;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

    class AdminRegisterController extends Controller
    {

        /**
        * @Route("/register_admin", name="admin_register")
        * @Method({"GET", "POST"})
        */
        public function admin_register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
        {

            if ($this->isGranted('IS_AUTHENTICATED_FULLY') && $this->isGranted('ROLE_ADMIN'))
            {
                return $this->redirectToRoute('article_viewall');
            }


            $register = new Admin();

            $form = $this->createFormBuilder($register)
            ->add('email', EmailType::class, array('attr' => array('class' => 'form-control')))
            ->add('username', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('plainPassword', RepeatedType::class, array(
               'type' => PasswordType::class,
               'first_options'  => array('label' => 'Password', 'attr' => array('class' => 'form-control')),
               'second_options' => array('label' => 'Repeat Password', 'attr' => array('class' => 'form-control')),

            ))
            ->add('save', SubmitType::class, array('label' => 'Add article', 'attr' => array('class' => 'btn btn-primary mt-3')))
            ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $password = $passwordEncoder->encodePassword($register, $register->getPlainPassword());
                $register->setPassword($password);
                $register = $form->getData();
                $db = $this->getDoctrine()->getManager();
                $db->persist($register);
                $db->flush();
                $this->get('session')->getFlashBag()->add('message', 'Account succssefully created');
                return $this->redirectToRoute('register_admin');
            }

            return $this->render('security/admin_register.html.twig', array('form' => $form->createView()));
        }

    }

?>
