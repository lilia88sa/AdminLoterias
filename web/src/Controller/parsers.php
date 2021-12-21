<?php
/**
 * Created by PhpStorm.
 * User: lilia
 * Date: 13/06/2021
 * Time: 07:17 PM
 */
namespace App\Controller;

//use App\Controller\Pdf;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\JuegoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Juego;
use App\Entity\Result;
use App\Entity\NumeroPremiado;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\JuegoCRUDController;
use Smalot\PdfParser\Parser;
use App\Gufy\PdfToHtml\Pdf;

class parsers extends AbstractController
{
    function file_get_contents_ssl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3000); // 3 sec.
        curl_setopt($ch, CURLOPT_TIMEOUT, 10000); // 10 sec.
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

PUBLIC function feed($feedURL){
$i = 0;
$url = $feedURL;
    $context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));

    $xml = file_get_contents_ssl($url);
    $rss = simplexml_load_file($xml);
    foreach($rss->channel->item as $item) {
    $link = $item->link;  //extrae el link
    $title = $item->title;  //extrae el titulo
    $date = $item->pubDate;  //extrae la fecha
	$guid = $item->guid;  //extrae el link de la imagen
    $description = strip_tags($item->description);  //extrae la descripcion
    if (strlen($description) > 400) { //limita la descripcion a 400 caracteres
    $stringCut = substr($description, 0, 200);
    $description = substr($stringCut, 0, strrpos($stringCut, ' ')).'...';}
    if ($i < 16) { // extrae solo 16 items
     echo '<div class="cuadros1"><h4><a href="'.$link.'" target="_blank">'.$title.'</a></h4><br><img src="'.$guid.'"><br>'.$description.'<br><div class="time">'.$date.'</div></div>';}
     $i++;}
	echo ('<div style="clear: both;"></div>');
    }
//    parsers::feed("http://norfipc.com/rss.xml");

PUBLIC FUNCTION READ_API_XML(array $game,$res,$em){
            $clas=$game['clasificacion'];
//        dump($clas);
            $game=$game['juego'];
            $url = __DIR__ .$game->getUrl();
         parsers::fileWriteBoteFechaPremio($game,$clas,$em,$res);
//        if($clas=='nacional')
//         return parsers::fileWriteLotNac($game);

//          parsers::getPathFileWriteLotNac($game,$res);
//         return null;
          //$clas
            parsers::fileWrite($game,$clas);
//        return;
              //dump($clas);
            switch ($clas){
                case 'gordo':return parsers::arregloGordoJuego($clas,$game);
                
               /* case 'nacional':
				$a=parsers::getResultNac($game,$res);
				dump($a);
				return $a;*/
                case 'euromillones':return parsers::arregloEuroMillonesJuego($clas,$game);
                case 'lototurf':return parsers::arregloLototurfJuego($clas,$game);
                case 'quintuple':return parsers::arregloQuintupleJuego($clas,$game);
                case 'primitiva':return parsers::arregloPrimitivaJuego($clas,$game);
                case 'bonoloto':return parsers::arregloBonolotoJuego($clas,$game);
                case 'quiniela':return parsers::arregloQuinielalJuego($clas,$game);
                case 'quinigol':return parsers::arregloQuinigolJuego($clas,$game);
				case 'nacional':return parsers::getResultNac($game,$res);
                default:return null;
            }
            return null;
        }
