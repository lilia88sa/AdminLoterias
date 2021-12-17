<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 11/24/2020
 * Time: 7:52 PM
 */

namespace App\Controller\Front;


use App\Controller\parsers;
use App\Entity\Category;
use App\Entity\Cliente;
use App\Entity\Comments;
use App\Entity\NumeroPremiado;
use App\Entity\Post;
use App\Entity\Result;
use App\Entity\Juego;
use App\Entity\Section;
use App\Entity\Rating;
use App\Form\CommentType;

use Carbon\Carbon;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Storage\StorageInterface;
use Tchoulom\ViewCounterBundle\Counter\ViewCounter as Counter;
use Tchoulom\ViewCounterBundle\Finder\StatsFinder;
use Knp\Component\Pager\PaginatorInterface;
use Gufy\PdfToHtml\Pdf;

class FrontController extends AbstractController
{
    private $storage;

    /**
     * @var Counter
     */
    protected $viewcounter;

    /**
     * @var StatsFinder
     */
    protected $statsFinder;

    public function __construct(
        StorageInterface $storage,
        Counter $viewCounter,
        StatsFinder $statsFinder
    ) {
        $this->storage = $storage;
        $this->viewcounter = $viewCounter;
        $this->statsFinder = $statsFinder;
    }
    /**
     * @Route("/{_locale}", name="homepage",
     *     requirements={
     *         "_locale": "en|es",
     *     },
     *     defaults={"_locale" : "es"})
     */
    public function homeAction(Request $request): Response
    {
//        $em = $this->getDoctrine()->getManager();

//        $slider = $em->createQuery("SELECT s FROM  App\Entity\Section s JOIN s.orderClasification oc
//       WHERE oc.name like '%slider%' and s.publish=1")->setMaxResults(1)->getResult();
//        $sl = count($slider) > 0 ? reset($slider) : false;
//
//        $dir = $em->createQuery("SELECT s FROM  App\Entity\Section s JOIN s.orderClasification oc
// WHERE oc.name like '%directorio%' and s.publish=1")->setMaxResults(1)->getResult();
//          $directorio = count($dir) > 0 ? reset($dir) : false;

        //////////////////// BEGIN FORM////////////////////////////////
//        $em = $this->getDoctrine()->getManager();
//        $post=new Post();
//        $em->persist($post);
//        $em->flush();
        //$post = $this->getDoctrine()->getRepository(Post::class)->find(1);
        $comment = new Comments();
        //$comment->setPost($post);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        // $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
        if ($form->isSubmitted() && $form->isValid()) {
            // $comment->setPost($post);
            $em->persist($comment);
            $em->flush();
            $this->get("session")->getFlashBag()->add("info","Comentario enviado, sera publicado en breve");
            return $this->redirectToRoute('homepage');
        }elseif ($form->isSubmitted() && !$form->isValid()){
            if(!is_null($form['captcha']->getErrors())){
                $this->get("session")->getFlashBag()->add("danger","Los valores del Captcha no coinciden");
            }

        }
        //////////////////// END FORM////////////////////////////////
        ///
        //$games=$this->getDoctrine()->getRepository(Juego::class)->findAll();
        $games=$this->getDoctrine()->getRepository(Juego::class)->findBy([], ['order_home' => 'ASC']);
        $client_all=($this->getDoctrine()->getRepository(Cliente::class)->findAll());
        $client=reset($client_all);
        $last_results=$this->getDoctrine()->getRepository(Juego::class)->lastResultXgame();
        $last10rxg=$this->getDoctrine()->getRepository(Juego::class)->last10ResultXgame();
        $numsPrem=$this->getDoctrine()->getRepository(NumeroPremiado::class)->allNumPrem();
        //$numnsPrem=$this->getDoctrine()->getRepository(NumeroPremiado::class)->alllNumPrem();
        $numnsPrem="[[3,56],[23,56]]";

		//dump($numnsPrem);die;


        return $this->render('front/home.html.twig',['juegos'=>$games,'client'=>$client,'results'=>$last_results,
            'form'=>$form->createView(),'nums'=>$numsPrem,'numsx'=>$numnsPrem,'last10rxg'=>$last10rxg
        ]);
    }

