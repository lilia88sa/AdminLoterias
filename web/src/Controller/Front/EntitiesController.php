<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 11/24/2020
 * Time: 7:52 PM
 */

namespace App\Controller\Front;


use App\Entity\Entities;

use App\Form\EntitiesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;


class EntitiesController extends AbstractController
{

    public function __construct(

    ) {
    }


    /**
     * @Route("entity-new/{_locale}", name="entity_new",
     *     requirements={
     *         "_locale": "en|es",
     *     },
     *     defaults={"_locale" : "es"})
     */
    public function newUserEntityAction(Request $request): Response
    {
        $entity = new Entities();
        $form = $this->createForm(EntitiesType::class, $entity);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->get("session")->getFlashBag()->add("info","La entidad esta en proceso de revisión, estaremos en contacto mediante el correo electrónico enviado");
            return $this->redirectToRoute('entity_new');
        }elseif ($form->isSubmitted() && !$form->isValid()){
             if(!is_null($form['captcha']->getErrors())){
                 $this->get("session")->getFlashBag()->add("danger","Los valores del Captcha no coinciden");
             }

        }
        return $this->render('front/new_entity_user/new.html.twig', [
           'form' => $form->createView(),
           'entity' => $entity,
        ]);
    }

    /**
     * @Route("entidades/{_locale}", name="listado_entidades",
     *     requirements={
     *         "_locale": "en|es",
     *     },
     *     defaults={"_locale" : "es"})
     */
    public function listarentidades (PaginatorInterface $paginator,Request $request): Response{
        $em = $this->getDoctrine()->getManager();
        $entidades = $em->createQuery("SELECT e FROM  App\Entity\Entities e WHERE e.publish = 1
     order by e.id desc ")->getResult();

        $dir = $em->createQuery("SELECT c FROM  App\Entity\Section c JOIN c.orderClasification oc
     WHERE oc.name like '%directorio%' and c.publish=1")->setMaxResults(1)->getResult();

        $directorio = count($dir) > 0 ? reset($dir) : false;


        $pagination = $paginator->paginate(
            $entidades, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('front/new_entity_user/listado.html.twig', [
            'paginator' => $pagination,
            'directorio' => $directorio

        ]);

    }

    /**
     * @Route("entity_detalles/{_locale}/{id}/{slug}", name="entity_detalles",
     *     requirements={
     *         "_locale": "en|es",
     *     },
     *     defaults={"_locale" : "es"})
     */
    public function entitydetallesAction( Request $request, $id): Response
    {

        $entity = $this->getDoctrine()->getRepository(Entities::class)->find($id);

        $em = $this->getDoctrine()->getManager();
        $dir = $em->createQuery("SELECT c FROM  App\Entity\Section c JOIN c.orderClasification oc
        WHERE oc.name like '%directorio%' and c.publish=1")->setMaxResults(1)->getResult();

        $directorio = count($dir) > 0 ? reset($dir) : false;

        return $this->render('front/new_entity_user/detalles.html.twig', [
            'entity' => $entity,
            'directorio' => $directorio
        ]);
    }

    public function entidadinicio( Request $request): Response
    {

        $em = $this->getDoctrine()->getManager();
        $entidades = $em->createQuery("SELECT e FROM  App\Entity\Entities e WHERE e.publish = 1
     order by e.id desc ")->setMaxResults(8)->getResult();


        $dir = $em->createQuery("SELECT c FROM  App\Entity\Section c JOIN c.orderClasification oc
        WHERE oc.name like '%directorio%' and c.publish=1")->setMaxResults(8)->getResult();

        $directorio = count($dir) > 0 ? reset($dir) : false;

        return $this->render('front/new_entity_user/detalles.html.twig', [
            'entidades' => $entidades,
            'directorio' => $directorio
        ]);
    }

}

