<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 2/23/2021
 * Time: 3:04 PM
 */

namespace App\Controller\Front;

use App\Entity\Entities;
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends AbstractController
{

    public function __construct(
    ) {

    }

    /**
     * @Route("/{_locale}/search", name="search_homepage",
     *     requirements={
     *         "_locale": "en|es",
     *     },
     *     defaults={"_locale" : "es"})
     */
    public function searchHomepageAction(Request $request)
    {
        $search = $request->query->get('key_search');
        $search = trim($search);
        $search = stripslashes($search);
        $search = htmlspecialchars($search);
        $pagination = $this->getDoctrine()->getRepository(Post::class)->searchHomepagePost($search, $request);

        // parameters to template
        return $this->render('front/search/search_homepage.html.twig', [
            'pagination' => $pagination
        ]);

    }

    /**
     * @Route("/{_locale}/entity_search", name="search_entity_homepage",
     *     requirements={
     *         "_locale": "en|es",
     *     },
     *     defaults={"_locale" : "es"})
     */
    public function searchEntityAction(Request $request)
    {
        $search = $request->query->get('key_entity_search');
        $search = trim($search);
        $search = stripslashes($search);
        $search = htmlspecialchars($search);
        $pagination = $this->getDoctrine()->getRepository(Entities::class)->searchHomepageEntity($search, $request);

        // parameters to template
        return $this->render('front/search/search_entity_homepage.html.twig', [
            'pagination' => $pagination
        ]);

    }

}