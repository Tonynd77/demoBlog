<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManager;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class BlogController extends AbstractController
{

    /**
     * Méthode permettant de définir la page d'accueil du blog, le point d'entré du site
     * 
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('blog/home.html.twig', [
            'title' => 'Bienvenue sur le blog Symfony',
            'age'   => 25
        ]);
    }


    /**
     * Methode permettant d'afficher tout les articles du blog
     * 
     * @Route("/blog", name="blog")
     */
    public function blog(ArticleRepository $repoArticle): Response
    {
        // $repoArticle = $this->getDoctrine()->getRepository(Article::class);
        dump($repoArticle);

        $articles = $repoArticle->findAll();
        dump($articles);

        return $this->render('blog/blog.html.twig', [
            'articles'   => $articles 
        ]);
    }

    /**
     * @Route("/blog/new" , name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function create(Request $request, EntityManagerInterface $manager, Article $article = null, SluggerInterface $slugger): Response
    {
        // La classe Request perrmet de stocker les données http véhiculé par les superglobales ($_POST, $_GET, $_FILES ect...)
        // dump($request);

        /* if($request->request->count() > 0)
        {
            $article = new Article;

            $article->setTitre($request->request->get('titre'))
                    ->setContenu($request->request->get('contenu'))
                    ->setImage($request->request->get('image'))
                    ->setDate(new \DateTime());

            dump($article);

            $manager->persist($article);
            $manager->flush();
        } */

        // Si la variable $article N'EST PAS (null), si elle ne contient aucun article de la BDD, cela veut dire nous avons envoyé la route '/blog/new', c'est une insertion, on entre dans le IF et on crée une nouvelle instance de l'entité Article, création d'un nouvel article
        // Si la variable $article contient un article de la BDD, cela veut dire que nous avons envoyé la route '/blog/id/edit', c'est une modifiction d'article, on entre pas dans le IF, $article ne sera pas null, il contient un article de la BDD, l'article à modifier

        if(!$article)
        {
            $article = new Article;
        }

        /* $article->setTitre("Titre à la con")
                ->setContenu("Contenu à la con"); */

        $formArticle = $this->createForm(ArticleType::class, $article);

        $formArticle->handleRequest($request);

        dump($article);

        if($formArticle->isSubmitted() && $formArticle->isValid())
        {
            if(!$article->getId())
            {
                $article->setDate(new \DateTime());
            }

            // Traitement de l'image
            $image = $formArticle->get('image')->getData();
            // dump($image);

            if($image)
            {
                // On rrécuperre le nom d'origine du fichier
                $nomOrigineFichier = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // dump($nomOrigineFichier);

                $secureNomFichier = $slugger->slug($nomOrigineFichier);
                // dump($secureNomFichier);

                $nouveauNomFichier = $secureNomFichier . '-' . uniqid() . '.' . $image->guessExtension();
                dump($nouveauNomFichier);

                try
                {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $nouveauNomFichier
                    );
                }
                catch(FileException $e)
                {

                }

                $article->setImage($nouveauNomFichier);
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', [
                'id' => $article->getId()
            ]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle'   => $formArticle->createView(),
            'editMode'      => $article->getId(), // Si editMode dans le template renvoi TRUE, alors l'article possède un ID, c'est une modification sinon elle renvoi FALSE, c'est une insertion
            // elle renvoi FALSE, c'est une insertion
            'imageArticle'  => $article->getImage() // on transmet l'image de l'article afin de l'afficher en cas de modification

        ]);
    }

    /**
     * Methode permettant d'afficher le détail d'un article
     * {id} : permet de définir une route paramétré qui va receptionner un id d'1 article de la bdd
     * $id va receptinné l'id trransmit dans la route
     * 
     * @Route ("/blog/{id}", name="blog_show")
     */

    // INJECTION DEPANDANCE
    public function blog_show(Request $request, Article $article, EntityManagerInterface $manager): Response
    {
        // dump($id);

        // $repoArticle = $this->getDoctrine()->getRepository(Article::class);
        // $repoArticle : objet issu de la classe ArticleRepository

        // $article = $repoArticle->find($id);
        dump($article);

        $comment = new Comment;

        $formComment = $this->createForm(CommentType::class, $comment);

        dump($request);

        $formComment->handleRequest($request);

        dump($comment);

        if($formComment->isSubmitted() && $formComment->isValid())
        {
            $comment->setDate(new \DateTime)
                    ->setArticle($article);

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success', "Le commentaire a été posté avec succès !");

            return $this->redirectToRoute('blog_show', [
                'id' => $article->getId()
            ]);

        }

        return $this->render('blog/blog_show.html.twig', [
            'article' => $article,
            'formComment' => $formComment->createView()
        ]);
    }
}
