<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 3/9/2021
 * Time: 3:01 PM
 */

namespace App\Providers;


use App\Entity\AtencionPoblacion;
use App\Repository\AtencionPoblacionRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AtencionPoblacionProvider
{
    private $client;
    private $container;
    private $route;
    private $entityManager;
    private $atencionPoblacionRepository;

    public function __construct(HttpClientInterface $client,
                                ContainerInterface $container,
                                EntityManagerInterface $entityManager,
                                AtencionPoblacionRepository $atencionPoblacionRepository)
    {
        $this->client = $client;
        $this->container = $container;
        $this->route = $this->container->getParameter('gestion_delegados');
        $this->entityManager = $entityManager;
        $this->atencionPoblacionRepository = $atencionPoblacionRepository;
    }

    private function getUrl($method){

        $url = "servicio/{$method}";

        return $url;
    }

    public function login(){
        $response = $this->client->request(
            'POST',
            $this->route.$this->getUrl('login'),[
                'headers' => [
                    'Authorization' => 'Tercero '
                ],
                'body' => ['entidad' => 'infocap', 'passentidad' => 'Infocap*123'],
                //'verify_peer' => false,
               'verify_host' => false,
            ]
        );
        if($response->getStatusCode() == Response::HTTP_OK){
            $token =  json_decode($response->getContent());
            $this->existOrCreateEntity($token->access_token);
            return array(
                'status' => 'OK',
                'token' => $token->access_token
            );
        }
        return array(
            'status' => $response->getStatusCode(),
            'token' => 'Error procesando sus datos intente en otro momento'
        );
    }

    private function getToken(){
        $entity = $this->atencionPoblacionRepository->findAll();
        if(empty($entity)){
            $login_token = $this->login();
            return $login_token['status'] == 'OK' ? $login_token['token'] : $login_token;
        }else{
            $atencionPoblacion = $entity[0];
            if(Carbon::instance($atencionPoblacion->getUpdatedAt())
                    ->diffInHours(Carbon::instance(new \DateTime())) >= 1){
                $login_token = $this->login();
                return $login_token['status'] == 'OK' ? $login_token['token'] : $login_token;
            }else{
                return $atencionPoblacion->getToken() ;
            }
        }
    }

    protected function existOrCreateEntity($access_token){
        $entity = $this->atencionPoblacionRepository->findAll();
        if(empty($entity)){
            $atencionPoblacion = new AtencionPoblacion();
            $atencionPoblacion->setToken($access_token);
            $this->entityManager->persist($atencionPoblacion);
            $this->entityManager->flush();
            return $atencionPoblacion;
        }
        $atencionPoblacion = $entity[0];
        $atencionPoblacion->setToken($access_token);
        $this->entityManager->persist($atencionPoblacion);
        $this->entityManager->flush();
        return $atencionPoblacion;
    }

    public function getProvincia()
    {
        $token = $this->getToken();
        if(is_array($token)){
            return $token['token'];
        }
        $response = $this->client->request(
            'GET',
            $this->route.$this->getUrl('provincia'),[
                'headers' => [
                    'Authorization' => 'Tercero '.$token
                ],
                //verify_peer' => false,
                'verify_host' => false,
            ]
        );
        if($response->getStatusCode() == Response::HTTP_OK){
            $provincias =  json_decode($response->getContent());
            return array(
                'status' => 'OK',
                'respuesta' => $provincias
            );
        }elseif ($response->getStatusCode() == Response::HTTP_UNAUTHORIZED){
            $this->login();
            return $this->getProvincia();
        }
        return array(
            'status' => $response->getStatusCode(),
            'respuesta' => 'Error'
        );
    }

    public function getProvinciaActivas()
    {
        $token = $this->getToken();
        if(is_array($token)){
            return $token['token'];
        }
        $response = $this->client->request(
            'GET',
            $this->route.$this->getUrl('provinciasactivas'),[
                'headers' => [
                    'Authorization' => 'Tercero '.$token
                ],
                //verify_peer' => false,
                'verify_host' => false,
            ]
        );
        if($response->getStatusCode() == Response::HTTP_OK){
            $provincias =  json_decode($response->getContent());
            return array(
                'status' => 'OK',
                'respuesta' => $provincias
            );
        }elseif ($response->getStatusCode() == Response::HTTP_UNAUTHORIZED){
            $this->login();
            return $this->getProvincia();
        }
        return array(
            'status' => $response->getStatusCode(),
            'respuesta' => 'Error'
        );
    }

    public function getProvinciaContratadas()
    {
        $token = $this->getToken();
        if(is_array($token)){
            return $token['token'];
        }
        $response = $this->client->request(
            'GET',
            $this->route.$this->getUrl('provinciascontratadas'),[
                'headers' => [
                    'Authorization' => 'Tercero '.$token
                ],
                //verify_peer' => false,
                'verify_host' => false,
            ]
        );
        if($response->getStatusCode() == Response::HTTP_OK){
            $provincias =  json_decode($response->getContent());
            return array(
                'status' => 'OK',
                'respuesta' => $provincias
            );
        }elseif ($response->getStatusCode() == Response::HTTP_UNAUTHORIZED){
            $this->login();
            return $this->getProvincia();
        }
        return array(
            'status' => $response->getStatusCode(),
            'respuesta' => 'Error'
        );
    }

    public function getMunicipios(AtencionPoblacion $atencionPoblacion)
    {
        $token = $this->getToken();
        if(is_array($token)){
            return $token['token'];
        }
        $response = $this->client->request(
            'POST',
            $this->route.$this->getUrl('municipio'),[
                'headers' => [
                    'Authorization' => 'Tercero '.$token
                ],
                'body' => ['idprovincia' => 15],
                //verify_peer' => false,
                'verify_host' => false,
            ]
        );
        if($response->getStatusCode() == Response::HTTP_OK){
            $provincias =  json_decode($response->getContent());
            return array(
                'status' => 'OK',
                'respuesta' => $provincias
            );
        }elseif ($response->getStatusCode() == Response::HTTP_UNAUTHORIZED){
            $this->login();
            return $this->getProvincia();
        }
        return array(
            'status' => $response->getStatusCode(),
            'respuesta' => 'Error'
        );
    }

    public function getClasificacion()
    {
        $token = $this->getToken();
        if(is_array($token)){
            return $token['token'];
        }
        $response = $this->client->request(
            'POST',
            $this->route.$this->getUrl('clasificacion'),[
                'headers' => [
                    'Authorization' => 'Tercero '.$token
                ],
                'body' => ['idprovincia' => 15],
                //verify_peer' => false,
                'verify_host' => false,
            ]
        );
        if($response->getStatusCode() == Response::HTTP_OK){
            $provincias =  json_decode($response->getContent());
            return array(
                'status' => 'OK',
                'respuesta' => $provincias
            );
        }elseif ($response->getStatusCode() == Response::HTTP_UNAUTHORIZED){
            $this->login();
            return $this->getProvincia();
        }
        return array(
            'status' => $response->getStatusCode(),
            'respuesta' => 'Error'
        );
    }

    public function getNivel()
    {
        $token = $this->getToken();
        if(is_array($token)){
            return $token['token'];
        }
        $response = $this->client->request(
            'POST',
            $this->route.$this->getUrl('nivel'),[
                'headers' => [
                    'Authorization' => 'Tercero '.$token
                ],
                'body' => ['idprovincia' => 15],
                //verify_peer' => false,
                'verify_host' => false,
            ]
        );
        if($response->getStatusCode() == Response::HTTP_OK){
            $provincias =  json_decode($response->getContent());
            return array(
                'status' => 'OK',
                'respuesta' => $provincias
            );
        }elseif ($response->getStatusCode() == Response::HTTP_UNAUTHORIZED){
            $this->login();
            return $this->getProvincia();
        }
        return array(
            'status' => $response->getStatusCode(),
            'respuesta' => 'Error'
        );
    }

    public function getPlanteamientosPublicos()
    {
        try{
            $token = $this->getToken();
            if(is_array($token)){
                return $token['token'];
            }
            $response = $this->client->request(
                'POST',
                $this->route.$this->getUrl('planteamientospublicos'),[
                    'headers' => [
                        'Authorization' => 'Tercero '.$token
                    ],
                    'body' => ['idprovincia' => 15],
                    //verify_peer' => false,
                    'verify_host' => false,
                ]
            );
            if($response->getStatusCode() == Response::HTTP_OK){
                $provincias =  json_decode($response->getContent());
                return array(
                    'status' => 'OK',
                    'respuesta' => $provincias
                );
            }elseif ($response->getStatusCode() == Response::HTTP_UNAUTHORIZED){
                $this->login();
                return $this->getProvincia();
            }
            return array(
                'status' => $response->getStatusCode(),
                'respuesta' => 'Error'
            );
        }catch (\Exception $es){
            return array(
                'status' => $es->getMessage(),
                'respuesta' => 'Error al cargar datos'
            );
        }

    }

    public function getCantPlanteamientosPublicos()
    {
        $token = $this->getToken();
        if(is_array($token)){
            return $token['token'];
        }
        $response = $this->client->request(
            'POST',
            $this->route.$this->getUrl('cantidadplantpublicos'),[
                'headers' => [
                    'Authorization' => 'Tercero '.$token
                ],
                'body' => ['idprovincia' => 15],
                //verify_peer' => false,
                'verify_host' => false,
            ]
        );
        if($response->getStatusCode() == Response::HTTP_OK){
            $provincias =  json_decode($response->getContent());
            return array(
                'status' => 'OK',
                'respuesta' => $provincias
            );
        }elseif ($response->getStatusCode() == Response::HTTP_UNAUTHORIZED){
            $this->login();
            return $this->getProvincia();
        }
        return array(
            'status' => $response->getStatusCode(),
            'respuesta' => 'Error'
        );
    }

    public function getConsejos(AtencionPoblacion $atencionPoblacion)
    {
        $token = $this->getToken();
        if(is_array($token)){
            return $token['token'];
        }
        $response = $this->client->request(
            'POST',
            $this->route.$this->getUrl('consejo'),[
                'headers' => [
                    'Authorization' => 'Tercero '.$token
                ],
                'body' => ['idprovincia' => 15,
                    'idmunicipio' => $atencionPoblacion->getMunicipio()],  //PLAYA
                //verify_peer' => false,
                'verify_host' => false,
            ]
        );
        if($response->getStatusCode() == Response::HTTP_OK){
            $provincias =  json_decode($response->getContent());
            return array(
                'status' => 'OK',
                'respuesta' => $provincias
            );
        }elseif ($response->getStatusCode() == Response::HTTP_UNAUTHORIZED){
            $this->login();
            return $this->getProvincia();
        }
        return array(
            'status' => $response->getStatusCode(),
            'respuesta' => 'Error'
        );
    }

    public function getCircunscripcion(AtencionPoblacion $atencionPoblacion)
    {
        $token = $this->getToken();
        if(is_array($token)){
            return $token['token'];
        }
        $response = $this->client->request(
            'POST',
            $this->route.$this->getUrl('circunscripcion'),[
                'headers' => [
                    'Authorization' => 'Tercero '.$token
                ],
                'body' => ['idprovincia' => 15,
                    'idmunicipio' => $atencionPoblacion->getMunicipio(),  //PLAYA
                    'idconsejo' => 296
                ],
                //verify_peer' => false,
                'verify_host' => false,
            ]
        );
        if($response->getStatusCode() == Response::HTTP_OK){
            $provincias =  json_decode($response->getContent());
            return array(
                'status' => 'OK',
                'respuesta' => $provincias
            );
        }elseif ($response->getStatusCode() == Response::HTTP_UNAUTHORIZED){
            $this->login();
            return $this->getProvincia();
        }
        return array(
            'status' => $response->getStatusCode(),
            'respuesta' => 'Error'
        );
    }

    public function getPlanDespacho(AtencionPoblacion $atencionPoblacion)
    {
        $token = $this->getToken();
        if(is_array($token)){
            return $token['token'];
        }
        $response = $this->client->request(
            'POST',
            $this->route.$this->getUrl('plandespacho'),[
                'headers' => [
                    'Authorization' => 'Tercero '.$token
                ],
                'body' => ['idprovincia' => 15,
                    'idmunicipio' => $atencionPoblacion->getMunicipio(), //PLAYA
                    'idconsejo' => 296,
                    'idcircunscripcion' => 4994
                ],
                //verify_peer' => false,
                'verify_host' => false,
            ]
        );
        if($response->getStatusCode() == Response::HTTP_OK){
            $provincias =  json_decode($response->getContent());
            return array(
                'status' => 'OK',
                'respuesta' => $provincias
            );
        }elseif ($response->getStatusCode() == Response::HTTP_UNAUTHORIZED){
            $this->login();
            return $this->getProvincia();
        }
        return array(
            'status' => $response->getStatusCode(),
            'respuesta' => 'Error'
        );
    }

    public function getProgramaReunion(AtencionPoblacion $atencionPoblacion)
    {
        $token = $this->getToken();
        if(is_array($token)){
            return $token['token'];
        }
        $response = $this->client->request(
            'POST',
            $this->route.$this->getUrl('programareunion'),[
                'headers' => [
                    'Authorization' => 'Tercero '.$token
                ],
                'body' => ['idprovincia' => 15,
                    'idmunicipio' => $atencionPoblacion->getMunicipio(), //PLAYA
                    'idconsejo' => 296,
                    'idcircunscripcion' => 4994
                ],
                //verify_peer' => false,
                'verify_host' => false,
            ]
        );
        if($response->getStatusCode() == Response::HTTP_OK){
            $provincias =  json_decode($response->getContent());
            return array(
                'status' => 'OK',
                'respuesta' => $provincias
            );
        }elseif ($response->getStatusCode() == Response::HTTP_UNAUTHORIZED){
            $this->login();
            return $this->getProvincia();
        }
        return array(
            'status' => $response->getStatusCode(),
            'respuesta' => 'Error'
        );
    }

    public function getDatosDelegado(AtencionPoblacion $atencionPoblacion)
    {
        $token = $this->getToken();
        if(is_array($token)){
            return $token['token'];
        }
        $response = $this->client->request(
            'POST',
            $this->route.$this->getUrl('datosdelegado'),[
                'headers' => [
                    'Authorization' => 'Tercero '.$token
                ],
                'body' => ['idprovincia' => 15,
                    'idmunicipio' => $atencionPoblacion->getMunicipio(), //PLAYA
                    'idconsejo' => 296,
                    'idcircunscripcion' => 4994
                ],
                //verify_peer' => false,
                'verify_host' => false,
            ]
        );
        if($response->getStatusCode() == Response::HTTP_OK){
            $provincias =  json_decode($response->getContent());
            return array(
                'status' => 'OK',
                'respuesta' => $provincias
            );
        }elseif ($response->getStatusCode() == Response::HTTP_UNAUTHORIZED){
            $this->login();
            return $this->getProvincia();
        }
        return array(
            'status' => $response->getStatusCode(),
            'respuesta' => 'Error'
        );
    }

    public function getVerificarTramite(AtencionPoblacion $atencionPoblacion)
    {
        try{
            $token = $this->getToken();
            if(is_array($token)){
                return $token['token'];
            }
            $response = $this->client->request(
                'POST',
                $this->route.$this->getUrl('verificartramitepoblacion'),[
                    'headers' => [
                        'Authorization' => 'Tercero '.$token
                    ],
                    'body' => ['idprovincia' => 15,
                        'codigoplanteamiento' => $atencionPoblacion->getCodigo()
                    ],
                    //verify_peer' => false,
                    'verify_host' => false,
                ]
            );
            if($response->getStatusCode() == Response::HTTP_OK){
                $provincias =  json_decode($response->getContent());
                return array(
                    'status' => 'OK',
                    'respuesta' => $provincias
                );
            }elseif ($response->getStatusCode() == Response::HTTP_UNAUTHORIZED){
                $this->login();
                return $this->getProvincia();
            }elseif($response->getStatusCode() == Response::HTTP_NOT_FOUND){
                return array(
                    'status' => 'KO',
                    'respuesta' => 'No se encuentra su código'
                );
            }
            return array(
                'status' => $response->getStatusCode(),
                'respuesta' =>  json_decode($response->getContent())
            );
        }catch (\Exception $es){
            return array(
                'status' => $es->getMessage(),
                'respuesta' => 'Error al cargar datos'
            );
        }

    }

    public function postTramitarDesp(AtencionPoblacion $atencionPoblacion)
    {
        $token = $this->getToken();
        if(is_array($token)){
            return $token['token'];
        }
        if(empty($atencionPoblacion->getNotificar())){
            $notificar = 'N';
        }elseif(count($atencionPoblacion->getNotificar()) == 2){
            $notificar = '3';
        }else{
            $notificar = $atencionPoblacion->getNotificar();
            $notificar[0] == 1 ? $notificar = '2' : $notificar = '1';
        }
        $response = $this->client->request(
            'POST',
            $this->route.$this->getUrl('despachoonline'),[
                'headers' => [
                    'Authorization' => 'Tercero '.$token
                ],
                'body' => ['idprovincia' => 15,
                    'idmunicipio' => $atencionPoblacion->getMunicipio(),
                    'nombre' => $atencionPoblacion->getNombre(),
                    'direccion' => $atencionPoblacion->getDireccion(),
                    'idclasificacion' => $atencionPoblacion->getClasificacion(),
                    'resumen' => $atencionPoblacion->getResumen(),
                    'nivel' => $atencionPoblacion->getNivel() ? 1 : 2,
                    'idinstanciadetalle' => 49,
                    'pcorreo' => $atencionPoblacion->getCorreo(),
                    'pcelular' => $atencionPoblacion->getTelefono(),
                    'espublico' => $atencionPoblacion->getPublico() ? 'S' : 'N',
                    'pnotificacion' => $notificar
    ]
                ,
                 'verify_peer' => false,
                'verify_host' => false,
            ]
        );

        if($response->getStatusCode() == Response::HTTP_OK){
            $codigo =  json_decode($response->getContent());

            return array(
                'status' => 'OK',
                'respuesta' => $codigo->codigoplateamiento
            );
        }elseif ($response->getStatusCode() == Response::HTTP_UNAUTHORIZED){
            $this->login();
            return $this->postTramitar($atencionPoblacion);
        }
        return array(
            'status' => $response->getStatusCode(),
            'respuesta' => 'Error'
        );
    }

    public function postTramitar(AtencionPoblacion $atencionPoblacion)
    {
        try{
            $token = $this->getToken();
            if(is_array($token)){
                return $token['token'];
            }
            if(empty($atencionPoblacion->getNotificar())){
                $notificar = 'N';
            }elseif(count($atencionPoblacion->getNotificar()) == 2){
                $notificar = '3';
            }else{
                $notificar = $atencionPoblacion->getNotificar();
                $notificar[0] == 1 ? $notificar = '2' : $notificar = '1';
            }
            $response = $this->client->request(
                'POST',
                $this->route.$this->getUrl('despachoonlinepoblacion'),[
                    'headers' => [
                        'Authorization' => 'Tercero '.$token
                    ],
                    'body' => ['idprovincia' => 15,
                        'idmunicipio' => $atencionPoblacion->getMunicipio(),
                        'nombre' => $atencionPoblacion->getNombre(),
                        'direccion' => $atencionPoblacion->getDireccion(),
                        'idclasificacion' => $atencionPoblacion->getClasificacion(),
                        'resumen' => $atencionPoblacion->getResumen(),
                        'nivel' => $atencionPoblacion->getNivel() ? 1 : 2,
                        'idinstanciadetalle' => 49,
                        'pcorreo' => $atencionPoblacion->getCorreo(),
                        'pcelular' => $atencionPoblacion->getTelefono(),
                        'espublico' => $atencionPoblacion->getPublico() ? 'S' : 'N',
                        'pnotificacion' => $notificar
                    ],
                    // 'verify_peer' => false,
                    'verify_host' => false,
                ]
            );

            if($response->getStatusCode() == Response::HTTP_OK){
                $codigo =  json_decode($response->getContent());
                return array(
                    'status' => 'OK',
                    'respuesta' => $codigo->codigoplateamiento
                );
            }elseif ($response->getStatusCode() == Response::HTTP_UNAUTHORIZED){
                $this->login();
                return $this->postTramitar($atencionPoblacion);
            }
            return array(
                'status' => $response->getStatusCode(),
                'respuesta' => 'Error'
            );
        }catch (\Exception $es){
            return array(
                'status' => $es->getMessage(),
                'respuesta' => 'Error al cargar gestionar datos'
            );
        }

    }

}