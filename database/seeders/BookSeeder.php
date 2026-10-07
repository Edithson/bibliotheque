<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
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
                'cover_color' => '#5b1a1f',
                'cover_width' => 56,
                'cover_height' => 216,
                'file_path' => 'books/le-cartographe-des-brumes.pdf',
                'description' => "Un géomètre dessine la carte d'une île qui change de place chaque matin.",
                'excerpt' => "Le géomètre posa son compas sur la table et sut, avant même de tracer le premier trait, que l'île ne figurerait sur aucune carte.|Chaque matin, la brume déplaçait la côte d'une lieue, et chaque soir il corrigeait ses relevés en riant tout seul.",
                'nbr_pages' => 280,
                'publish_year' => 2024,
                'is_published' => true,
            ],
            [
                'title' => "Nuits d'Harmattan",
                'slug' => 'nuits-d-harmattan',
                'author' => 'Awa Diallo',
                'category_slug' => 'roman',
                'price' => 0, // Gratuit
                'cover_color' => '#3e2a1c',
                'cover_width' => 48,
                'cover_height' => 196,
                'file_path' => 'books/nuits-d-harmattan.pdf',
                'description' => 'Quand le vent sec se lève, une ville entière rouvre ses souvenirs.',
                'excerpt' => "Quand l'harmattan se leva, la ville ferma ses volets et rouvrit ses souvenirs. Chez Mama Awa, on racontait les vieilles histoires.|La poussière rougeoyait sur les toits. Les conteurs ne craignaient pas le vent : il ne faisait qu'apporter le début de la légende.",
                'nbr_pages' => 210,
                'publish_year' => 2023,
                'is_published' => true,
            ],
            [
                'title' => 'Mémoire des Grands Fleuves',
                'slug' => 'memoire-des-grands-fleuves',
                'author' => 'Paul-Henri Mbarga',
                'category_slug' => 'histoire',
                'price' => 8000,
                'cover_color' => '#1c4a4a',
                'cover_width' => 62,
                'cover_height' => 208,
                'file_path' => 'books/memoire-des-grands-fleuves.pdf',
                'description' => "De la source à l'estuaire, l'histoire de ceux qui vivent sur les rives.",
                'excerpt' => "Avant les routes, il y avait les fleuves. Ils portaient les pirogues, les nouvelles, les mariages et parfois les guerres.|Ce livre suit leur cours de la source à l'estuaire et écoute ceux qui vivent sur leurs rives depuis toujours.",
                'nbr_pages' => 340,
                'publish_year' => 2022,
                'is_published' => true,
            ],
            [
                'title' => "L'Horloger de Minuit",
                'slug' => 'l-horloger-de-minuit',
                'author' => 'Camille Rousseau',
                'category_slug' => 'roman',
                'price' => 0, // Gratuit
                'cover_color' => '#4a2340',
                'cover_width' => 44,
                'cover_height' => 188,
                'file_path' => 'books/l-horloger-de-minuit.pdf',
                'description' => "L'horloge de la gare s'arrête chaque nuit à minuit. Pourquoi ?",
                'excerpt' => "L'horloge de la gare s'arrêtait chaque nuit à minuit précis, et personne ne s'en étonnait plus, sauf le nouveau chef de gare.|Il monta au clocher avec une lampe, un tournevis et la ferme intention de comprendre. Il y trouva un vieil homme qui remontait le temps.",
                'nbr_pages' => 195,
                'publish_year' => 2025,
                'is_published' => true,
            ],
            [
                'title' => 'Petit Atlas des Étoiles',
                'slug' => 'petit-atlas-des-etoiles',
                'author' => 'Léa Fontaine',
                'category_slug' => 'sciences',
                'price' => 7500,
                'cover_color' => '#1d2b4a',
                'cover_width' => 52,
                'cover_height' => 200,
                'file_path' => 'books/petit-atlas-des-etoiles.pdf',
                'description' => "Un guide de poche pour reconnaître les constellations à l'œil nu.",
                'excerpt' => "Levez les yeux : ces points de lumière sont des soleils lointains. Le plus proche, après le nôtre, est à plus de quatre années-lumière.|Pour les repérer, il suffit d'un ciel dégagé, d'un peu de patience et de ce petit atlas glissé dans la poche.",
                'nbr_pages' => 160,
                'publish_year' => 2024,
                'is_published' => true,
            ],
            [
                'title' => 'Le Jardin des Contes Perdus',
                'slug' => 'le-jardin-des-contes-perdus',
                'author' => 'Fatou Ndiaye',
                'category_slug' => 'jeunesse',
                'price' => 3500,
                'cover_color' => '#7a3418',
                'cover_width' => 60,
                'cover_height' => 212,
                'file_path' => 'books/le-jardin-des-contes-perdus.pdf',
                'description' => 'Dans le jardin de grand-mère, chaque conte oublié devient une fleur.',
                'excerpt' => "Dans le jardin de grand-mère, chaque conte oublié devenait une fleur. Il suffisait de tendre l'oreille pour l'entendre éclore.|Un soir, Kofi trouva un bouton qui chantait. Il décida de l'arroser avec de l'eau de pluie et beaucoup de curiosité.",
                'nbr_pages' => 140,
                'publish_year' => 2023,
                'is_published' => true,
            ],
            [
                'title' => 'Carnets de Route',
                'slug' => 'carnets-de-route',
                'author' => 'Marc Olivier',
                'category_slug' => 'voyage',
                'price' => 0, // Gratuit
                'cover_color' => '#4b4a1e',
                'cover_width' => 46,
                'cover_height' => 192,
                'file_path' => 'books/carnets-de-route.pdf',
                'description' => 'Trois semaines sur les routes, un carnet, et aucun plan précis.',
                'excerpt' => "Nous avons quitté la ville à l'aube, sans autre plan que celui de suivre la route tant qu'elle nous paraîtrait belle.|Au troisième jour, le carnet était plein de noms de villages, de recettes griffonnées et d'adresses de gens qui nous avaient nourris.",
                'nbr_pages' => 250,
                'publish_year' => 2021,
                'is_published' => true,
            ],
            [
                'title' => 'Éloge de la Lenteur',
                'slug' => 'eloge-de-la-lenteur',
                'author' => 'Étienne Vasseur',
                'category_slug' => 'essai',
                'price' => 5500,
                'cover_color' => '#1a1a1a',
                'cover_width' => 50,
                'cover_height' => 204,
                'file_path' => 'books/eloge-de-la-lenteur.pdf',
                'description' => 'Un essai pour reprendre le temps de lire, de marcher et de penser.',
                'excerpt' => "Nous avons tout accéléré, jusqu'à nos pensées. Pourtant, la lecture reste ce petit territoire où l'on peut encore marcher lentement.|Prendre son temps n'est pas perdre son temps : c'est en retrouver la texture, page après page, comme on caresse le grain du papier.",
                'nbr_pages' => 180,
                'publish_year' => 2025,
                'is_published' => true,
            ],
        ];

        $defaultUser = User::first();

        foreach ($books as $bookData) {
            $categorySlug = $bookData['category_slug'];
            unset($bookData['category_slug']);

            $category = Category::where('slug', $categorySlug)->first();
            $bookData['category_id'] = $category ? $category->id : null;
            if ($defaultUser) {
                $bookData['user_id'] = $defaultUser->id;
                $bookData['updated_by_user_id'] = $defaultUser->id;
            }

            Book::firstOrCreate(['slug' => $bookData['slug']], $bookData);
        }
    }
}
