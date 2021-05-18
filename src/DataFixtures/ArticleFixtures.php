<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        for($i = 1; $i <= 3; $i++)
        {
            $category = new Category;

            $category->setTitre($faker->word)
                    ->setDescription($faker->paragraph());

            $manager->persist($category);

            // création de 4 à 10 Articles
            for($j = 1; $j <= mt_rand(4,9); $j++)
            {
                $article = new Article;

                $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';

                $article->setTitre($faker->sentence())
                        ->setContenu($content)
                        ->setImage("https://picsum.photos/id/23$j/300/300")
                        ->setDate($faker->dateTimeBetween('-6 months'))
                        ->setCategory($category);

                $manager->persist($article);

                // Création de 4 à 10 commentaires
                for($k = 1; $k <= mt_rand(4,10); $k++)
                {
                    $comment = new Comment;

                    $content = '<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';

                    $now = new DateTime;
                    $interval = $now->diff($article->getDate());
                    $days = $interval->days;
                    $minimum = "-$days days";

                    $comment->setAuteur($faker->name())
                            ->setCommentaire($content)
                            ->setDate($faker->dateTimeBetween($minimum))
                            ->setArticle($article);

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