private function arregloLototurfJuego($clas,$game)
{
    //parsers::fileWrite($game,$clas);
    $_ =[];
    $f = fopen($clas . '.xml', 'r');

    $lines =[];
    while ($l = fgets($f)) {
        $lines[] = $l;
//        dump($l);
        if (count($lines)>55 && strlen(strstr($l, '</descripcion>')) > 0 && strlen(strstr($lines[count($lines) - 55], '<descripcion>')) > 0) {
           // dump($lines);die;
            $d=array_slice($lines,count($lines)-55);
           // dump($d);die;
            $caballo=explode(')',explode('Caballo(',$d[1])[1])[0];
            $reintegro=explode(')',explode('R(',$d[1])[1])[0];
           // dump($caballo.'  '.$reintegro);die;
            //$reintegro=str_replace();
            $fecha=parsers::fecha($d[0]);
            //dump($fecha);
            $combinaciones=str_replace(' - ',',',substr($d[1],0,strpos($d[1],' Caballo')));
           // dump($combinaciones);
            $cat=[];
            $pre=[];
            foreach (range(10,45,5) as $i){$cat[]=str_replace(['&uacute;','&ordf;','<br />'],['ú','°',''],trim($d[$i]));$pre[]=str_replace(' &euro;<br />','',trim($d[$i+2]));}
            //dump(join(';',$pre));
            $premios=(join(';',$pre));
            //dump(join(',',$cat));
            $categorias=(join(',',$cat));

//            $_[]=(new Result($fecha,$combinaciones,$game,null, null, null,null,null, null,null, null, null,null,null,null));
            $_[]=(new Result(['premio'=>$premios,'categoria'=>$categorias,'reintegros'=>$reintegro,'caballo'=>$caballo,'fecha'=>$fecha,'combinaciones'=>$combinaciones,'juego'=>$game]));


        }
    }

    //dump($_);
    fclose($f);
    return $_;
}
private function arregloQuintupleJuego($clas,$game)
{
    //parsers::fileWrite($game,$clas);
    $_ =[];
    $f = fopen($clas . '.xml', 'r');

    $lines =[];
    while ($l = fgets($f)) {
        $lines[] = $l;
//        dump($l);


        if (count($lines)>13 && strlen(strstr($l, '</descripcion>')) > 0 && strlen(strstr($lines[count($lines) - 13], '<descripcion>')) > 0) {
//            dump($l);
            $d=array_slice($lines,count($lines)-13);
            //dump($d);
            $fecha=parsers::fecha($d[0]);
            //dump($fecha);
            $combinaciones=array_slice($d,3,6);
            //dump($combinaciones);die;
            $comb=[];
            foreach ($combinaciones as $c){
                $al=str_replace(['<br />',': '],['',','],trim($c));
                $comb[]=substr($al,2,strlen($al));
                //$arr=explode(' ',trim($c));
                //$comb[]=/*array_slice($arr,3,6).','.*/str_replace('<br','',$arr[count($arr)-2]);
            }
            $combinaciones=(join(';',$comb));
          //  dump($combinaciones);die;
            $str=str_replace('Categoría Acertantes Premios  ','',$d[12]);
            $cat_pre=explode(' €',$str);
            $cat_pre=array_slice($cat_pre,0,5);
            //dump($cat_pre);
            $cat=[];
            $pre=[];
            foreach ($cat_pre as $cp){
                $__=explode(') ',$cp);
                $cat[]=$__[0].')';
                $pre[]=$__[1];
            }
            $premios=(join(';',$pre));
//            dump(join(',',$cat));
            $categorias=(join(',',$cat));
            //dump($premios.';'.$categorias);

            $_[]=(new Result(['fecha'=>$fecha,'combinaciones'=>$combinaciones,'premio'=>$premios,'categoria'=>$categorias,'juego'=>$game]));



        }
    }
    //dump($_);
    fclose($f);
    return $_;
}
private function arregloGordoJuego($clas,$game)
{
    //parsers::fileWrite($game,$clas);
    $_ =[];
    $f = fopen($clas . '.xml', 'r');

    $lines =[];
    while ($l = fgets($f)) {
        $lines[] = $l;
//        dump($l);


        if (count($lines)>60 && strlen(strstr($l, '</descripcion>')) > 0 && strlen(strstr($lines[count($lines) - 60], '<descripcion>')) > 0) {
//            dump($l);
//            dump('YYYYYYYYYYYYYYYYYYYYYYYEEEEEEEEEEEEEEEEEEEEEEEEEEEEESSSSSSSSSSSSSSSSSSSSSSSSSSS');
            $d=array_slice($lines,count($lines)-60);
           // dump($d);die;
            //$aux=trim(explode(':',$d[1])[1]);
            //dump($aux);die;
            $numero_calve=str_replace(['R(',')<br />'],'',trim(explode(':',$d[1])[1]));
            $fecha=parsers::fecha($d[0]);
            //dump($fecha);
            $combinaciones=str_replace(' - ',',',substr($d[1],0,strpos($d[1],' Nú')));
            //dump($combinaciones);
            $cat=[];
            $pre=[];
            foreach (range(10,45,5) as $i){$cat[]=str_replace(['&ordf;','<br />'],['ª',''],trim($d[$i]));$pre[]=str_replace(' &euro;<br />','',trim($d[$i+2]));}
            //dump(join(';',$pre));
            $premios=(join(';',$pre));
            //dump(join(',',$cat));
            $categorias=(join(',',$cat));
           // $resp=['fecha'=>$fecha,'combinaciones'=>$combinaciones,'premios'=>$premios,'categorias'=>$categorias,'juego'=>$game];
            $_[]=(new Result(['fecha'=>$fecha,'clave'=>$numero_calve,'combinaciones'=>$combinaciones,'premio'=>$premios,'categoria'=>$categorias,'juego'=>$game]));



        }
    }
   //dump($_);
    fclose($f);
    return $_;
}
private function arregloPrimitivaJuego($clas,$game)
{
    //parsers::fileWrite($game,$clas);
    $_ = [];
    $f = fopen($clas . '.xml', 'r');

    $lines =[];
    while ($l = fgets($f)) {
        $lines[] = $l;
//        dump($l);


        if (count($lines)>87 && strlen(strstr($l, '</descripcion>')) > 0 && strlen(strstr($lines[count($lines) - 87], '<descripcion>')) > 0) {
//            dump($l);
////            dump('YYYYYYYYYYYYYYYYYYYYYYYEEEEEEEEEEEEEEEEEEEEEEEEEEEEESSSSSSSSSSSSSSSSSSSSSSSSSSS');
//            $c=2;
//               while(true){
//                   if(!(strlen(strstr($lines[count($lines) - $c], '<descripcion>')) > 0)){$c++;continue;}
//                   break;
//               }
//
//             if($c>2){
            $d=array_slice($lines,count($lines)-87);
            //dump($d);die;
//               }
            $combinaciones=str_replace(' - ',',',substr($d[1],0,strpos($d[1],' Comple')));
            //dump($combinaciones);
            $C=explode(')',explode('C(',$d[1])[1])[0];
            $R=explode(')',explode('R(',$d[1])[1])[0];
            $joker=parsers::entre_str($d[1],'Joker: ','<br />');
            //dump($joker);
//            dump($combinaciones);
            $fecha=parsers::fecha($d[0]);
            //dump($fecha);
            $cat=[];
            $pre=[];
            foreach (range(10,35,5) as $i){$cat[]=str_replace(['&ordf;','<br />'],['ª',''],trim($d[$i]));$pre[]=str_replace(' &euro;<br />','',trim($d[$i+2]));}
            //dump(join(';',$pre));
            $premios=(join(';',$pre));
            //dump(join(',',$cat));
            $categorias=(join(',',$cat));
            ////////////////////////////////
            $catJ=[];
            $preJ=[];
            foreach (range(54,78,4) as $i){$catJ[]=str_replace(['&ordf;','<br />'],['ª',''],trim($d[$i]));$preJ[]=str_replace(' &euro;<br />','',trim($d[$i+1]));}
          //  dump(join(';',$preJ));
            $premiosJ=(join(';',$preJ));
            //dump(join(',',$catJ));
            $categoriasJ=(join(',',$catJ));
//            $_[]=(new Result($fecha,$combinaciones,$game,null, null, null,null,null, null,$joker, $categorias, $premios,$categoriasJ, $premiosJ,null));
            $_[]=(new Result(['reintegros'=>$R,'complementos'=>$C,'fecha'=>$fecha,'combinaciones'=>$combinaciones,'premio'=>$premios,'categoria'=>$categorias,'juego'=>$game,'millon_joker'=>$joker,'premio_joker'=>$premiosJ,'categoria_joker'=>$categoriasJ]));

//
        }
    }
//    dump($lines);
   //dump($_);
    fclose($f);
    return $_;

}
private function arregloBonolotoJuego($clas,$game)
{
    //parsers::fileWrite($game,$clas);
    $_=[];
    $f = fopen($clas.'.xml','r');

    $lines=[];
    while ($l=fgets($f)) {
        $lines[] = $l;
//        dump($l);


        if(count($lines) >= 45 && strlen(strstr($l, '</descripcion>')) > 0 && strlen(strstr($lines[count($lines) - 45], '<descripcion>')) > 0) {
//            dump($l);
//            dump('YYYYYYYYYYYYYYYYYYYYYYYEEEEEEEEEEEEEEEEEEEEEEEEEEEEESSSSSSSSSSSSSSSSSSSSSSSSSSS');
            $d=array_slice($lines,count($lines)-45);
            //dump($d);die;

            $combinaciones=str_replace(' - ',',',substr($d[1],0,strpos($d[1],' Comple')));

           // dump($combinaciones);

            $C=explode(')',explode('C(',$d[1])[1])[0];
            $R=explode(')',explode('R(',$d[1])[1])[0];

            $fecha=parsers::fecha($d[0]);
            //dump($fecha);

            $cat=[];
            $pre=[];
            foreach (range(10,30,5) as $i){$cat[]=str_replace(['&ordf;','<br />'],['ª',''],trim($d[$i]));$pre[]=str_replace(' &euro;<br />','',trim($d[$i+2]));}
            //dump(join(';',$pre));
            $premios=(join(';',$pre));
           // dump(join(',',$cat));
            $categorias=(join(',',$cat));

//            $_[]=(new Result($fecha,$combinaciones,$game,null, null, null,null,null, null,null, $categorias, $premios,null,null,null));
             $_[]=(new Result(['reintegros'=>$R,'complementos'=>$C,'fecha'=>$fecha,'combinaciones'=>$combinaciones,'premio'=>$premios,'categoria'=>$categorias,'juego'=>$game]));
        }
    }
   //dump($_);
    fclose($f);
    return $_;


}
private function arregloEuroMillonesJuego($clas,$game)
{
    //parsers::fileWrite($game,$clas);
    $_=[];
    $f = fopen($clas.'.xml','r');

    $lines=[];
    while ($l=fgets($f)) {
        $lines[] = $l;
//        dump($l);


        if(count($lines) >= 106 && strlen(strstr($l, '</descripcion>')) > 0 && strlen(strstr($lines[count($lines) - 107], '<descripcion>')) > 0) {
//            dump($l);
//            dump('YYYYYYYYYYYYYYYYYYYYYYYEEEEEEEEEEEEEEEEEEEEEEEEEEEEESSSSSSSSSSSSSSSSSSSSSSSSSSS');
            $d=array_slice($lines,count($lines)-107);
           // dump($d);

//            $millon=substr(trim($d[1]), strpos($d[1],'n: ')+3,strpos($d[1],'<br />')-strpos($d[1],'n: ')-3);
            $millon=parsers::entre_str($d[1],'n: ','<br />');
           // dump($millon);

            $combinaciones=str_replace(' - ',',',substr($d[1],0,strpos($d[1],' Estrellas: ')));

           // dump($combinaciones);
            $estrellas=str_replace(' - ',',',substr(trim($d[1]), strpos($d[1],' Estrellas: ')+11,strpos($d[1],'Mill')-strpos($d[1],' Estrellas: ')-11));
           // dump($estrellas);
            //combinaciones
//            $comb=explode(' ',$est_comb[0]);
//            dump($comb);
            $fecha=parsers::fecha($d[0]);
           // dump($fecha);

            $cat=[];
            $pre=[];
            foreach (range(10,82,6) as $i){$cat[]=str_replace(['&ordf;','<br />'],['ª  (',')'],trim($d[$i]));$pre[]=str_replace(' &euro;<br />','',trim($d[$i+2]));}
           // dump(join(';',$pre));
            $premios=(join(';',$pre));
           // dump(join(',',$cat));
            $categorias=(join(',',$cat));

//            $_[]=(new Result($fecha,$combinaciones,$game,$estrellas, null, null,null,null, null,$millon, $categorias, $premios,null,null,null));
            $_[]=(new Result(['fecha'=>$fecha,'combinaciones'=>$combinaciones,'premio'=>$premios,'categoria'=>$categorias,'juego'=>$game,'millon_joker'=>$millon,'estrellas'=>$estrellas]));

//            $_[]=parsers::extraerQuinigolJuego($meses,$game,$d);


        }
    }
  // dump($_);
    fclose($f);
    return $_;
}
private function entre_str($str,$c1,$c2){

        return substr(trim($str), strpos($str,$c1)+strlen($c1),strpos($str,$c2)-strpos($str,$c1)-strlen($c1));

}
function array_map_assoc(callable $f, array $a) {
    return array_reduce(array_map($f, array_keys($a), $a), function (array $acc, array $a) {
        return $acc + $a;
    }, []);
}
private function arregloQuinielalJuego($clas,$game){
    //parsers::fileWrite($game,$clas);
  
/////////////////////////////////////
    $_=[];
    $f = fopen($clas.'.xml','r');

    $lines=[];
    while ($l=fgets($f)){
        $lines[]=$l;
//        dump($l);


        if(count($lines) >=23 && strlen(strstr($l,'</descripcion>'))>0 && strlen(strstr($lines[count($lines)-23],'<descripcion>'))>0){
//            dump('YYYYYYYYYYYYYYYYYYYYYYYEEEEEEEEEEEEEEEEEEEEEEEEEEEEESSSSSSSSSSSSSSSSSSSSSSSSSSS');

//            $arr[]=array_slice($lines,count($lines)-14);
            $d=array_slice($lines,count($lines)-23);
           //dump($d);die;
//            dump($l);
            $bb=[];
////                       if(count($d)>=11)
            foreach (range(4,17) as $i){
                $cc=explode(' ',str_replace('<br />','',trim($d[$i])));
                $r=join(' ',array_slice($cc,1,count($cc)-2)).','.$cc[count($cc)-1];
                $bb[]=$r;
            }
            $cc=explode(' ',str_replace('<br />','',trim($d[18])));
            $r=join(' ',array_slice($cc,0,count($cc)-3)).','.$cc[count($cc)-3].$cc[count($cc)-2].$cc[count($cc)-1];
            $bb[]=$r;
//
            $resultados=join(';',$bb);
//            dump($resultados);
            $cat_pre=array_slice(explode(' €',str_replace('Categoría Acertantes Premios ','',explode('</descripcion>',trim($d[21]))[0])),1,5);
//            dump($cat_pre);
            $cat=[];
            $pre=[];
            foreach ($cat_pre  as $cp)
                {
                    $sss=explode('     ',$cp);
                    $cat[]=$sss[0];
                    $pre[]=$sss[1];
                }
            $categorias=join(',',$cat);
            $premios=join(';',$pre);
//            dump($premios.'   '.$categorias);
//
////                        $resp[]=array('resultados'=>$resultados,'categorias'=>$categorias,'premios'=>$premios);
////    $fecha_ = \DateTime::createFromFormat('Y-m-d', '2021-05-06');
//            //////////////////////////////////////////////////////////////
////                       $fecha =;
            $fecha=parsers::fecha($d[0]);
//            dump($fecha);
//
//            $_[]=(new Result($fecha, $resultados,$game,null, null, null,null,null, null,null, $categorias, $premios,null,null,null));
            $_[]=(new Result(['fecha'=>$fecha,'combinaciones'=>$resultados,'premio'=>$premios,'categoria'=>$categorias,'juego'=>$game]));
//            $_[]=parsers::extraerQuinigolJuego($meses,$game,$d);

        }
    }
  //  dump($_);die;
    fclose($f);
    return $_;
    }
