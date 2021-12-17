<?php

namespace App\Controller;
use App\Entity\Juego;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ResultController extends AbstractController
{
    /**
     * @Route("/result", name="result")
     */
    public function index()
    {
//        return $this->render('result/index.html.twig', [
//            'controller_name' => 'ResultController',
//        ]);
//    }
$last_results=$this->getDoctrine()->getRepository(Juego::class)->lastResultXgame();
$lasts=['results'=>$last_results];
    //dump($ga);



return $this->render('result/index.html.twig',$lasts);
}
}
