<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 2/23/2021
 * Time: 3:04 PM
 */

namespace App\Controller\Front;

use App\Entity\KeyWords;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class GlossaryController extends AbstractController
{

    public function __construct(
    ) {

    }

    /**
     * @Route("/{_locale}/glosario", name="glossary",
     *     requirements={
     *         "_locale": "en|es",
     *     },
     *     defaults={"_locale" : "es"})
     */
    public function glossaryAction(Request $request, PaginatorInterface $paginator)
    {
        $glossary = $this->getDoctrine()->getRepository(KeyWords::class)->findBy(['isGlossary' => true], ['title' => 'ASC']);

        $pagination = $paginator->paginate(
            $glossary, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        // parameters to template
        return $this->render('front/glossary/glossary_list.html.twig', [
            'pagination' => $pagination
        ]);

    }

    /**
     * @Route("palabra_detalles/{_locale}/{id}/", name="palabra_detalles",
     *     requirements={
     *         "_locale": "en|es",
     *     },
     *     defaults={"_locale" : "es"})
     */
    public function palabraclaveAction(Request $request, $id): Response{
        $palabra = $this->getDoctrine()->getRepository(KeyWords::class)->find($id);
        return $this->render('front/glossary/palabra_clave.html.twig', [
            'palabra' => $palabra
        ]);

    }

}