<?php
namespace App\_;
class _LN_{

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
			$f=explode('</div>',explode('<div class="fecha">',$_f[$_i])[1])[0];
			$fecha=join(array_reverse(explode('/',explode(' ',$fecha)[1])));
			$r=[];
			foreach(str_split($_f[$_i+19])as $n)
			   if(is_numeric($n))
				   $r[]=$n;
			$fecha_reint[]=[$fecha=>$r];   
		}
		
	}//dump($fecha_reint);dump($_f);die;
	return $fecha_reint;
	
}
public function getPathFileWriteLotNac(Juego $g,$res){
	$fecha_reint=parsers::parseElPais();
    /*foreach ($fecha_reint as $v) {
        parsers::sorteo($v['fecha']);
    }
    die;*/
    $year=(new \DateTime('@'.strtotime('now')))->format('Y');
  
    $pdfFilePath = 'https://www.loteriasyapuestas.es/f/loterias/documentos/Loter%C3%ADa%20Nacional/listas%20de%20premios/SM_LISTAOFICIAL.A'.$year.'.S';
    

    
  	
	
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

}