private function arregloQuinigolJuego($clas,$game){
    //parsers::fileWrite($game,$clas);

    $_=[];
    $f = fopen($clas.'.xml','r');

    $lines=[];
    while ($l=fgets($f)){
        $lines[]=$l;
//        dump($l);


        if(count($lines) >=14 && strlen(strstr($l,'</descripcion>'))>0 && strlen(strstr($lines[count($lines)-14],'<descripcion>'))>0){
//            dump('YYYYYYYYYYYYYYYYYYYYYYYEEEEEEEEEEEEEEEEEEEEEEEEEEEEESSSSSSSSSSSSSSSSSSSSSSSSSSS');

//            $arr[]=array_slice($lines,count($lines)-14);
            $d=array_slice($lines,count($lines)-14);
//            dump($d);
            $bb=[];
//                       if(count($d)>=11)

            foreach (range(4,9) as $i){
                $cc=explode(' ',str_replace('<br />','',trim($d[$i])));
                $r=join(' ',array_slice($cc,1,count($cc)-4)).','.$cc[count($cc)-3].$cc[count($cc)-2].$cc[count($cc)-1];
                $bb[]=$r;
            }
            // dump($bb);die;
            $resultados=join(';',$bb);
//            dump($resultados);
            $cat_pre=array_slice(explode(' €',str_replace('Categoría Acertantes Premios ','',explode('</descripcion>',trim($d[13]))[0])),0,5);
//            dump($cat_pre);
            $cat=[];
            $pre=[];
            foreach ($cat_pre  as $cp)
                {
                    $sss=explode('     ',$cp);
                    $cat[]=$sss[0];
                    $pre[]=$sss[1];
                }
            $categorias=join(',',$cat);
            $premios=join(';',$pre);
//            dump($premios.'   '.$categorias);

//                        $resp[]=array('resultados'=>$resultados,'categorias'=>$categorias,'premios'=>$premios);
//    $fecha_ = \DateTime::createFromFormat('Y-m-d', '2021-05-06');
            //////////////////////////////////////////////////////////////
//                       $fecha =;
            $fecha=parsers::fecha($d[0]);
//            dump($fecha);
//
//            $_[]=(new Result($fecha, $resultados,$game,null, null, null,null,null, null,null, $categorias, $premios,null,null,null));
            $_[]=(new Result(['fecha'=>$fecha,'combinaciones'=>$resultados,'premio'=>$premios,'categoria'=>$categorias,'juego'=>$game]));
//            $_[]=parsers::extraerQuinigolJuego($meses,$game,$d);

        }
    }
   //dump($_);
    fclose($f);
    return $_;

}
private function fecha($str){

    $meses=['Jan'=>'01','Feb'=>'02','Mar'=>'03','Apr'=>'04','May'=>'05','Jun'=>'06','Jul'=>'07','Aug'=>'08','Sep'=>'09','Oct'=>'10','Nov'=>'11','Dec'=>'12'];
    $fecha=explode(' ',substr(trim($str), strpos($str,'<fecha>')+7,strpos($str,'</fecha>')-strpos($str,'<fecha>')-7));
//            dump(strpos($str,'</fecha>'));
//            dump($fecha);
//    $fecha=$fecha[3].'-'.$meses[$fecha[2]].'-'.$fecha[1].' '.$fecha[4];
    $fecha=$fecha[3].'-'.$meses[$fecha[2]].'-'.$fecha[1];
//   dump($fecha);//die;
    return (\DateTime::createFromFormat('Y-m-d', $fecha));

}
public function fileWrite($game,$file_name){
    $url=$game->getUrl();
    if(in_array($file_name,['lototurf','quintuple']))
        $url=str_replace('/botes/','/resultados/',$url);
    $rss = simplexml_load_file($url);
   // if(in_array($file_name,['lototurf']))
     // dump($rss);die;

    $f=fopen($file_name.'.xml','w');
    //fwrite($f,$rss);
    //fclose($f);
     $c=0;
    foreach($rss->channel->item as $item) { //dump($clas);
        fwrite($f,('<item>'));
        fwrite($f,nl2br('<numero>'.(++$c).'</numero>'));
//                        $link = $item->link;  //extrae el link
        $title = $item->title;  //extrae el titulo
        fwrite($f,nl2br('<title>'.$title.'</title>'));
        $date = $item->pubDate;  //extrae la fecha
        fwrite($f,nl2br('<fecha>'.$date.'</fecha>'));
////                        $guid = $item->guid;  //extrae el link de la imagen
        $description = trim(strip_tags($item->description));  //extrae la descripcion
        fwrite($f,nl2br('<descripcion>'.$description.'</descripcion>\n'));
        //fwrite($f,'////////////////////////////////////////////////////////////');
        fwrite($f,('</item>'));

////                        dump(array($link,$title,$date,$guid,$description));
//                        dump(array($description));
    }
    fclose($f);

}
public  function fileWriteBoteFechaPremio($g,$clas,$em,$res){
        //if(!in_array($g->getId(),[9,5,4,6,3,7,8,1]))return [];

        if($clas=='nacional'){
             parsers::fileWriteLotNac($g,$em,$res);
             return;
        }
        $urls=['primitiva'=>'https://www.loteriasyapuestas.es/es/la-primitiva/botes/.formatoRSS',
               'euromillones'=>'https://www.loteriasyapuestas.es/es/euromillones/botes/.formatoRSS',
               'bonoloto'=>'https://www.loteriasyapuestas.es/es/bonoloto/botes/.formatoRSS',
               'quiniela'=>'https://www.loteriasyapuestas.es/es/la-quiniela/botes/.formatoRSS',
               'quinigol'=>'https://www.loteriasyapuestas.es/es/el-quinigol/botes/.formatoRSS',
               'lototurf'=>'https://www.loteriasyapuestas.es/es/lototurf/botes/.formatoRSS',
               'quintuple'=>'https://www.loteriasyapuestas.es/es/lototurf/botes/.formatoRSS',
               'gordo'=>'https://www.loteriasyapuestas.es/es/gordo-primitiva/.formatoRSS',
            ];
    $url=$urls[$clas];
    $meses = ['enero' => '01', 'febrero' => '02', 'marzo' => '03', 'abril' => '04', 'mayo' => '05', 'junio' => '06', 'julio' => '07', 'agosto' => '08', 'septiembre' => '09', 'octubre' => '10', 'noviembre' => '11', 'diciembre' => '12'];


    $rss = simplexml_load_file($url);
//    $f=fopen($fn.'.xml','w');
    //fwrite($f,$rss);
    //fclose($f);

    $c=0;
    foreach($rss->channel->item as $item) { //dump($clas);
        $c++;
        if((in_array($clas,['quintuple','gordo']) && $c==2)|| in_array($clas,['euromillones','primitiva',
                'lototurf','quiniela','bonoloto','quinigol','nacional'])) {
            $item_link = $item->link;
//            dump($clas);
            if(substr_count($item_link,'bote-de-')==0)continue;
            $dinero_bote = str_replace('-', '.', explode('euro', explode('bote-de-', $item_link)[1])[0]);
            if(substr_count($dinero_bote,'.')>=2){
                $fc=explode('.',$dinero_bote);
                $mid=$fc[1];
                $mm='';
                $cont=0;
                foreach (array_reverse(str_split($mid)) as $m){
                    if($m=='0'){$cont++;continue;}
                    else{break;}

                }
                if($cont==3)$dinero_bote=$fc[0].' M';
               // if($cont==2)$dinero_bote=$fc[0].','.$mid[0].' M';
                if($cont==1||$cont==2)$dinero_bote=$fc[0].','.substr($mid,0,$cont==2?1:2).' M';
                if($cont==0)$dinero_bote=$fc[0].','.$mid.' M';


               // $dinero_bote=str_replace($str, ' M',$dinero_bote);
            }
                //$dinero_bote =  substr($dinero_bote ,0,strlen($dinero_bote )-strpos($dinero_bote ,'0')).' M';

//            dump($g->getName().' '.$dinero_bote);

             $ss12 = explode('-', explode('.formatoRSS', explode('euro-', $item_link)[1])[0]);
            $fecha = join('-', [$ss12[4], $meses[$ss12[2]], $ss12[0]]);
            $fecha_bote = (\DateTime::createFromFormat('Y-m-d', $fecha));
//            dump($g->getName().' ');
//            dump($fecha_bote);
            if((new \DateTime('@'.strtotime('now')))<=$fecha_bote){
                $g->setBote($dinero_bote);
                $g->setFechaBote($fecha_bote);
            }else {
                $g->setBote(null);
                $g->setFechaBote(null);
            }
            $em->persist($g);
            $em->flush();
            return;
//            $em = $this->getDoctrine()->getManager();


            //return [$g];
           // break;
        }

//        fwrite($f,('<item>'));
//        fwrite($f,nl2br('<numero>'.(++$c).'</numero>'));
////                        $link = $item->link;  //extrae el link
//        $title = $item->title;  //extrae el titulo
//        fwrite($f,nl2br('<title>'.$title.'</title>'));
//        $date = $item->pubDate;  //extrae la fecha
//        fwrite($f,nl2br('<fecha>'.$date.'</fecha>'));
//////                        $guid = $item->guid;  //extrae el link de la imagen
//        $description = trim(strip_tags($item->description));  //extrae la descripcion
//        fwrite($f,nl2br('<descripcion>'.$description.'</descripcion>\n'));
//        //fwrite($f,'////////////////////////////////////////////////////////////');
//        fwrite($f,('</item>'));

////                        dump(array($link,$title,$date,$guid,$description));
//                        dump(array($description));
    }
//    fclose($f);


}
public function get_Day_Date($_url){
	 $_PDFParser = new Pdf();
     
     $_text=$_PDFParser->text($_url);
	//dump($_text);die;
	 /////////////////////////////////////
	 $_f=fopen('result_nacional.txt','w');
        fwrite($_f,$_text);
        fclose($_f);
	 ///////////////////////////////
	  $_meses = ['enero' => '01', 'febrero' => '02', 'marzo' => '03', 'abril' => '04', 'mayo' => '05', 'junio' => '06', 'julio' => '07', 'agosto' => '08', 'septiembre' => '09', 'octubre' => '10', 'noviembre' => '11', 'diciembre' => '12'];
    $_day_week=['Mon'=>'LUNES','Tue'=>'MARTES','Wed'=>'MIERCOLES',
	'Thu'=>'JUEVES','Fri'=>'VIERNES','Sat'=>'SABADO','Sun'=>'DOMINGO'];
      ///////////////////////////////
	 $_tag='';
	  $_f=fopen('result_nacional.txt','r');
        while ($_l=fgets($_f)) {
            $_lines[]=$_l;
            //dump($text);
			 if(substr_count($_l, 'SORTEO DEL DÍA') > 0){
				
				 $_str=explode('Diez',$_l)[0];
				  //dump($str);die;
				  $_d=explode(' ',$_str);
				   //dump($d);die;
				  $_dd=join('_',[(strlen($_d[0])==1?'0':'').$_d[0],$_meses[strtolower($_d[2])],substr($_d[4],2,4)]);
				  $_df=join('-',[$_d[4],$_meses[strtolower($_d[2])],$_d[0]]);
				 // $fecha=$year.'-'.$meses[$r[2]].'-'.$r[0];

                $_dw = $_day_week[(\DateTime::createFromFormat('Y-m-d',$_df)->format('D'))];
				  //dump($dw.'_'.$dd);die;
				  $_tag=$_dw.'_'.$_dd;
				  
				  break;
				  
			 }
		}
		fclose($_f);
	 ///////////////////////////////////////////////////
	 return'https://www.loteriasyapuestas.es/f/loterias/documentos/Loter%C3%ADa%20Nacional/notas%20de%20prensa/PREMIOS_MAYORES_DEL_SORTEO_DE_LOTERIA_NACIONAL_'.$_tag.'.pdf';
	 
}
public function getResult($_url){
	 $_PDFParser = new Pdf();
      $_text=$_PDFParser->text($_url);
	// dump($_text);die;
	  /////////////////////////////////////
	 $_f=fopen('result_nacional.txt','w');
        fwrite($_f,$_text);
        fclose($_f);
	 ///////////////////////////////
	 $_Rs=[];
	 $_f=fopen('result_nacional.txt','r');
        while ($_l=fgets($_f)) {
	  if(substr_count($_l, 'REINTEGROS') > 0){
		  //dump($_l);die;
		   foreach (str_split($_l) as $_n)
                     if(is_numeric($_n))
						 $_Rs[]=$_n;
					 break;
                     
	  }         
	    
	  }fclose($_f);
//	  dump($_Rs);//die;
	
}
public function parseElPais(){
	$_url='https://servicios.elpais.com/sorteos/loteria-nacional/';
	$_text=file_get_contents($_url);
	
	/////////////////////////////////
	 $_f=fopen('result_nacional.txt','w');
        fwrite($_f,$_text);
        fclose($_f);
	////////////////////////////////
	$_f=file('result_nacional.txt');
	//dump($_f);die;
	$fecha_reint=[];
	foreach(range(0,count($_f)-1) as $_i){
		if(substr_count($_f[$_i],'<div class="fecha">')>0){
			$fecha=explode('</div>',explode('<div class="fecha">',$_f[$_i])[1])[0];
			$r=[];
			foreach(str_split($_f[$_i+19])as $n)
			   if(is_numeric($n))
				   $r[]=$n;
			$fecha_reint[]=['fecha'=>$fecha,'reintegros'=>$r];   
		}
		
	}//dump($fecha_reint);dump($_f);die;
	return $fecha_reint;
	
}
public function sorteo($fecha){
   
   $path='https://www.loteriasyapuestas.es/f/loterias/documentos/Loter%C3%ADa%20Nacional/notas%20de%20prensa/PREMIOS_MAYORES_DEL_SORTEO_DE_LOTERIA_NACIONAL';
   $x=explode(' ', $fecha);
   $day_w=str_replace('á','A',strtoupper($x[0]));
   $y=explode('/',$x[1])[2];
   $_1=str_replace([$y,'/'], [substr($y,2,4),'_'], $x[1]);
  // dump($day_w.'_'.$_1);
   $d=explode('_',$_1);
   $_2=join('_',[$d[0],(int)$d[1],$d[2]]);
   $_3=join('_',[(int)$d[0],$d[1],$d[2]]);
   $_4=join('_',[(int)$d[0],(int)$d[1],$d[2]]);
   /*dump($_2);
   dump($_3);
   dump($_4);*/
   foreach (['_'.$day_w.'_','_'] as $dw) 
       foreach ([$_1,$_2,$_3,$_4] as $f)
       if (stripos(get_headers($path.$dw.$f)[0],"200 OK")!==false){
//            dump($path.$dw.$f);
        } 
           
       
   
 
}
public function getPathFileWriteLotNac(Juego $g,$res){
	$fecha_reint=parsers::parseElPais();
    foreach ($fecha_reint as $v) {
        parsers::sorteo($v['fecha']);
    }
    die;
    $year=(new \DateTime('@'.strtotime('now')))->format('Y');
    $month=(new \DateTime('@'.strtotime('now')))->format('m');
    $month=(new \DateTime('@'.strtotime('now')))->format('d');
    $pdfFilePath = 'https://www.loteriasyapuestas.es/f/loterias/documentos/Loter%C3%ADa%20Nacional/listas%20de%20premios/SM_LISTAOFICIAL.A'.$year.'.S';
    //.'.S053.pdf';

    /**
     * the last pdf of the year
     */
    //$paths=[];
    //$cc=14;
    // $flag=true;

     $resp=[];
     //$R=[];
	 /*$dd='https://www.loteriasyapuestas.es/f/loterias/documentos/Loter%C3%ADa%20Nacional/notas%20de%20prensa/PREMIOS_MAYORES_DEL__SORTEO_DE_LOTERIA_NACIONAL_DOMINGO_09_05_21.pdf';*/
  $_day_week=['Mon'=>'LUNES','Tue'=>'MARTES','Wed'=>'MIERCOLES',
	'Thu'=>'JUEVES','Fri'=>'VIERNES','Sat'=>'SABADO','Sun'=>'DOMINGO'];
  	
	$_path='https://www.loteriasyapuestas.es/f/loterias/documentos/Loter%C3%ADa%20Nacional/notas%20de%20prensa/PREMIOS_MAYORES_DEL_SORTEO_DE_LOTERIA_NACIONAL_';
	//'https://www.loteriasyapuestas.es/es/resultados/loteria-nacional'
	 foreach(range(1,31)as $_i)
	 {
		// $_url='';
		/*if (stripos(get_headers($p=$pdfFilePath.str_repeat('0', 3 - strlen($_i)) . $_i . '.pdf')[0],"200 OK")!==false){
			$_url=parsers::get_Day_Date($p);
			//dump($_url);die;
		}*/
		 $_dw = $_day_week[(\DateTime::createFromFormat('Y-m-d','2021-07-'.(strlen($_i)==1?'0':'').$_i)->format('D'))];
		 $_dat=join('_',[(strlen($_i)==1?'0':'').$_i,'07','21']);
		 $_url=$_path.$_dw.'_'.$_dat.'.pdf';
		
		if (stripos(get_headers($_url)[0],"200 OK")!==false){
			$_url=parsers::getResult($_url);
		}
		 $_dat1=join('_',[$_i,'07','21']);
		 $_url1=$_path.$_dw.'_'.$_dat1.'.pdf';
		if (stripos(get_headers($_url1)[0],"200 OK")!==false){
			$_url=parsers::getResult($_url1);
		} 
		$_dat=join('_',[(strlen($_i)==1?'0':'').$_i,'7','21']);
		 $_url=$_path.$_dw.'_'.$_dat.'.pdf';
		
		if (stripos(get_headers($_url)[0],"200 OK")!==false){
			$_url=parsers::getResult($_url);
		}
		 $_dat1=join('_',[$_i,'7','21']);
		 $_url1=$_path.$_dw.'_'.$_dat1.'.pdf';
		if (stripos(get_headers($_url1)[0],"200 OK")!==false){
			$_url=parsers::getResult($_url1);
		}
///////////////////////////////////////////////	
         $_dat=join('_',[(strlen($_i)==1?'0':'').$_i,'07','21']);
		 $_url=$_path.$_dat.'.pdf';
		
      if (stripos(get_headers($_url)[0],"200 OK")!==false){
			$_url=parsers::getResult($_url);
		}
		 $_dat1=join('_',[$_i,'07','21']);
		 $_url1=$_path.$_dat1.'.pdf';
		if (stripos(get_headers($_url1)[0],"200 OK")!==false){
			$_url=parsers::getResult($_url1);
		} 
		$_dat=join('_',[(strlen($_i)==1?'0':'').$_i,'7','21']);
		 $_url=$_path.$_dat.'.pdf';
		
		if (stripos(get_headers($_url)[0],"200 OK")!==false){
			$_url=parsers::getResult($_url);
		}
		 $_dat1=join('_',[$_i,'7','21']);
		 $_url1=$_path.$_dat1.'.pdf';
		if (stripos(get_headers($_url1)[0],"200 OK")!==false){
			$_url=parsers::getResult($_url1);
		}	
		 
	 }
	
//$resp[]=(new Result(['juego'=>$g,'combinaciones'=>$numss,'url_lectura'=>$p,'fecha'=>$dd,'reintegros'=>$reint]));
      

    //dump($resp);
    return ($resp);


}
public function getLotNacResults(){
    $meses = ['enero' => '01', 'febrero' => '02', 'marzo' => '03', 'abril' => '04', 'mayo' => '05', 'junio' => '06', 'julio' => '07', 'agosto' => '08', 'septiembre' => '09', 'octubre' => '10', 'noviembre' => '11', 'diciembre' => '12'];

    $url_pdfs=array_reverse(parsers::getPathFileWriteLotNac());
        foreach ($url_pdfs as $path){
            $PDFParser = new Parser();
            //$path=$pdfFilePath.str_repeat('0',3-strlen($c)).$c.'.pdf';
//            dump($path);
            //--$c;
            $pdf = $PDFParser->parseFile($path);
            $text = $pdf->getText();
            $f=fopen('result_nacional.txt','w');
            fwrite($f,$text);
            fclose($f);
            //////////////////////////////////////////////////////////
            $f=fopen('result_nacional.txt','r');
            while ($l=fgets($f)){

                if(strlen(strstr($l, 'Se celebrará el día ')) > 0){
                    //dump($l);
                    $str=substr($l,strpos($l,'Se celebrará el día ')+strlen('Se celebrará el día '),strpos($l,', a las')-3*strlen(', a las')-1);
                    $strHora=explode(':00 horas,',explode(', a las ',$l)[1])[0];
//                    dump($strHora);
                    $r=explode(' ',$str);
                    $nd=(new \DateTime('@'.strtotime('now')));
//                    dump($nd);
                    $y=$nd->format('Y');
                    $m=$nd->format('m');
                    $d=$nd->format('d');
                    $H=$nd->format('H');

                    $mm=$meses[$r[2]];
//             dump('202'+1);
                    if($mm>$m)$y+=1;
                    $fecha=$y.'-'.$meses[$r[2]].'-'.$r[0].' '.$strHora.':00:00';

                    $dd = (\DateTime::createFromFormat('Y-m-d H:i:s',$fecha));
//                    dump($dd);
//                    if($nd>$dd)$flag_pdf=true;

                }


            }

            fclose($f);

        }

}
public function getFileWriteLotNac($res){
   /* $year = (new \DateTime('@' . strtotime('now')))->format('Y');
    $n=intval($res->lastResultNacPosYear())+1;
    $c=str_repeat('0',3-strlen($n)).$n;
    $path = 'https://www.loteriasyapuestas.es/f/loterias/documentos/Loter%C3%ADa%20Nacional/listas%20de%20premios/SM_LISTAOFICIAL.A' . $year . '.S'.$c.'.pdf';
//    dump($path);//die;
    if(!stripos(get_headers($path)[0],"200 OK"))return null;*/
    $year=(new \DateTime('@'.strtotime('now')))->format('Y');

    $pdfFilePath = 'https://www.loteriasyapuestas.es/f/loterias/documentos/Loter%C3%ADa%20Nacional/listas%20de%20premios/SM_LISTAOFICIAL.A'.$year.'.S';
    //.'.S053.pdf';

    /**
     * the last pdf of the year
     */
	 
    $cc=intval($res->lastResultNacPosYear())+1;;

        while (stripos(get_headers($pdfFilePath . str_repeat('0', 3 - strlen($cc)) . $cc . '.pdf')[0],"200 OK"))
                ++$cc;


    /*foreach (range(1,20) as $cc)
        if(curl_init($pdfFilePath.str_repeat('0',3-strlen($cc)).$cc.'.pdf') !== false)
            dump($pdfFilePath.str_repeat('0',3-strlen($cc)).$cc.'.pdf');
    die('end!!');*/

    /**
     * the path pdf more acurately
     */
    --$cc;
 //   dump($pdfFilePath.str_repeat('0',3-strlen($cc)).$cc.'.pdf');die;
    return parsers::moreAcuratelyfileWriteLotNac($pdfFilePath,$cc);



}
public function moreAcuratelyfileWriteLotNac($pdfFilePath,$c){
    $meses = ['enero' => '01', 'febrero' => '02', 'marzo' => '03', 'abril' => '04', 'mayo' => '05', 'junio' => '06', 'julio' => '07', 'agosto' => '08', 'septiembre' => '09', 'octubre' => '10', 'noviembre' => '11', 'diciembre' => '12'];
    //$bote='';
    $fecha_bote=null;
    /**
     * the path pdf more acurately
     */
    $flag_pdf=false;
    while(!$flag_pdf){
        $PDFParser = new Parser();
        $path=$pdfFilePath.str_repeat('0',3-strlen($c)).$c.'.pdf';
//        dump($path);
        --$c;
        $pdf = $PDFParser->parseFile($path);
        $text = $pdf->getText();
        $f=fopen('bote_nacional.txt','w');
        fwrite($f,$text);
        fclose($f);
        //////////////////////////////////////////////////////////
        $f=fopen('bote_nacional.txt','r');
        while ($l=fgets($f)){

            if(strlen(strstr($l, 'Se celebrará el día ')) > 0){
                //dump($l);
                $str=substr($l,strpos($l,'Se celebrará el día ')+strlen('Se celebrará el día '),strpos($l,', a las')-3*strlen(', a las')-1);
                $strHora=explode(':00 horas,',explode(', a las ',$l)[1])[0];
//                dump($strHora);
                $r=explode(' ',$str);
                $nd=(new \DateTime('@'.strtotime('now')));
//                dump($nd);
                $y=$nd->format('Y');
                $m=$nd->format('m');
                $d=$nd->format('d');
                $H=$nd->format('H');

                $mm=$meses[$r[2]];
//             dump('202'+1);
                if($mm>$m)$y+=1;
                $fecha=$y.'-'.$meses[$r[2]].'-'.$r[0].' '.$strHora.':00:00';

                $dd = (\DateTime::createFromFormat('Y-m-d H:i:s',$fecha));
//                 dump($dd);
                if($nd>$dd)$flag_pdf=true;

            }


        }

        fclose($f);



    }
         $c+=2;
    return $pdfFilePath.str_repeat('0',3-strlen($c)).$c.'.pdf';

}
public function resultNac($game,$clas,$em){
	
	
}
public function pathNac($last_url){
	$arr=explode('.',$last_url);
	$c=intval(explode('S',$arr[count($arr)-2])[1])+1;
	//$c=intval($arr[count($arr)-2]);
	//dump($c);die;
	//$c++;
	$c='S'.str_repeat('0',3-strlen($c)).$c;
	$str=str_replace($arr[count($arr)-2],$c,$last_url);
	return $str;
}
public function fileWriteLotNac(Juego $g,$em,$res){
    /*$url=$g->getUrlJuego();
	$url=parsers::pathNac($url);
	dump($url);//die;
	if(stripos(get_headers($url)[0],"200 OK")){
		dump($url);*/
    $pdfFilePath = parsers::getFileWriteLotNac($res);
//    $pdfFilePath = parsers::getFileWriteLotNac();
    //$pdfFilePath = $url;
//dump($pdfFilePath);
    //$pdfFilePath = 'https://www.loteriasyapuestas.es/f/loterias/documentos/Loter%C3%ADa%20Nacional/listas%20de%20premios/SM_LISTAOFICIAL.A2021.S053.pdf';
    $PDFParser = new Parser();
   $pdf = $PDFParser->parseFile($pdfFilePath);
   $text = $pdf->getText();
    $f=fopen('bote_nacional.txt','w');
    fwrite($f,$text);
    fclose($f);
    $meses = ['enero' => '01', 'febrero' => '02', 'marzo' => '03', 'abril' => '04', 'mayo' => '05', 'junio' => '06', 'julio' => '07', 'agosto' => '08', 'septiembre' => '09', 'octubre' => '10', 'noviembre' => '11', 'diciembre' => '12'];
   $bote='';
   $fecha_bote=null;
    $f=fopen('bote_nacional.txt','r');
    while ($l=fgets($f)){
         if(strlen(strstr($l, 'Premio mayor,')) > 0){
//             dump($l);
             $dinero_bote=substr($l,strpos($l,'Premio mayor, ')+strlen('Premio mayor, '),strpos($l,' euros ')-2*strlen(' euros '));
             if(substr_count($dinero_bote,'.')>=2){
                 $fc=explode('.',$dinero_bote);
                 $mid=$fc[1];
                 $mm='';
                 $cont=0;
                 foreach (array_reverse(str_split($mid)) as $m){
                     if($m=='0'){$cont++;continue;}
                     break;
                 }
                 if($cont==3)$dinero_bote=$fc[0].' M';
                 // if($cont==2)$dinero_bote=$fc[0].','.$mid[0].' M';
                 if($cont==1||$cont==2)$dinero_bote=$fc[0].','.substr($mid,0,$cont==2?1:2).' M';
                 if($cont==0)$dinero_bote=$fc[0].','.$mid.' M';

                 // $dinero_bote=str_replace($str, ' M',$dinero_bote);
             }
//             dump($dinero_bote);
             $bote=$dinero_bote;
             break;
         }

         if(strlen(strstr($l, 'Se celebrará el día ')) > 0){
//             dump($l);
             $str=substr($l,strpos($l,'Se celebrará el día ')+strlen('Se celebrará el día '),strpos($l,', a las')-3*strlen(', a las')-1);
            // $strHora=substr($l,strpos($l,', a las ')+strlen(', a las '),strpos($l,':00 horas,')-4*strlen(':00 horas,'));
             $strHora=explode(':00 horas,',explode(', a las ',$l)[1])[0];
//             dump($strHora);
             $r=explode(' ',$str);
             $nd=(new \DateTime('@'.strtotime('now')));
             $y=$nd->format('Y');
             $m=$nd->format('m');
             $d=$nd->format('d');
             $H=$nd->format('H');
            // $H

             $mm=$meses[$r[2]];
//             dump('202'+1);
             if($mm>$m)$y+=1;
             $fecha=$y.'-'.$meses[$r[2]].'-'.$r[0];
             $dd = (\DateTime::createFromFormat('Y-m-d',$fecha));

//             dump($dd);
             $fecha_bote=$dd;
//             dump(strtotime('now'));
//             $date = (new \DateTime('@'.strtotime('now')))->format('Y');
//             dump($date);
//             $r=\DateTime::now();
//             dump($r);
//             $e=intl cal_get_now();
//
//             dump($e);
////             $dd=('now');
//             $dd = (\DateTime::createFromFormat('Y-m-d',$e));
//             dump($dd);


         }


    }

    fclose($f);
    if((new \DateTime('@'.strtotime('now')))<=$fecha_bote){
		//$g->setUrlJuego($url);
        $g->setBote($dinero_bote);
        $g->setFechaBote($fecha_bote);
    }else {
        $g->setBote(null);
        $g->setFechaBote(null);
    }

    $em->persist($g);
    $em->flush();
    //return $g;
	//}
}
public  function fileWriteNacLot($game,$clas){
       //parsers::fileWrite($game,$clas);

        $ff=false;
        $line=[];
        $r=[];
        $k=[];
        $f=fopen($clas.'.xml','r');
        while($l=fgets($f)){
            $line[]=$l;


            if(strlen(strstr($l, '<div class="restocabecerilla">')) > 0)
            {$ff=true;}
            if(strlen(strstr($l, '<div class="repartopremios">')) > 0)
            {$ff=false;$r[]=$k;$k=[];}

            if($ff &&  strlen(strstr($l, '<div class="fecha">')) > 0){
                $h=explode(' ',str_replace(['</div>','<div class="fecha">'],['',''],trim($l)))[1];
                $u=explode('/',$h);
                $k['fecha']=join('-',array($u[2],$u[1],$u[0]));
            }
            if($ff && strlen(strstr($line[count($line)-3], '<h4>Primer premio</h4>')) > 0 && strlen(strstr($l, '<li>')) > 0)
                $k['1er']=str_replace(['<li>','</li>'],[''],trim($l));
            if($ff && strlen(strstr($line[count($line)-3], '<h4>Segundo premio</h4>')) > 0 && strlen(strstr($l, '<li>')) > 0)
                $k['2do']=str_replace(['<li>','</li>'],[''],trim($l));
            if($ff && strlen(strstr($line[count($line)-3], '<h4>Reintegros</h4>')) > 0 && strlen(strstr($l, '<li>')) > 0)
                $k['reint']=join(',',array_slice(explode(' ',str_replace(['<li>','</li>'],[''],trim($l))),1));

        }
        fclose($f);

        $_=[];
        //echo json_encode($r);
        foreach ($r as $ii){
           /* $resul=[];
            $resul['resultados']=$ii['1er'].','.$ii['2do'];
            $resul['reintegros']=$ii['reint'];
            $resul['fecha']= (\DateTime::createFromFormat('Y-m-d', $ii['fecha']));
            $resul['juego']=$game;*/

            $_[]=(new Result(['resultados'=>$ii['1er'].','.$ii['2do'],'reintegros'=>$ii['reint'],'fecha'=>(\DateTime::createFromFormat('Y-m-d', $ii['fecha'])),'juego'=>$game]));
        }
        return $_;
    }


