<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\CommentType;
use App\Form\CategoryType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Migrations\Configuration\EntityManager\ManagerRegistryEntityManager;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function admin(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/admin/articles", name="admin_articles")
     * @Route("/admin/article/{id}/remove", name="admin_article_remove")
     */
    public function adminArticles(EntityManagerInterface $manager, ArticleRepository $repository, Article $article = null): Response
    {
        dump($article);

        // via le manager qui permet de manipuler la BDD (insert, upadte, delete etc...), on execute la méthode getClassMetadata() afin de selectionner les méta données (primary key ,not null, noms des champs etc..) d'une entité (donc d'une table SQL), pour selectionner le nom des champs/colonnes de la table grace à la méthode getFieldNames()

        $colonnes = $manager->getClassMetadata(Article::class)->getFieldNames();
        dump($colonnes);

        $articles = $repository->findAll();
        dump($articles);

        // SUPRESSION ARTICLE EN BDD
        if($article)
        {
            $id = $article->getId();

            $manager->remove($article);
            $manager->flush();

            $this->addFlash('success', "L'article n° $id a été supprimé avec succés !");

            return $this->redirectToRoute('admin_articles');
        }

        return $this->render('admin/admin_articles.html.twig', [
            'colonnes' => $colonnes,
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/admin/categories", name="admin_categories")
     */
    public function adminCategory(EntityManagerInterface $manager, CategoryRepository $repoCategory): Response
    {
        $colonnes = $manager->getClassMetadata(Category::class)->getFieldNames();
        dump($colonnes);

        $categories = $repoCategory->findAll();
        dump($categories);

        return $this->render('admin/admin_category.html.twig', [
            'colonnes' => $colonnes,
            'categories' => $categories
        ]);
    }
    /**
     * @Route("/admin/categorie/new", name="admin_new_categorie")
     * @Route("/admin/categorie/{id}/edit", name="admin_edit_categorie")
     */
    public function createCategory(Request $request, EntityManagerInterface $manager, Category $categorie = null): Response
    {
        if(!$categorie)
        {
            $categorie = new Category;
        }

        $formCategory = $this->createForm(CategoryType::class, $categorie);

        $formCategory->handleRequest($request);

        dump($categorie);

        if($formCategory->isSubmitted() && $formCategory->isValid())
        {
            if(!$categorie->getId())
            {
                $word = 'enregistrée';
            }
            else{
                $word = 'modifiée';
            }

            $manager->persist($categorie);
            $manager->flush();

            $this->addFlash('success', "La catégorie à bien été $word avec succés !");
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/admin_create_category.html.twig', [
            'formCategory' => $formCategory->createView(),
            'editMode'     => $categorie->getId()
        ]);
    }

    /**
     * @Route("/admin/categorie/{id}/remove", name="admin_remove_categorie")
     */
    public function adminRemoveCategory(Category $categorie, EntityManagerInterface $manager)
    {
        dump($categorie);
        if($categorie->getArticles()->isEmpty())
        {
            $manager->remove($categorie);
            $manager->flush();

            $this->addFlash('success', "La catégorie a été supprimée avec succés !");
        }
        else{
            $this->addFlash('danger', "Il n'est pas possible de supprimer la catégorie car des articles y sont toujours associés !
            ");
        }

        return $this->redirectToRoute('admin_categories');
    }

    /**
     * @Route("/admin/commentaires", name="admin_commentaires")
     * @Route("/admin/commentaire/{id}/remove", name="admin_remove_commentaire")
     */
    public function adminComment(EntityManagerInterface $manager, CommentRepository $repoCommentaires, Comment $commentaire = null): Response
    {
        $colonnes = $manager->getClassMetadata(Comment::class)->getFieldNames();
        dump($colonnes);

        $commentaires = $repoCommentaires->findAll();
        dump($commentaires);

        // SUPPRESSION
        if($commentaire)
        {
            $manager->remove($commentaire);
            $manager->flush();

            $this->addFlash('success', "Le commentaire a été supprimé avec succés !");

            return $this->redirectToRoute('admin_commentaires');
        }

        return $this->render('admin/admin_commentaires.html.twig', [
            'colonnes' => $colonnes,
            'commentaires' => $commentaires
        ]);
    }

    /**
     * @Route("/admin/commentaire/{id}/edit", name="admin_edit_commentaire")
     */
    public function adminEditComment(Request $request, EntityManagerInterface $manager, Comment $commentaire): Response
    {
        // $commentaire = new Comment;

        $formCommentaire = $this->createForm(CommentType::class, $commentaire);

        $formCommentaire->handlerequest($request);

        if($formCommentaire->isSubmitted() && $formCommentaire->isValid())
        {

            $manager->persist($commentaire);
            $manager->flush();

            $this->addFlash('success', "Le commentaire à bien été modifié avec succés !");
            return $this->redirectToRoute('admin_commentaires');
        }

        return $this->render('admin/admin_edit_commentaire.html.twig', [
            'formCommentaire' => $formCommentaire->createView()
        ]);
    }

    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function adminUsers(EntityManagerInterface $manager, Request $request, UserRepository $repoUser)
    {
        $colonnes = $manager->getClassMetadata(User::class)->getFieldNames();
        dump($colonnes);

        $users = $repoUser->findAll();
        dump($users);


        return $this->render('admin/admin_users.html.twig', [
            'colonnes' => $colonnes,
            'users'    => $users
        ]);
    }
}
