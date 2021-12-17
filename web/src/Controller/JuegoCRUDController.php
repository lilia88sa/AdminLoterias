<?php

namespace App\Controller;

use App\Entity\Consulta;
use App\Entity\Juego;
use App\Entity\NumeroPremiado;
use App\Entity\Result;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Security\User;
use App\Controller\parsers;
use App\Repository\ResultRepository;
use App\Smalot\PdfParser\Parser;

class JuegoCRUDController extends AbstractController
{
    /**
     * @Route("/juego", name="juego")
     */
    public function index()
    {
//        $pdfFilePath = $this->get('kernel')->getRootDir() . '/../web/example.pdf';
//        $pdfFilePath = 'E:/xampp/tomcat/webapps/docs/architecture/startup/serverStartUp.pdf';
//        $pdfFilePath = 'https://www.loteriasyapuestas.es/f/loterias/documentos/Loter%C3%ADa%20Nacional/listas%20de%20premios/SM_LISTAOFICIAL.A2021.S054.pdf';
//
//        // Cree una instancia de PDFParser
//        $PDFParser = new Parser();
//
//        // Cree una instancia del PDF con el método parseFile del analizador
//        // este método espera como primer argumento la ruta al archivo PDF
//        $pdf = $PDFParser->parseFile($pdfFilePath);
//
//        // Extrae TODO el texto con el método getText
//        $text = $pdf->getText();
//
//        foreach (range(0,180) as $i)
//        dump($text[$i]);

        // Envía el texto como respuesta en el controlador.
       // dump($text);
       // $f=fopen($pdfFilePath,'r');
//        while ($l=fgets($text)){
           // if(strlen(strstr($l, 'Premio mayor,')) > 0)
            //$PDFParser->parseText($l);
//                dump($l);
           // if(strlen(strstr($l, 'Se celebrará el día ')) > 0)
            //    dump($l);
            //Se celebrará el día
//        }
        //fclose($f);

//        $rr=file('https://www.loteriasyapuestas.es/es/resultados/loteria-nacional.html');
//        dump($rr);
       // $finfo = new finfo('https://www.loteriasyapuestas.es/es/resultados/loteria-nacional.html');
      //  dump( $finfo->buffer($_POST["script"]) . "\n");
//        $d=(mime_content_type('https://www.loteriasyapuestas.es/es/resultados/loteria-nacional.html'));
//        dump($d);
//        $finfo = finfo_open(FILEINFO_MIME_TYPE); // devuelve el tipo mime de su extensión
//        foreach (glob("*") as $filename) {
//            echo finfo_file($finfo, $filename) . "\n";
//        }
//        finfo_close($finfo);
//        dump(mime_content_type('https://www.loteriasyapuestas.es'));
//        header('Content-Description: File Transfer');
////        header('Content-Type: application/octet-stream');
//        header('Content-Type: text/html');
//        header('Content-Disposition: attachment; filename=test.html');
//        header('Content-Transfer-Encoding: binary');
//        header('Expires: 0');
//        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//        header('Pragma: public');
////        echo file_get_contents('http://spreadsheetpage.com/downloads/xl/worksheet%20functions.xlsx');
//        dump( file_get_contents('https://www.loteriasyapuestas.es/es/resultados/loteria-nacional'));

//////////////////////////////////////////////////////////////////////
        $this->INSERTAR_RESULTS_EN_MASA_1();
        $d=$this->getDoctrine()->getRepository(Result::class)->findAll();
////
        $e=['result'=>$d];
//        $e=array('quin'=>$d);
//        $ga=$this->getDoctrine()->getRepository(Juego::class)->lastResultXgame();
//        dump($ga);



        return $this->render('juego_crud/index.html.twig',$e);
    }




    PUBLIC FUNCTION INSERTAR_RESULTS_EN_MASA_1(){

//        $c2 = $this->getDoctrine()->getRepository(Consulta::class)->find(array('id'=>2))->consulta;
//        dump($this->consulta(2));
        $em = $this->getDoctrine()->getManager();
       // $games=$this->consulta_r(2);
//        $games=$this->getDoctrine()->getRepository(Juego::class)->juegoClas_();
        $games=$this->getDoctrine()->getRepository(Juego::class)->juegoClas();

       $res=$this->getDoctrine()->getRepository(Result::class);

        //$games=$em->createQuery('select g as juego,oc.name as clasificacion from App\Entity\Juego g join g.orderClasification oc')->getResult();
        foreach ($games as $game){
        $results=parsers::READ_API_XML($game,$res,$em);
//juegoClas
            if($results!=null)
            foreach ($results as $b) {
 dump($b);
//                $cont = $this->getDoctrine()->getRepository(Result::class)->juegoClasification_($game['juego']->getId(),$b->getFecha()->format('Y-m-d'));
                $cont=0;
                if($b instanceof Result){
					$cont = $this->getDoctrine()->getRepository(Result::class)->juegoClasification($game['juego']->getId(),$b->getFecha()->format('Y-m-d'));
					dump($cont);
					}
                

                else if($b instanceof NumeroPremiado)
                    $cont=count($this->getDoctrine()->getRepository(NumeroPremiado::class)->findBy(['fecha'=>$b->getFecha()]));
                // dump($cont);
//                $cont=$em->createQuery('SELECT r.fecha FROM App\Entity\Result r')->getResult();
//    dump($cont);
                if($cont==0){
                   // dump('no esta:'.($b->getFecha()->format('Y-m-d')));
                    $em->persist($b);
                    $em->flush();
                }

            }
        }
    }



}
