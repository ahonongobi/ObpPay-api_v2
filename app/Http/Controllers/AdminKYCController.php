<?php

namespace App\Http\Controllers;

use App\Models\Kyc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\FirebaseService;

class AdminKYCController extends Controller
{
    public function index()
    {
        // Dernier KYC par utilisateur
        $latestKycs = Kyc::with('user')
            ->get()
            ->groupBy('user_id')
            ->map(function ($kycs) {
                // take the latest KYC for this user
                return $kycs->sortByDesc('id')->first();
            })
            ->values() // reset keys
            ->map(function ($kyc) {

                // récupérer TOUS les documents KYC de ce user
                $docs = Kyc::where('user_id', $kyc->user_id)->get()->map(function ($item) {
                    return [
                        'type' => match ($item->type) {
                            'id_card' => 'Carte d’identité',
                            'passport' => 'Passeport',
                            'selfie' => 'Selfie',
                            default => 'Document',
                        },
                        'path' => $item->file_path,
                    ];
                });

                // on attache dynamiquement les docs
                $kyc->docs = $docs;

                return $kyc;
            });

        return view('admin.kyc.index', ['kycs' => $latestKycs]);
    }


    public function show($id)
    {
        // On récupère une ligne KYC
        $kyc = Kyc::findOrFail($id);

        // On récupère TOUTES les preuves KYC de CE user
        $allKycDocs = Kyc::where('user_id', $kyc->user_id)->get();

        // Formatage selon ton JS "type + path"
        $docs = $allKycDocs->map(function ($item) {
            return [
                'type' => match ($item->type) {
                    'id_card' => 'Carte d’identité',
                    'passport' => 'Passeport',
                    'selfie' => 'Selfie',
                    default => 'Document'
                },
                'path' => $item->file_path,
            ];
        });

        return response()->json([
            'id' => $kyc->id,
            'status' => $kyc->status,
            'user' => $kyc->user,
            'docs' => $docs,
        ]);
    }



    public function approve($id)
    {
        // 1️ Get the KYC row
        $kyc = Kyc::with('user')->findOrFail($id);

        // 2️ Update all KYC rows for this user
        Kyc::where('user_id', $kyc->user_id)
            ->update(['status' => 'approved']);

        // 3️ Admin log
        admin_log(
            'kyc',
            "KYC #{$kyc->id} approuvé pour l'utilisateur {$kyc->user->name} (ID: {$kyc->user->id})"
        );

        // 4️ Firebase notification
        if ($kyc->user->fcm_token) {
            try {
                $firebase = new FirebaseService();

                $firebase->sendToToken(
                    $kyc->user->fcm_token,
                    "KYC approuvé",
                    "Votre KYC a été approuvé avec succès.",
                    [
                        "screen" => "kyc",
                        "kyc_id" => $kyc->id
                    ]
                );
            } catch (\Exception $e) {
                Log::error("FCM KYC APPROVE ERROR: " . $e->getMessage());
            }
        }

        // 5️ Redirect back with success message
        return back()->with('success', 'KYC approuvé avec succès.');
    }


    public function reject($id)
    {
        // 1️ Get the KYC row
        $kyc = Kyc::with('user')->findOrFail($id);

        // 2️ Update all KYC rows for this user
        Kyc::where('user_id', $kyc->user_id)
            ->update(['status' => 'rejected']);

        // 3️ Admin log
        admin_log(
            'kyc',
            "KYC #{$kyc->id} rejeté pour l'utilisateur {$kyc->user->name} (ID: {$kyc->user->id})"
        );

        // 4️ Firebase notification
        if ($kyc->user->fcm_token) {
            try {
                $firebase = new FirebaseService();

                $firebase->sendToToken(
                    $kyc->user->fcm_token,
                    "KYC rejeté",
                    "Votre KYC a été rejeté. Veuillez soumettre de nouveaux documents.",
                    [
                        "screen" => "kyc",
                        "kyc_id" => $kyc->id
                    ]
                );
            } catch (\Exception $e) {
                Log::error("FCM KYC REJECT ERROR: " . $e->getMessage());
            }
        }

        // 5️ Redirect back with success message
        return back()->with('success', 'KYC rejeté avec succès.');
    }
}