public function getResultNac(Juego $g,$res)
{ 
    dump(__DIR__);
    $ff=file_get_contents('https://servicios.elpais.com/sorteos/loteria-nacional/index.html');
    $f1=fopen('result_nacional_el_pais.txt','w');
    fwrite($f1,$ff);
    fclose($f1);
//    dump($ff);

    //$year = (new \DateTime('@' . strtotime('now')))->format('Y');
    $year = date('Y', time());
    $n=intval($res->lastResultNacPosYear())+1;
	// dump($n);
    $c=str_repeat('0',3-strlen($n)).$n;
    $path = "https://www.loteriasyapuestas.es/f/loterias/documentos/Loter%C3%ADa%20Nacional/listas%20de%20premios/SM_LISTAOFICIAL.A$year.S$c.pdf";
   // dump($path);//die;
    $arrContextOptions=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );

    //$response = file_get_contents("https://maps.co.weber.ut.us/arcgis/rest/services/SDE_composite_locator/GeocodeServer/findAddressCandidates?Street=&SingleLine=3042+N+1050+W&outFields=*&outSR=102100&searchExtent=&f=json", false, stream_context_create($arrContextOptions));


    if(!stripos(get_headers($path,false, stream_context_create($arrContextOptions))[0],"200 OK"))return null;
   // dump('gngcn');
   
   //include ('C:/xampp7.4/htdocs/loteria/web/public/parsers.php');
    //$este=asset('parsers.php');
    $txttt=parsers::get_str_html_pdf($c);
	$array_json=json_encode(parsers::parseNumPrem($txttt));
	//dump($txttt);
	//dump('gngcnfxbfc');


    $PDFParser = new Parser();
    $pdf = $PDFParser->parseFile($path);
    $textt = $pdf->getText();
   // $txttt = $pdf->getText();
    $f=fopen('result_nacional.txt','w');
    fwrite($f,$textt);
    fclose($f);
