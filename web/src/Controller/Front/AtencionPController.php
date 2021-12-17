<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 11/24/2020
 * Time: 7:52 PM
 */

namespace App\Controller\Front;

use App\Entity\AtencionPoblacion;
use App\Form\AtencionPoblacionType;
use App\Providers\AtencionPoblacionProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class AtencionPController extends AbstractController
{
    private $atencionPoblacionProvider;

    public function __construct(
     AtencionPoblacionProvider $atencionPoblacionProvider
    ) {
        $this->atencionPoblacionProvider = $atencionPoblacionProvider;
    }

     /**
     * @Route("quejas/{_locale}", name="quejas",
     *     requirements={
     *         "_locale": "en|es",
     *     },
     *     defaults={"_locale" : "es"})
     */
    public function quejasAction(Request $request): Response
    {
        $entity = new AtencionPoblacion();
        $entity1 = new AtencionPoblacion();
        $form = $this->createForm(AtencionPoblacionType::class, $entity, [
            'action_type' => 'queja'
        ]);
        $form1 = $this->createForm(AtencionPoblacionType::class, $entity1);
        $form->handleRequest($request);
        $form1->handleRequest($request);

        $response = $this->atencionPoblacionProvider->getPlanteamientosPublicos();
        if($response['status'] == 'OK'){
            $response = $response['respuesta'];
        }else{
            $response = null;
        }
        if ($form->isSubmitted() && $form->isValid()) {
            //$response = $this->atencionPoblacionProvider->getProvincia();
//            $response = $this->atencionPoblacionProvider->getProvinciaActivas();
            //$response = $this->atencionPoblacionProvider->getProvinciaContratadas();
//          $response = $this->atencionPoblacionProvider->getMunicipios($entity);
//            $response = $this->atencionPoblacionProvider->getConsejos($entity);
//            $response = $this->atencionPoblacionProvider->getCircunscripcion($entity);
//            $response = $this->atencionPoblacionProvider->getPlanDespacho($entity);
//            $response = $this->atencionPoblacionProvider->getProgramaReunion($entity);
//            $response = $this->atencionPoblacionProvider->getDatosDelegado($entity);
//            $response = $this->atencionPoblacionProvider->getClasificacion();
//            $response = $this->atencionPoblacionProvider->getNivel();
            //$response = $this->atencionPoblacionProvider->getPlanteamientosPublicos();

            $response = $this->atencionPoblacionProvider->postTramitar($entity);
            //dump($response);die;
            if($response['status'] == 'OK'){
                $this->get("session")->getFlashBag()->add("info",'Su petición está siendo tramitada puede consultar el estado de la misma con el código: '.$response['respuesta'].' en la pestaña: Estado del Trámite de esta ventana');
            }else{
                $this->get("session")->getFlashBag()->add("danger",'Su petición '.$response['respuesta']);
            }

            return $this->redirectToRoute('quejas');
        }elseif ($form->isSubmitted() && !$form->isValid()){
            if(!is_null($form['captcha']->getErrors())){
                $this->get("session")->getFlashBag()->add("danger","Los valores del Captcha no coinciden");
            }
        }
            return $this->render('front/atencionp/quejas.html.twig', [
            'form' => $form->createView(),
            'form1' => $form1->createView(),
            'publicos' => $response,
        ]);
    }

    /**
     * @Route("estado_quejas/{_locale}", name="estado_quejas",
     *     requirements={
     *         "_locale": "en|es",
     *     },
     *     defaults={"_locale" : "es"})
     */
    public function estadoAction(Request $request): Response
    {
        $entity = new AtencionPoblacion();
        $form = $this->createForm(AtencionPoblacionType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $response = $this->atencionPoblacionProvider->getVerificarTramite($entity);
            if($response['status'] == 'OK'){
                $response = $response['respuesta'][0];
                !is_null($response->respuestaciudadano) ? $solucion = $response->respuestaciudadano : $solucion = 'aun en proceso';
                $tramite = "Su Tramite de {$response->clasificacion} con código {$response->codigoplanteamiento} a nombre de {$response->nombre} recepcionado el {$response->fecharecepcion} desde {$response->descmun} se encuentra en {$response->descentramiteestado} sobre el tema {$response->resumen} con respuesta {$solucion}";
                $this->get("session")->getFlashBag()->add("info",$tramite);
            }elseif ($response['status'] == 'KO'){
                $response = $response['respuesta'];
                $this->get("session")->getFlashBag()->add("danger",$response);
            }else{
                $this->get("session")->getFlashBag()->add("danger",'Su petición '.$response['respuesta']);
            }
            return $this->redirectToRoute('quejas');
        }
        return $this->redirectToRoute('quejas');
    }

}

