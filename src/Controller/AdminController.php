<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
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
}