//    dump($textt);//die;
    $meses = ['enero' => '01', 'febrero' => '02', 'marzo' => '03', 'abril' => '04', 'mayo' => '05', 'junio' => '06', 'julio' => '07', 'agosto' => '08', 'septiembre' => '09', 'octubre' => '10', 'noviembre' => '11', 'diciembre' => '12'];

    $premios=[];
    $precio_premios=[];
    $fecha='';
    $reintegros=[];
    $contR=0;

    $f=fopen('result_nacional.txt','r');
    while ($l=fgets($f)){
        if(strlen(strstr($l, 'excepto el')) > 0){
            $a=substr(explode('excepto el ',$l)[1],0,5);
           // dump($a);
            $premios[]=$a;

        }


        if(strlen(strstr($l, 'Premio de ')) > 0){
            //$a=substr(explode('Premio de ',$l)[1],0,5);
            //dump($l);
            $arr=explode('Premio de ',$l);
            $arr=array_slice($arr,1);
            foreach ($arr as $b)
                $precio_premios[]=explode(' ',$b)[0];
            //$contR++;

        }
        if(strlen(strstr($l, 'web de esta Sociedad.Madrid, ')) > 0){
            $_=explode(' ',explode('web de esta Sociedad.Madrid, ',trim($l))[1]);
            $fecha=join('/',[$_[0],$meses[$_[2]],$_[4]]);

//            dump($fecha);
            /*$arr=explode('Premio de ',$l);
            $arr=array_slice($arr,1);
            foreach ($arr as $b)
                $precio_premios[]=explode(' ',$b)[0];*/
            //$contR++;

        }



    }
    fclose($f);
