<?php
namespace App\Controller;
use App\Smalot\PdfParser\Parser;
class Pdf{
	
     public $_text;
	 
	  public function text($_url){
	 $_PDFParser = new Parser();
     $_pdf = $_PDFParser->parseFile($_url);
     $_text=$_pdf->getText();
	 
	 $_PDFParser = null;
	 $_pdf=null;
	 return $_text;
	  }
}