<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContratController extends Controller
{

    public function showSigned(Request $request, Contrat $contrat): \Illuminate\Contracts\View\View
    {
         if (!is_null($contrat->signature_contractant)) {
            Log::warning('Tentative d\'accès à un contrat déjà validé ou rejeté.', ['contrat_id' => $contrat->id]);
            return view('contrats.already_decided', ['contrat' => $contrat]);
        }
         return view('contrats.show_signed', ['contrat' => $contrat]);
    }


    public function validateContrat(Request $request, Contrat $contrat): \Illuminate\Http\RedirectResponse
    {
        if (!is_null($contrat->signature_contractant)) {
            Log::warning('Tentative de double soumission sur un contrat.', ['contrat_id' => $contrat->id, 'action' => $request->input('action')]);
            return redirect()->route('contrats.validation.success')->with('error', 'Vous avez déjà pris une décision pour ce contrat.');
        }

        $request->validate([
            'signature_text' => 'required|string|max:255',
            'action' => 'required|in:approve,reject',
        ]);

        $action = $request->input('action');
        $signatureText = strtolower(trim($request->input('signature_text')));

        if ($action === 'approve') {
            if ($signatureText !== 'lu et approuve') {
                return redirect()->back()->withInput()->with('error', 'Veuillez écrire "Lu et approuve" pour valider le contrat.');
            }
            $contrat->signature_contractant = true;
            $message = 'Contrat approuvé. Le processus de signature numérique est en cours.';
            Log::info('Contrat approuvé, processus Docusign en attente', ['contrat_id' => $contrat->id]);
            // TODO: Appeler un service ou une job pour gérer l'envoi à Docusign
            // dispatch(new SendToDocusignJob($contrat));
        } else {
            if ($signatureText !== 'rejeter') {
                return redirect()->back()->withInput()->with('error', 'Veuillez écrire "Rejeter" pour annuler le contrat.');
            }
            $contrat->signature_contractant = false;
            $message = 'Contrat rejeté. Votre décision a été enregistrée.';
            Log::info('Contrat rejeté', ['contrat_id' => $contrat->id]);
        }

        $contrat->save();

        return redirect()->route('contrats.validation.success')->with('success', $message);
    }


    public function validationSuccess(): \Illuminate\Contracts\View\View
    {
        return view('contrats.validation_success');
    }
}