//    dump(join(',',$premios));

    $premios=(join(',',$premios));
//    dump(join(',',$precio_premios));
    ;
    $precio_premios[]=intval(str_replace('.','',$precio_premios[0]))/100000;
    $precio_premios=(join(';',$precio_premios));
//    dump('yap');die;
    $act_cont=false;
    $cont=0;
    $f=fopen('result_nacional_el_pais.txt','r');
    while ($l=fgets($f)){
        if($act_cont)$cont++;
        if(strlen(strstr($l, $fecha)) > 0){
//            dump($l);//die;
            $act_cont=true;
        }
        if($cont==19){
            //dump($l);die;
            $reintegros=explode(' ',str_replace(['<li> ','</li>'],'',trim($l)));
        }
    }
    fclose($f);
//    dump(join(',',$reintegros));
    $reintegros=join(',',$reintegros);
    $_f=explode('/',$fecha);
    $fecha=join('-',[$_f[2],$_f[1],$_f[0]]);
    $fecha=(\DateTime::createFromFormat('Y-m-d',$fecha));
    $np=new NumeroPremiado(['fecha'=>$fecha,'text'=>$txttt,'array_json'=>$array_json]);
    $r=new Result(['juego'=>$g,'fecha'=>$fecha,'pos_year'=>$n,'combinaciones'=>$premios,'reintegros'=>$reintegros,'categoria'=>'1er Premio,2º Premio,Reintegro','premio'=>$precio_premios]);
    return [$r,$np];
    //dump($r);
    //dump('yap');die;
}
  public function aux($r){
          return str_replace(' ','',trim($r));
          }
          public function aux1($h){
              $arr=[];
              foreach ($h as $r){
                  $x=trim($r);
                  if(strlen($x)>5){
                      $arr[]=substr($x,0,5);
                      $arr[]=substr($x,5,5);
                  }else{
                      $arr[]=$x;
                  }
              }return $arr;
          }
    public function aux2($h1){
        $arr=[];
        foreach ($h1 as $r){

            if(strlen($r)==6 && substr_count($r,'.')==0){
                $arr[]=substr($r,0,3);
                $arr[]=substr($r,3,3);
            }else{
                $arr[]=$r;
            }
        }return $arr;

    }
    public function aux3($a,$b){
    $r=[];
    foreach (range(0,count($a)-1)as $i)
        $r[]=[$a[$i],$b[$i]];
    return $r;
    }
    public function aux4($d){

    $pos=-1;
    $_a='';
    $_b='';
    foreach (range(0,count($d)-1) as $i)
        if(substr_count($d[$i],' ')>0){
            $pos=$i;
            $cc=explode(' ',$d[$i]);
            $_a=$cc[0];
            $_b=$cc[1];
            break;
        }
       $a=array_slice($d,0,$pos+1);
       $a[count($a)-1]=$_a;
       $b=array_slice($d,$pos,count($d)-$pos);
       $b[0]=$_b;
//       dump($a);
//       dump($b);

    }

  public function parserNumPrem($txt){

// initiate
      $pdf = new Pdf('C:/Users/Lilia/Desktop/SM_LISTAOFICIAL.A2021.S078.pdf');
      $html = $pdf->html();
      dump($html);die;

      //dump($txt);
      $f=fopen('result_nacional.txt','w');
      fwrite($f,$txt);
      fclose($f);
      $f=file('result_nacional.txt');
      //dump($f);
      $h=array_slice($f,4,182);
      $h=array_map('App\Controller\parsers::aux',$h);
      $h[0]=substr($h[0],strlen($h[0])-5,5);
      $h[count($h)-1]=(substr($h[count($h)-1],0,5));
      $arr=parsers::aux1($h);

      $h1=array_slice($f,185,182);
      $h1=array_map('App\Controller\parsers::aux',$h1);
      $h1[0]=substr($h1[0],strlen($h1[0])-3,3);
      $h1[count($h1)-1]=explode('.',$h1[count($h1)-1])[0];
      $arr1=parsers::aux2($h1);
      //dump($h);//die;
     // dump($arr1);//die;
      $rr=parsers::aux3($arr,$arr1);
      ////////////////////////////////////////
      $hh=array_slice($f,547,37);
      $d=explode('Terminaciones',trim($hh[0]));
      $hh[0]=$d[count($d)-1];
      $hh[count($hh)-1]=explode('.',str_replace(' ','',trim($hh[count($hh)-1])))[0];
      $hh=array_map('trim',$hh);
      parsers::aux4($hh);
      dump($hh);die;
      return $rr;
     // dump($rr);


      //dump($arr);die;
     /* while ($l=fgets($f)){

      }*/
     // fclose($f);


  }