    /**
     * @Route("/last_result", name="last_result")
     */
    public function last_result(Request $request): Response
    {
        $last_results=$this->getDoctrine()->getRepository(Juego::class)->lastResultXgame();
        //FrontController::testChangePage();

          //dump($last_results);
        return $this->render('front/result/result_last.html.twig',['results'=>$last_results]);
    }
    /**
     * @Route("/premiado", name="premiado")
     */
    public function premiado(Request $request): Response
    {
        $numsPrem=$this->getDoctrine()->getRepository(NumeroPremiado::class)->allNumPrem();
		//$nums=[];
		//foreach($num as $v)$nums[]=['id'=>$v->getId(),'fecha'=>$v->getFecha(),'array'=>json_decode($v->getArrayJson())];
        //dump($nums);die;
        return $this->render('front/numero_premiado.html.twig',['nums'=>$numsPrem]);
    }


    /**
     * @Route("/blog", name="blog")
     */
    public function blog(Request $request): Response
    {
        $games=$this->getDoctrine()->getRepository(Juego::class)->findBy([], ['order_home' => 'ASC']);
        $client_all=($this->getDoctrine()->getRepository(Cliente::class)->findAll());
        $client=reset($client_all);
        return $this->render('front/blog.html.twig',['juegos'=>$games,'client'=>$client]);
    }

    protected function visitCount(){
//        $page = $this->statsFinder->loadContents();
        $total = 0;
//        if(count($page) != 0){
//            $page = $page['posts'];
//            foreach ($page as $item) {
//                $total += $item->getTotal();
//            }
//        }

        $date = Carbon::today();
        $dateLastWeek = Carbon::now()->subWeek();
        $date_yesterday = Carbon::yesterday();
        $total = $this->getDoctrine()->getRepository(Post::class)->findByViews();
        $byDay = 0;
        $byYesterday = 0;
        $lastWeek = 0;
        // foreach ($posts as $post) {
        //$day = $this->statsFinder->findByDay($post, 2021, 1, 4, $date->dayName);
        //$year = $this->statsFinder->findByYear($post, $date->year); //WORKS!
        //$month = $this->statsFinder->findByMonth($post, $date->year, $date->month); //WORKS!
        //$week = $this->statsFinder->findByWeek($post, $date->year, $date->month, $date->weekOfYear); //WORKS!


//            $day = $this->statsFinder->findByDay($post, $date->year, $date->month, $date->weekOfYear, $date->dayName);
//            if(!is_null($day)){
//                $byDay +=  $day->getTotal() ? $day->getTotal() : 0;
//            }
//            $yesterday = $this->statsFinder->findByDay($post, $date->year, $date->month, $date->weekOfYear, $date_yesterday->dayName);
//            if(!is_null($yesterday)){
//                $byYesterday +=  $yesterday->getTotal() ? $yesterday->getTotal() : 0;
//            }
//            $last_week = $this->statsFinder->findByWeek($post, $dateLastWeek->year, $dateLastWeek->month, $dateLastWeek->weekOfYear);
//            if(!is_null($last_week)){
//                $lastWeek += $last_week->getTotal() ? $last_week->getTotal() : 0;
//            }
        // }
        return array (
            'total' => $total,
//            'hoy'   => $byDay,
//            'ayer'  => $byYesterday,
//            'semana_pasada' => $lastWeek
        );
    }

    /**
     * @Route("ajax/get-tramiteserv-data/{_locale}", methods={"GET"}, name="get_tramiteserv_data",  options={"expose"=true})
     */
    public function tramiteservAction()
    {
        $secciones = $this->getDoctrine()->getRepository(Section::class)->findAll();
        $tram = Array();
        foreach($secciones as $s){
            $calif = $s->getOrderClasification();
            foreach ($calif as $c){
                if ($c->getName() == "tramites y servicios"){
                    $tram[]=$s;
                }
            }
        }
        if(count($tram) > 0){
            $tramiteserv = (reset($tram))->getCategories() ;
        }else{
            $tramiteserv = null;
        }
        $html = $this->renderView('front/tramiteserv/tramiteserv_inicio_ajax.html.twig', [
            'tramites' => $tramiteserv,
            'seccion' => reset($tram)
        ]);
        return new JsonResponse(array(
            'status' => 'ok',
            'html' => $html
        ));
    }

    public function noticiasinicio(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $consulta2 = $em->getRepository(Post::class)->findLatestNews();

        $response = $this->render('front/noticias/noticias_inicio.html.twig', [

            'noticias' => $consulta2,
            'catnoticias' => count($consulta2) > 0 ? reset($consulta2)->getCategory() : null
        ]);
        $response->setPublic();
        $response->setMaxAge(600);
        $response->setSharedMaxAge(600);

        return $response;
    }

