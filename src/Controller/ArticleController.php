<?php

    namespace App\Controller;

    use App\Entity\Article;

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

    class ArticleController extends Controller
    {

        /**
        * @Route("/admin/{message}", name="article_viewall_message")
        * @Method({"GET"})
        */
        public function indexMessage($message)
        {
            $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
            return $this->render('articles/index.html.twig', array('articles' => $articles, 'message' => $message));
        }

        /**
        * @Route("/admin/", name="article_viewall")
        * @Method({"GET"})
        */
        public function index()
        {
            $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
            return $this->render('articles/index.html.twig', array('articles' => $articles));
        }

        /**
        * @Route("/admin/article/view/{id}", name="article_view")
        */

        public function show($id)
        {
            $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

            return $this->render('articles/show.html.twig', array('article' => $article));
        }

        /**
        * @Route("/admin/article/new", name="article_new")
        * @Method({"GET", "POST"})
        */
        public function new(Request $request)
        {
            $article = new Article();

            $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('author', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('content', TextareaType::class, array('attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array('label' => 'Add article', 'attr' => array('class' => 'btn btn-primary mt-3')))
            ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $article = $form->getData();
                $db = $this->getDoctrine()->getManager();
                $db->persist($article);
                $db->flush();
                return $this->redirectToRoute('article_viewall_message', array('message' => 'Article added successfully to the database.'));
            }

            return $this->render('articles/new.html.twig', array('form' => $form->createView()));
        }

        /**
        * @Route("/admin/article/edit/{id}", name="article_edit")
        * @Method({"GET", "POST"})
        */
        public function edit(Request $request, $id)
        {
            $article = new Article();
            $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

            $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('author', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('content', TextareaType::class, array('attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array('label' => 'Update article', 'attr' => array('class' => 'btn btn-primary mt-3')))
            ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $db = $this->getDoctrine()->getManager();
                $db->flush();

                return $this->redirectToRoute('article_viewall_message', array('message' => 'Article (ID '.$id.') edited successfully.'));
            }

            return $this->render('articles/edit.html.twig', array('form' => $form->createView()));
        }


        /**
        * @Route("/admin/article/delete/{id}")
        * @Method({"DELETE"})
        */
        public function delete(Request $request, $id)
        {
            $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
            $db = $this->getDoctrine()->getManager();
            $db->remove($article);
            $db->flush();

            $response = "Successfully deleted article ".$article->getTitle()." from database.";
            // $response = new Response();
            // $response->setContent('Successfully deleted from database.');
            // $response->setStatusCode(Response::HTTP_OK);
            //
            // // prints the HTTP headers followed by the content
            // $response->send();


            return new Response($response);
        }

        // /**
        // * @Route("/article/save")
        // */
        // public function save()
        // {
        //     $db = $this->getDoctrine()->getManager();
        //
        //     $article = new Article();
        //     $article->setTitle("Article two");
        //     $article->setContent("Ceva2");
        //     $article->setAuthor("Not me");
        //
        //     $db->persist($article);
        //     $db->flush();
        //
        //     return new Response('Saved succesffuly, id is ' . $article->getId());
        // }
    }

?>
