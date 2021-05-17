<?php

namespace App\DataFixtures;

use App\Entity\Article;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // On boucle de 10 fois pour créer 10 articles
        for($i = 1; $i <= 10; $i++)
        {
            // Importer classe ctrrrl + alt + i
            $article = new Article();

            $article->setTitre("Titre de l'article n°$i")
                    ->setContenu("<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. In, dicta eveniet? Expedita, ratione tempore labore rerum esse, quis voluptas deserunt quibusdam nulla exercitationem provident necessitatibus numquam inventore! Aspernatur eveniet, ut laboriosam omnis recusandae placeat illum nemo velit aliquid laudantium explicabo, consequatur dignissimos. Explicabo facilis dicta eius exercitationem non. Rem neque est similique dolor fugiat corrupti accusamus porro, rerum ipsum totam molestias nulla quae nemo provident voluptatibus doloremque a minus blanditiis! Earum sequi commodi sapiente voluptates dicta accusantium similique dolore repudiandae. Repudiandae et vel nemo rem voluptatum cum qui perspiciatis est rerum, tempora minus soluta odio, similique itaque tenetur, modi porro!</p><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. In, dicta eveniet? Expedita, ratione tempore labore rerum esse, quis voluptas deserunt quibusdam nulla exercitationem provident necessitatibus numquam inventore! Aspernatur eveniet, ut laboriosam omnis recusandae placeat illum nemo velit aliquid laudantium explicabo, consequatur dignissimos. Explicabo facilis dicta eius exercitationem non. Rem neque est similique dolor fugiat corrupti accusamus porro, rerum ipsum totam molestias nulla quae nemo provident voluptatibus doloremque a minus blanditiis! Earum sequi commodi sapiente voluptates dicta accusantium similique dolore repudiandae. Repudiandae et vel nemo rem voluptatum cum qui perspiciatis est rerum, tempora minus soluta odio, similique itaque tenetur, modi porro!</p>")
                    ->setImage("https://picsum.photos/300/300?grayscale")
                    ->setDate(new \DateTime());

                    $manager->persist($article); // prepare et on garde en mémoire les requetes SQL d'insertions
        }
        $manager->flush(); // permet d'executerrr les requetes SQL en BDD
    }
}