    public function mihabana(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        // $consulta = $em->createQuery('SELECT s FROM App\Entity\Section s WHERE s.orderElement IS NOT NULL ORDER BY s.orderElement ASC');
        $consulta = $em->createQuery("SELECT c FROM  App\Entity\Category c JOIN c.orderClasification oc
     WHERE oc.name like '%personalidades inicio%' and c.publish=1 order by c.orderElement asc ")->getResult();
//
        $fecha = new \DateTime('now');
        $formato = $fecha->format('d/m');
        $hoy   = explode('/', $formato);
        $diahoy = $hoy[0];
        $meshoy = $hoy[1];
        $f='-'.$meshoy.'-'.$diahoy;
        $efemerides11 = $em->createQuery("SELECT p FROM  App\Entity\Post p join p.category c JOIN c.orderClasification oc
       WHERE oc.name like '%efemerides%' and p.optionalDate like '%".$f."%' and c.publish=1 order by p.optionalDate ASC")->getResult();

        $entretenimiento = $em->createQuery("SELECT c FROM  App\Entity\Category c JOIN c.orderClasification oc
       WHERE oc.name like '%entretenimiento%' and c.publish=1")->getResult();


        $imagenespalabras =  $em->createQuery("SELECT c FROM  App\Entity\Category c JOIN c.orderClasification oc
       WHERE oc.name like 'imagenes y palabras' and c.publish=1")->getResult();


//        $categoria = $em->createQuery("SELECT c FROM  App\Entity\Category c JOIN c.orderClasification oc
//       WHERE oc.name like '%efemerides%' and c.publish=1")->setMaxResults(1)->getResult();
        $categoria= count($efemerides11) > 0 ? reset($efemerides11)->getCategory() : false;
//       $efemerides = $categoria ? $categoria->getPost() : null;
//       $fecha = new \DateTime('now');
//       $formato = $fecha->format('d/m');
//       $hoy   = explode('/', $formato);
//       $diahoy = $hoy[0];
//       $meshoy = $hoy[1];
//
//
//       $efe = array();
//       if(!is_null($efemerides)){
//           foreach ($efemerides as $e){
//               $fechaformat = $e->getOptionalDate()->format('d/m');
//
//               $fechaver = explode('/', $fechaformat);
//
//               if($diahoy == $fechaver[0] && $meshoy == $fechaver[1]){
//                   $efe[] = $e;
//               }
//           }
//       }

        $response = $this->render('front/mihabana/mihabana.html.twig', [

            'personalidades' => $consulta,
            'efemerides' => $efemerides11,
            'categoria' => $categoria,
            'galeria' => count($entretenimiento) > 0 ? reset($entretenimiento)->getSection() : false,
            'imagenespalabras' =>  count($entretenimiento) > 0 ? reset($imagenespalabras) : false
        ]);
        $response->setPublic();
        $response->setMaxAge(600);
        $response->setSharedMaxAge(600);

        return $response;
    }

    public function gobiernoprogramas(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $gobierno = $em->createQuery("SELECT c FROM  App\Entity\Category c join c.orderClasification oc
     WHERE oc.name in ('gobierno','composicion del gobierno') and c.publish=1")->getResult();


        $programas = $em->createQuery("SELECT c FROM  App\Entity\Category c join c.orderClasification oc
     WHERE oc.name like 'programas' and c.publish=1")->getResult();

//        $categorias = $this->getDoctrine()->getRepository(Category::class)->findBy(array('publish' => 1));
//        $gobierno = Array();
//        $programas = Array();
//
//        foreach($categorias as $c){
//            $calif = $c->getOrderClasification();
//            foreach ($calif as $ca){
//
//                if ($ca->getName() == "gobierno"){
//                    $gobierno[]=$c;
//                }
//                if ($ca->getName() == "composicion del gobierno"){
//                    $gobierno[]=$c;
//                }
//                if ($ca->getName() == "programas"){
//                    $programas[]=$c;
//                }
//            }
//        }

        $response = $this->render('front/gobierno/gobierno_inicio.html.twig', [
            'gobierno' => $gobierno,
            'programas' => $programas
        ]);
        $response->setPublic();
        $response->setMaxAge(600);
        $response->setSharedMaxAge(600);

        return $response;
    }
    //secciones
    //categorias
    //articulos

    public function menu(Request $request): Response
    {


        $em = $this->getDoctrine()->getManager();
        $consulta = $em->createQuery("SELECT s FROM App\Entity\Section s join s.orderClasification oc
        WHERE oc.name like 'menu' and s.orderElement IS NOT NULL ORDER BY s.orderElement ASC");
        $seccion = $consulta->getResult();



        $em = $this->getDoctrine()->getManager();
        $arr_=array("secciones","identidad","enlamemoria","personalidades","composicion",
            "programas","gobierno","actualidad","tramites","galeria","audio","entretenimiento","blog","glosario");
        $arr=array("identidad","en la memoria","personalidades","composicion del gobierno","programas",
            "gobierno","actualidad","tramites y servicios","galeria","audio","entretenimiento","blog","glosario");
        $res=array();
        $res[$arr_[0]]=$seccion;
        $cont=1;
        foreach($arr as $a)
            $res[$arr_[$cont++]] = $em->createQuery("SELECT c FROM  App\Entity\Category c join c.orderClasification oc
            WHERE oc.name like '".$a."' and c.publish=1 and c.orderElement IS NOT NULL ORDER BY c.orderElement ASC")->getResult();

        $response = $this->render('front/menu.html.twig', $res
        );
        $response->setPublic();
        $response->setMaxAge(600);
        $response->setSharedMaxAge(600);

        return $response;
    }


    /**
     * @Route("ajax/get-footer-data/{_locale}", methods={"GET"}, name="get_footer_data",  options={"expose"=true})
     */
    public function footerAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $consulta = $em->createQuery("SELECT c FROM  App\Entity\Category c JOIN c.section s JOIN s.orderClasification oc
            WHERE oc.name like '%enlace%' and c.publish=1 order by c.orderElement asc")->getResult();

        $total =  $em->createQuery("SELECT SUM(p.views) FROM  App\Entity\Post p ")->getSingleScalarResult();

        $act = $em->createQuery("SELECT p FROM  App\Entity\Post p WHERE p.publish = 1 order by p.updatedAt desc")->setMaxResults(1)->getResult();
        $actualizacion = reset ($act);
        $actualizacion->getUpdatedAt()->modify('-4 hours');


        $html = $this->renderView('front/footer_ajax.html.twig', array(
            'categorias' => $consulta,
            'total' => $total,
            'actualizacion' => $actualizacion
        ));
        return new JsonResponse(array(
            'status' => 'ok',
            'html' => $html
        ));
    }


    /**
     * @Route("/{post}/rating-post", name="rating_post" , methods={"POST"})
     */
    public function ratingPostAction(Request $request, $post){
        $em = $this->getDoctrine()->getManager();
        $rate = $request->get('rate');
        $post = $em->getRepository(Post::class)->findOneById($post);
        $rating = new Rating();
        $rating->setRate($rate);
        $rating->setPost($post);

        $em->persist($rating);
        $em->flush();

        return new JsonResponse([
            'status' => 'ok',
            'rate' => $rate
        ]);
    }


    /**
     * @Route("post_detalles/{_locale}/{id}/{slug}", name="post_detalles",
     *     requirements={
     *         "_locale": "en|es",
     *     },
     *     defaults={"_locale" : "es"})
     */
    public function postdetallesAction(Request $request, $id): Response
    {
        $comment = new Comments();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setPost($post);
            $em->persist($comment);
            $em->flush();
            $this->get("session")->getFlashBag()->add("info","Comentario enviado, sera publicado en breve");
            return $this->redirectToRoute('post_detalles',['id' => $post->getId(), 'slug' => $post->getSlug()]);
        }elseif ($form->isSubmitted() && !$form->isValid()){
            if(!is_null($form['captcha']->getErrors())){
                $this->get("session")->getFlashBag()->add("danger","Los valores del Captcha no coinciden");
            }

        }
        $rate = $em->getRepository(Rating::class)->getRatingValuesByPost($post->getId());
        if(!is_null($rate)){
            $rate = [
                "votes" => $rate[0]["votes"],
                "average" => (integer)round($rate[0]["average"])
            ];
        }

//        $categoria = $post->getCategory();
//        $consulta = $em->createQuery("SELECT c FROM  App\Entity\Category c JOIN  c.orderClasification oc
//            WHERE oc.name like '%crucigrama%' and c.id = :id")->setMaxResults(1)->setParameters(['id' => $categoria->getId()])->getResult();

        $consulta = $em->createQuery("SELECT p FROM  App\Entity\Post p join p.category c JOIN  c.orderClasification oc
            WHERE oc.name like '%crucigrama%' and p.id = ".$post->getId())->setMaxResults(1)->getResult();

        if ( $consulta){
            $puzzle = $post->getPdf() ? $post->getPdf()[0] : null;
            if(!is_null($puzzle) && !is_null($puzzle->getPdfUrl())) {
                $puzzle = file_get_contents($this->storage->resolvePath($puzzle,'files'));
            }else{
                $puzzle = null;
            }
        }
        else{
            $puzzle = null;
        }
        $consulta_sopa = $em->createQuery("SELECT p FROM  App\Entity\Post p join p.category c JOIN  c.orderClasification oc
            WHERE oc.name like '%sopa de palabras%' and p.id = ".$post->getId())->setMaxResults(1)->getResult();
        if ( $consulta_sopa){
            $sopa_palabras = $post->getPdf() ? $post->getPdf()[0] : null;
            if(!is_null($sopa_palabras) && !is_null($sopa_palabras->getPdfUrl())) {
                $sopa_palabras = file_get_contents($this->storage->resolvePath($sopa_palabras,'files'));
            }else{
                $sopa_palabras = null;
            }
        }
        else{
            $sopa_palabras = null;
        }


//        $consulta1 = $em->createQuery("SELECT c FROM  App\Entity\Category c JOIN  c.orderClasification oc
//            WHERE oc.name like '%audio%' and c.id = :id")->setMaxResults(1)->setParameters(['id' => $categoria->getId()])->getResult();
//
//        if ( $consulta1){
//           $audio = true;
//        }
//        else{
//            $audio = false;
//        }

        // salvar contador de visitas
        $post = $this->viewcounter->saveView($post);

        return $this->render('front/post/post_detalles.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
            'rating' => $rate ? $rate : null,
            'puzzle' => $puzzle,
            'sopa_palabras' => $sopa_palabras

        ]);
    }
 /**
     * @Route("juego_detalles/{id}/{slug}", name="juego_detalles")
     *
     */
    public function juegodetallesAction(Request $request, $id): Response

    {

        $juegos = $this->getDoctrine()->getRepository(Juego::class)->findAll();
//        $results = $juego->getResults();


        $results= $this->getDoctrine()->getRepository(Juego::class)->juegoResultFechasDesc($id);
//        dump($results);die;

        return $this->render('front/result/result_detalles1.html.twig',
            ['results' => $results,
             'juegos' => $juegos
            ]

        );

//
//        $comment = new Comments();
//        $form = $this->createForm(CommentType::class, $comment);
//        $form->handleRequest($request);
//        $em = $this->getDoctrine()->getManager();
//       // $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
//        if ($form->isSubmitted() && $form->isValid()) {
//           // $comment->setPost($post);
//            $em->persist($comment);
//            $em->flush();
//            $this->get("session")->getFlashBag()->add("info","Comentario enviado, sera publicado en breve");
//            return $this->redirectToRoute('homepage');
//        }elseif ($form->isSubmitted() && !$form->isValid()){
//            if(!is_null($form['captcha']->getErrors())){
//                $this->get("session")->getFlashBag()->add("danger","Los valores del Captcha no coinciden");
//            }
//
//        }
//        $rate = $em->getRepository(Rating::class)->getRatingValuesByPost($post->getId());
//        if(!is_null($rate)){
//            $rate = [
//                "votes" => $rate[0]["votes"],
//                "average" => (integer)round($rate[0]["average"])
//            ];
//        }

//        $categoria = $post->getCategory();
//        $consulta = $em->createQuery("SELECT c FROM  App\Entity\Category c JOIN  c.orderClasification oc
//            WHERE oc.name like '%crucigrama%' and c.id = :id")->setMaxResults(1)->setParameters(['id' => $categoria->getId()])->getResult();

//        $consulta = $em->createQuery("SELECT p FROM  App\Entity\Post p join p.category c JOIN  c.orderClasification oc
//            WHERE oc.name like '%crucigrama%' and p.id = ".$post->getId())->setMaxResults(1)->getResult();
//
//        if ( $consulta){
//            $puzzle = $post->getPdf() ? $post->getPdf()[0] : null;
//            if(!is_null($puzzle) && !is_null($puzzle->getPdfUrl())) {
//                $puzzle = file_get_contents($this->storage->resolvePath($puzzle,'files'));
//            }else{
//                $puzzle = null;
//            }
//        }
//        else{
//            $puzzle = null;
//        }
//        $consulta_sopa = $em->createQuery("SELECT p FROM  App\Entity\Post p join p.category c JOIN  c.orderClasification oc
//            WHERE oc.name like '%sopa de palabras%' and p.id = ".$post->getId())->setMaxResults(1)->getResult();
//        if ( $consulta_sopa){
//            $sopa_palabras = $post->getPdf() ? $post->getPdf()[0] : null;
//            if(!is_null($sopa_palabras) && !is_null($sopa_palabras->getPdfUrl())) {
//                $sopa_palabras = file_get_contents($this->storage->resolvePath($sopa_palabras,'files'));
//            }else{
//                $sopa_palabras = null;
//            }
//        }
//        else{
//            $sopa_palabras = null;
//        }


//        $consulta1 = $em->createQuery("SELECT c FROM  App\Entity\Category c JOIN  c.orderClasification oc
//            WHERE oc.name like '%audio%' and c.id = :id")->setMaxResults(1)->setParameters(['id' => $categoria->getId()])->getResult();
//
//        if ( $consulta1){
//           $audio = true;
//        }
//        else{
//            $audio = false;
//        }

        // salvar contador de visitas
//        $post = $this->viewcounter->saveView($post);

       /* return $this->render('front/post/post_detalles.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
            'rating' => $rate ? $rate : null,
            'puzzle' => $puzzle,
            'sopa_palabras' => $sopa_palabras

        ]);*/
    }

    /**
     * @Route("category_detalles/{_locale}/{id}/{slug}", name="category_detalles",
     *     requirements={
     *         "_locale": "en|es",
     *     },
     *     defaults={"_locale" : "es"})
     */
    public function categorydetallesAction(PaginatorInterface $paginator, Request $request, $id): Response
    {

        $categoria = $this->getDoctrine()->getRepository(Category::class)->find($id);
//        $post = $categoria->getPost();
        $em = $this->getDoctrine()->getManager();

        $post = $em->createQuery("SELECT p FROM  App\Entity\Post p join p.category c where c.id=".$id."  
            order by p.id desc")->getResult();

        $pagination = $paginator->paginate(
            $post, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('front/category/category_detalles.html.twig', [
            'categoria' => $categoria,
            'paginator' => $pagination
        ]);
    }

    /**
     * @Route("seccion_detalles/{_locale}/{id}/{slug}/{swap}", name="seccion_detalles",
     *     requirements={
     *         "_locale": "en|es",
     *     },
     *     defaults={"_locale" : "es", "swap" : 0 })
     */
    public function secciondetallesAction(PaginatorInterface $paginator, Request $request, $id, $swap): Response
    {

        $em = $this->getDoctrine()->getManager();
//        $consulta = $em->createQuery("SELECT c FROM  App\Entity\Category c JOIN c.section s JOIN s.orderClasification oc
//        WHERE oc.name like 'gobierno' and c.publish=1 and s.id=".$id." order by c.orderElement asc")->getResult();


        $seccion = $this->getDoctrine()->getRepository(Section::class)->find($id);
        $categorias = $em->createQuery("SELECT p FROM  App\Entity\Category p join p.section c where c.id=".$id." and p.publish=1  
            order by p.orderElement asc")->getResult();
        $name = $swap==1?"('gobierno','composicion del gobierno')":($swap==2?"('programas')":($swap==3? "('entretenimiento')":""));
        if($name!="")
            $categorias = $em->createQuery("SELECT c FROM  App\Entity\Category c join c.orderClasification oc
         WHERE oc.name in ".$name." and c.publish=1  order by c.orderElement asc")->getResult();




//            $categorias = $em->createQuery("SELECT c FROM  App\Entity\Category c join c.orderClasification oc
//       WHERE oc.name like '%entretenimiento%' and c.publish=1")->getResult();
//        }
        //else//($entretenimiento = 0 && $gob = 0 && $programa = 0)
        //{$categorias = $seccion->getCategories();}

        $pagination = $paginator->paginate(
            $categorias, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
//
//        foreach($seccion->getOrderClasification() as $cal){
//            if($cal->getName() == "tramites y servicios"){
//                return $this->render('front/tramiteserv/servicios_detalles.html.twig', [
//                    'seccion' => $seccion,
//                    'paginator' => $pagination
//                ]);
//            }
//            if($cal->getName() == "gobierno"){
//                return $this->render('front/gobierno/gobierno_detalles.html.twig', [
//                    'seccion' => $seccion,
//                    'paginator' => $pagination
//                ]);
//            }
//        }
//        switch ($seccion->getOrderClasification())

        return $this->render('front/section/seccion_detalles.html.twig', [
            'seccion' => $seccion,
            'paginator' => $pagination
        ]);
    }


    public function sidebar (Request $request){
        $em = $this->getDoctrine()->getManager();

        $ultimanoticia = $em->createQuery("SELECT p FROM  App\Entity\Post p JOIN p.category c JOIN c.orderClasification oc
 WHERE oc.name like '%noticia%' and p.publish=1 order by p.id desc ")->setMaxResults(1)->getResult();

        $posts= $this->getDoctrine()->getRepository(Post::class)->findByclasification('IMPACTO');
        $impacto = end($posts);

        if($impacto != null){
            $ultimanoticia = $impacto;
        }

        $personalidadesi = $em->createQuery("SELECT c FROM  App\Entity\Category c JOIN c.orderClasification oc
     WHERE oc.name like '%personalidades inicio%' and c.publish=1 order by c.orderElement asc ")->getResult();

        return $this->render('front/sidebar.html.twig', [
            'ultimanoticia' => $ultimanoticia,
            'personalidades' => $personalidadesi
        ]);
    }

//    /**
//     * @Route("ajax/get-footer-visit-data/{_locale}", methods={"GET"}, name="get_footer_visit_data",  options={"expose"=true})
//     */
//    public function footerVisitAction(Request $request){
//        $em = $this->getDoctrine()->getManager();
//
//        $actualizacion = $em->createQuery("SELECT p.title FROM  App\Entity\Post p order by p.title desc")->setMaxResults(1)->getResult();
//
//       $visit_data = $this->visitCount();
//        $html = $this->renderView('front/footer_visitas_ajax.html.twig', array(
//            'hoy' => isset($visit_data['hoy']) ? $visit_data['hoy'] : 0,
//            'ayer' => isset($visit_data['ayer']) ? $visit_data['ayer'] : 0,
//            'semana_pasada' => isset($visit_data['semana_pasada']) ? $visit_data['semana_pasada'] : 0,
//            'total' => isset($visit_data['total']) ? $visit_data['total'] : 0,
//            'actualizacion' => $actualizacion
//        ));
//        return new JsonResponse(array(
//            'status' => 'ok',
//            'html' => $html
//        ));
//    }

    public function footerVisita(Request $request):Response {
        $em = $this->getDoctrine()->getManager();
        $total =  $em->createQuery("SELECT SUM(p.views) FROM  App\Entity\Post p ")->getSingleScalarResult();
//        $post = $this->getDoctrine()->getRepository(Post::class)->findAll();
//        $total = 0;
//        foreach($post as $p){
//            $total += $p->getViews();
//        }

        $actualizacion = $em->createQuery("SELECT p FROM  App\Entity\Post p order by p.title desc")->setMaxResults(1)->getResult();

        $response = $this->render('front/footer_visitas_ajax.html.twig', [

            'total' => $total,
            'actualizacion' => $actualizacion
        ]);
        $response->setPublic();
        $response->setMaxAge(600);
        $response->setSharedMaxAge(600);

        return $response;
    }

    /**
     * @Route("post/{_locale}/{id}", name="post_details",
     *     requirements={
     *         "_locale": "en|es",
     *     },
     *     defaults={"_locale" : "es"})
     */
    public function postdetailsAction(Request $request, $id): Response
    {
        $comment = new Comments();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setPost($post);
            $em->persist($comment);
            $em->flush();
            $this->get("session")->getFlashBag()->add("info","Comentario enviado, sera publicado en breve");
            return $this->redirectToRoute('post_detalles',['id' => $post->getId(), 'slug' => $post->getSlug()]);
        }elseif ($form->isSubmitted() && !$form->isValid()){
            if(!is_null($form['captcha']->getErrors())){
                $this->get("session")->getFlashBag()->add("danger","Los valores del Captcha no coinciden");
            }

        }
        $rate = $em->getRepository(Rating::class)->getRatingValuesByPost($post->getId());
        if(!is_null($rate)){
            $rate = [
                "votes" => $rate[0]["votes"],
                "average" => (integer)round($rate[0]["average"])
            ];
        }

        $categoria = $post->getCategory();
        $consulta = $em->createQuery("SELECT c FROM  App\Entity\Category c JOIN  c.orderClasification oc
            WHERE oc.name like '%crucigrama%' and c.id = :id")->setMaxResults(1)->setParameters(['id' => $categoria->getId()])->getResult();

        $puzzle = $post->getPdf() ? $post->getPdf()[0] : null;
        if(!is_null($puzzle)) {
            $puzzle = file_get_contents($this->storage->resolvePath($puzzle,'files'));
        }
        else{
            $puzzle = null;
        }

//        $consulta1 = $em->createQuery("SELECT c FROM  App\Entity\Category c JOIN  c.orderClasification oc
//            WHERE oc.name like '%audio%' and c.id = :id")->setMaxResults(1)->setParameters(['id' => $categoria->getId()])->getResult();
//
//        if ( $consulta1){
//           $audio = true;
//        }
//        else{
//            $audio = false;
//        }

        // salvar contador de visitas
        $post = $this->viewcounter->saveView($post);
        return $this->render('front/post/post_detail.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
            'rating' => $rate ? $rate : null,
            'puzzle' => $puzzle

        ]);
    }

    /**
     * @Route("cartelera-listado/{_locale}", name="cartelera_listado",
     *     requirements={
     *         "_locale": "en|es",
     *     },
     *     defaults={"_locale" : "es"})
     */
    public function carteleraPapeletaAction(PaginatorInterface $paginator, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $actualidad = $em->createQuery("SELECT s FROM App\Entity\Section s join s.orderClasification oc
        WHERE oc.name like 'actualidad'")->getResult();
        $seccion = reset ($actualidad);
//        dump($seccion);die();
        try{
            $papeleta_rss = [];
            $papeleta_rss_array = simplexml_load_file('http://lapapeleta.cu/feeds/');
            $name = 'Culturales';
            $sitio = $papeleta_rss_array->channel->title;
            $update = $papeleta_rss_array->channel->lastBuildDate ;
            foreach ($papeleta_rss_array->channel->item as $items) {
                $delimiter = explode("\n",$items->description,3);
                if(stristr($delimiter[1],'Habana')){
                    $papeleta_rss[] = $items;
                }
            }
            $papeleta_rss = $paginator->paginate(
                $papeleta_rss, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                10 /*limit per page*/
            );
        }catch (\Exception $exception){
            $papeleta_rss = null;
            $name = '';
        }

        $response = $this->render('front/cartelera_papeleta/cartelera.html.twig', [
            'papeleta_rss' => $papeleta_rss,
            'name' => $name,
            'seccion' => $seccion
        ]);
        $response->setPublic();
        $response->setMaxAge(600);
        $response->setSharedMaxAge(600);
        return $response;
    }

    /**
     * @Route("covid19-habana/{_locale}", name="covid19_habana",
     *     requirements={
     *         "_locale": "en|es",
     *     },
     *     defaults={"_locale" : "es"})
     */

    public function covidHabana (PaginatorInterface $paginator, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $consulta = $em->createQuery("SELECT c FROM App\Entity\Category c join c.orderClasification oc
        WHERE oc.name like 'covid-19'")->getResult();

        $categoria = reset ($consulta);

        $post = $em->createQuery("SELECT p FROM  App\Entity\Post p JOIN p.category c JOIN c.orderClasification oc
 WHERE oc.name like '%covid-19%' and p.publish=1 order by p.createdAt desc ")->setMaxResults(1)->getResult();

        $pagination = $paginator->paginate(
            $post, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('front/category/category_detalles.html.twig', [
            'categoria' => $categoria,
            'paginator' => $pagination
        ]);

    }

    /**
     * @Route("pantallas/{_locale}", name="pantallas",
     *     requirements={
     *         "_locale": "en|es",
     *     },
     *     defaults={"_locale" : "es"})
     */
    public function pantallasAction(Request $request): Response
    {

        $games=$this->getDoctrine()->getRepository(Juego::class)->findBy([], ['order_home' => 'ASC']);
        $client_all=($this->getDoctrine()->getRepository(Cliente::class)->findAll());
        $client=reset($client_all);
        $last_results=$this->getDoctrine()->getRepository(Juego::class)->lastResultXgame();
        $last_5_results=$this->getDoctrine()->getRepository(Juego::class)->last5Results();


        return $this->render('front/pantallas/pantalla.html.twig',['juegos'=>$games,'client'=>$client,'results'=>$last_results,'last_5'=>$last_5_results]);
    }

    public function testChangePage()
    {
        $file = dirname(__FILE__) . 'file:///C:/Users/Lilia%20Suárez%20Alonso/Desktop/SM_LISTAOFICIAL.A2021.S078.pdf';
//        $file =  'https://www.loteriasyapuestas.es/f/loterias/documentos/Loter%C3%ADa%20Nacional/listas%20de%20premios/SM_LISTAOFICIAL.A2021.S078.pdf';
        $pdf = new Pdf($file);
//          $pdf=new Pdf;
//          $pdf->open($file);
          $pdf->generate();
        //        $html = $pdf->html();

//        $tp=$pdf->getPages();

//        $html->goToPage(3);
        // echo count($html->find('body'));
      //  dump(count($html->find('body')));die;

//
       /*$this->assertEquals(1, $html->getCurrentPage());
        $html->goToPage(1);
        $this->assertEquals(1, $html->getCurrentPage());
        $this->assertArrayHasKey('pages', $pdf->getInfo());*/

       // dump($html);die;
    }

}