function get_str_html_pdf($code){
/////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////descarga////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
$ini=time();
//$year=(date('Y', strtotime("now")));
$year=(date('Y', time()));

$fileURL="https://www.loteriasyapuestas.es/f/loterias/documentos/Loter%C3%ADa%20Nacional/listas%20de%20premios/SM_LISTAOFICIAL.A$year.S$code.pdf"; 

 $ch = curl_init($fileURL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);         //follow redirects
	
	//curl_setopt ($ch, CURLOPT_HTTPHEADER, array ( 'Authorization: Bearer '. $token) );
	$response = curl_exec($ch);
	curl_close($ch);
	file_put_contents(
	    "SM_LISTAOFICIAL.A$year.S$code.pdf",
	    $response
	);
/////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////coversion////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/*$firstpage=1;
$lastpage=2;*/

$source_pdf="SM_LISTAOFICIAL.A$year.S$code.pdf";
$new_html_file_name="SM_LISTAOFICIAL.A$year.S$code.html";
$dir=__DIR__;
$output_folder=$dir;

if (!file_exists($output_folder)) { mkdir($output_folder, 0777, true);}
//$a= passthru("pdftohtml -f $firstpage -l $lastpage $source_pdf $output_folder/$new_html_file_name",$b);
$a= passthru("pdftohtml $source_pdf $new_html_file_name",$b);
dump($a);
//    $arrContextOptions=array(
//        "ssl"=>array(
//            "verify_peer"=>false,
//            "verify_peer_name"=>false,
//        ),
//    );


    $str=(file_get_contents("SM_LISTAOFICIAL.A$year.S{$code}s.html"));
dump($str);
//echo(file_get_contents("SM_LISTAOFICIAL.A$year.S{$code}s.html"));
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////limpieza////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
//$dir="$dir";
$dir=str_replace('src\Controller','public',$dir);
dump($dir);
foreach(glob("$dir/*") as $v)
     if(is_file(basename($v)) && abs(filectime($v)-$ini)<50){
		echo(basename($v)." : ".abs(filectime($v)-$ini)." <br/>");
		unlink(basename($v));
	}
	

return($str);

}
public function parseNumPrem($str){
		$f=fopen('result_nacional.txt','w');
        fwrite($f,$str);
        fclose($f);
		$f=file('result_nacional.txt');
		dump($f);//die;
		$ini=0;
		$end=0;
		
		foreach(range(0,count($f)-1) as $i){
		  if(substr_count($f[$i],'&#160;Euros')>0)
		    $ini=$i+1;
		  if(substr_count($f[$i],'<b>Terminaciones</b><br/>')>0)
		   {$end=$i;break;} 
		
		}
		$d=array_slice($f,$ini,$end-$ini);
		//dump($d);die;
		/*$d=array_map(function ($v) {return str_replace(['<b>','</b>'],'',explode('<br/>',$v)[0]);},$d); 
		$d=array_filter($d, function ($a) { return !(substr_count($a,'.....')>0); });*/
		$dd=[];
		foreach(range(0,count($d)-1,3) as $i)
         $dd[str_replace(['<b>','</b>'],'',explode('<br/>',$d[$i])[0])]=str_replace(['<b>','</b>'],'',explode('<br/>',$d[$i+1])[0]);
        
		//dump($dd);die;	

        $ini=0;
		$end=0;
      foreach(range(0,count($f)-1) as $i){
		 
		  if(substr_count($f[$i],'<b>Terminaciones</b><br/>')>0)
		  $ini=$i+1;

	     if(substr_count($f[$i],'<b>ESTE SORTEO PONE EN JUEGO')>0)
		   {$end=$i;break;} 
		
		}	
      $d1=array_slice($f,$ini,$end-$ini);
	  $d1=array_filter($d1,function($n){return !(substr_count($n,'...&#160;<br/>')>0);});
	  $d1=array_map(function($n){return explode('<br/>',$n)[0];},$d1);
	 //  dump($d1);die;
	  $d2=[];
	  foreach($d1 as $s)
	   if(substr_count($s,'.')>1){
		  $a=explode(',',str_replace(str_repeat('.', substr_count($s,'.')),',',$s)); 
		  $d2[]=$a[0];
		  $d2[]=$a[1];
	   }else $d2[]=$s;
	
 	  $d3=[];
	  //dump($d2);die;
	  foreach(range(0,count($d2)-1,2) as $i)
	  $d3[$d2[$i]]=$d2[$i+1];
	 
	 
      $re=$dd+$d3;
	  
	  dump($re);//die;
	  
	  $rr=[];
        foreach ($re as $k=>$v)
            $rr[]=[$k,$v];
        //return $rr;
		
		dump($rr);
	  
	  return $rr;
     // dump($re);die;			
		
		
		
	}
}
  
