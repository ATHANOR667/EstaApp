<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Services\DocuSignService;

class ContratController extends Controller
{

   public function downloadPdf(Contrat $contrat): \Symfony\Component\HttpFoundation\StreamedResponse
   {
       if ($contrat->status === 'signed')
       {
           $docuSignService = new DocuSignService();

           return $docuSignService->downloadSignedDocument($contrat);
       }else{
           return $contrat->download() ;
       }
   }

}
