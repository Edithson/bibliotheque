<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'title' => 'Le Cartographe des Brumes',
                'slug' => 'le-cartographe-des-brumes',
                'author' => 'Élise Marchand',
                'category_slug' => 'roman',
                'price' => 6500,
                'stock' => 12,
                'cover_color' => '#5b1a1f',
                'cover_width' => 56,
                'cover_height' => 216,
                'description' => "Un géomètre dessine la carte d'une île qui change de place chaque matin.",
                'excerpt' => "Le géomètre posa son compas sur la table et sut, avant même de tracer le premier trait, que l'île ne figurerait sur aucune carte.|Chaque matin, la brume déplaçait la côte d'une lieue, et chaque soir il corrigeait ses relevés en riant tout seul.",
                'nbr_pages' => 280,
                'publish_year' => 2024,
            ],
            [
                'title' => "Nuits d'Harmattan",
                'slug' => 'nuits-d-harmattan',
                'author' => 'Awa Diallo',
                'category_slug' => 'roman',
                'price' => 5000,
                'stock' => 7,
                'cover_color' => '#3e2a1c',
                'cover_width' => 48,
                'cover_height' => 196,
                'description' => 'Quand le vent sec se lève, une ville entière rouvre ses souvenirs.',
                'excerpt' => "Quand l'harmattan se leva, la ville ferma ses volets et rouvrit ses souvenirs. Chez Mama Awa, on racontait les vieilles histoires.|La poussière rougeoyait sur les toits. Les conteurs ne craignaient pas le vent : il ne faisait qu'apporter le début de la légende.",
                'nbr_pages' => 210,
                'publish_year' => 2023,
            ],
            [
                'title' => 'Mémoire des Grands Fleuves',
                'slug' => 'memoire-des-grands-fleuves',
                'author' => 'Paul-Henri Mbarga',
                'category_slug' => 'histoire',
                'price' => 8000,
                'stock' => 4,
                'cover_color' => '#1c4a4a',
                'cover_width' => 62,
                'cover_height' => 208,
                'description' => "De la source à l'estuaire, l'histoire de ceux qui vivent sur les rives.",
                'excerpt' => "Avant les routes, il y avait les fleuves. Ils portaient les pirogues, les nouvelles, les mariages et parfois les guerres.|Ce livre suit leur cours de la source à l'estuaire et écoute ceux qui vivent sur leurs rives depuis toujours.",
                'nbr_pages' => 340,
                'publish_year' => 2022,
            ],
            [
                'title' => "L'Horloger de Minuit",
                'slug' => 'l-horloger-de-minuit',
                'author' => 'Camille Rousseau',
                'category_slug' => 'roman',
                'price' => 4500,
                'stock' => 0,
                'cover_color' => '#4a2340',
                'cover_width' => 44,
                'cover_height' => 188,
                'description' => "L'horloge de la gare s'arrête chaque nuit à minuit. Pourquoi ?",
                'excerpt' => "L'horloge de la gare s'arrêtait chaque nuit à minuit précis, et personne ne s'en étonnait plus, sauf le nouveau chef de gare.|Il monta au clocher avec une lampe, un tournevis et la ferme intention de comprendre. Il y trouva un vieil homme qui remontait le temps.",
                'nbr_pages' => 195,
                'publish_year' => 2025,
            ],
            [
                'title' => 'Petit Atlas des Étoiles',
                'slug' => 'petit-atlas-des-etoiles',
                'author' => 'Léa Fontaine',
                'category_slug' => 'sciences',
                'price' => 7500,
                'stock' => 15,
                'cover_color' => '#1d2b4a',
                'cover_width' => 52,
                'cover_height' => 200,
                'description' => "Un guide de poche pour reconnaître les constellations à l'œil nu.",
                'excerpt' => "Levez les yeux : ces points de lumière sont des soleils lointains. Le plus proche, après le nôtre, est à plus de quatre années-lumière.|Pour les repérer, il suffit d'un ciel dégagé, d'un peu de patience et de ce petit atlas glissé dans la poche.",
                'nbr_pages' => 160,
                'publish_year' => 2024,
            ],
            [
                'title' => 'Le Jardin des Contes Perdus',
                'slug' => 'le-jardin-des-contes-perdus',
                'author' => 'Fatou Ndiaye',
                'category_slug' => 'jeunesse',
                'price' => 3500,
                'stock' => 22,
                'cover_color' => '#7a3418',
                'cover_width' => 60,
                'cover_height' => 212,
                'description' => 'Dans le jardin de grand-mère, chaque conte oublié devient une fleur.',
                'excerpt' => "Dans le jardin de grand-mère, chaque conte oublié devenait une fleur. Il suffisait de tendre l'oreille pour l'entendre éclore.|Un soir, Kofi trouva un bouton qui chantait. Il décida de l'arroser avec de l'eau de pluie et beaucoup de curiosité.",
                'nbr_pages' => 140,
                'publish_year' => 2023,
            ],
            [
                'title' => 'Carnets de Route',
                'slug' => 'carnets-de-route',
                'author' => 'Marc Olivier',
                'category_slug' => 'voyage',
                'price' => 6000,
                'stock' => 3,
                'cover_color' => '#4b4a1e',
                'cover_width' => 46,
                'cover_height' => 192,
                'description' => 'Trois semaines sur les routes, un carnet, et aucun plan précis.',
                'excerpt' => "Nous avons quitté la ville à l'aube, sans autre plan que celui de suivre la route tant qu'elle nous paraîtrait belle.|Au troisième jour, le carnet était plein de noms de villages, de recettes griffonnées et d'adresses de gens qui nous avaient nourris.",
                'nbr_pages' => 250,
                'publish_year' => 2021,
            ],
            [
                'title' => 'Éloge de la Lenteur',
                'slug' => 'eloge-de-la-lenteur',
                'author' => 'Étienne Vasseur',
                'category_slug' => 'essai',
                'price' => 5500,
                'stock' => 9,
                'cover_color' => '#1a1a1a',
                'cover_width' => 50,
                'cover_height' => 204,
                'description' => 'Un essai pour reprendre le temps de lire, de marcher et de penser.',
                'excerpt' => "Nous avons tout accéléré, jusqu'à nos pensées. Pourtant, la lecture reste ce petit territoire où l'on peut encore marcher lentement.|Prendre son temps n'est pas perdre son temps : c'est en retrouver la texture, page après page, comme on caresse le grain du papier.",
                'nbr_pages' => 180,
                'publish_year' => 2025,
            ],
            [
                'title' => 'Dune',
                'slug' => 'dune',
                'author' => 'Frank Herbert',
                'category_slug' => 'science-fiction',
                'price' => 9000,
                'stock' => 10,
                'cover_color' => '#8b4513',
                'cover_width' => 58,
                'cover_height' => 218,
                'description' => 'Un roman de science-fiction épique qui se déroule sur la planète désertique Arrakis.',
                'excerpt' => "Un début est un moment très délicat. Sachez donc que nous sommes en l'an 10191.|La planète est Arrakis, une terre désertique et désolée.",
                'nbr_pages' => 650,
                'publish_year' => 1965,
            ],
            [
                'title' => 'Le Seigneur des Anneaux',
                'slug' => 'le-seigneur-des-anneaux',
                'author' => 'J.R.R. Tolkien',
                'category_slug' => 'fantastique',
                'price' => 12000,
                'stock' => 6,
                'cover_color' => '#1f3d2e',
                'cover_width' => 65,
                'cover_height' => 220,
                'description' => "Une trilogie de fantasy qui suit les aventures de Frodon Sacquet et de la Communauté de l'Anneau.",
                'excerpt' => 'Trois Anneaux pour les Rois Elfes sous le ciel, Sept pour les Seigneurs Nains dans leurs demeures de pierre.|Un Anneau pour les gouverner tous, Un Anneau pour les trouver.',
                'nbr_pages' => 1150,
                'publish_year' => 1954,
            ],
            [
                'title' => 'Les Misérables',
                'slug' => 'les-miserables',
                'author' => 'Victor Hugo',
                'category_slug' => 'histoire',
                'price' => 10000,
                'stock' => 5,
                'cover_color' => '#2b1b17',
                'cover_width' => 64,
                'cover_height' => 215,
                'description' => 'Un roman historique qui se déroule en France au XIXe siècle, suivant la vie de Jean Valjean.',
                'excerpt' => "Tant qu'il existera, par le fait des lois et des mœurs, une damnation sociale créant artificiellement des enfers en pleine civilisation...|Des livres de la nature de celui-ci ne pourront pas être inutiles.",
                'nbr_pages' => 1400,
                'publish_year' => 1862,
            ],
        ];

        foreach ($books as $bookData) {
            $categorySlug = $bookData['category_slug'];
            unset($bookData['category_slug']);

            $category = Category::where('slug', $categorySlug)->first();
            $bookData['category_id'] = $category ? $category->id : null;
            $bookData['is_published'] = true;

            Book::firstOrCreate(['slug' => $bookData['slug']], $bookData);
        }
    }
}
