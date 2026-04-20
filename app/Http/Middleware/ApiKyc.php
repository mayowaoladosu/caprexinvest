<?php

namespace App\Http\Middleware;

use App\Models\Kyc;
use App\Models\UserKyc;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiKyc
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $kycForms = Kyc::where('status', 1)->get();

        // Fetch user's latest KYC submissions for those types
        $userKycs = UserKyc::where('user_id', $user->id)
            ->whereIn('kyc_id', $kycForms->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('kyc_id') // Keeps only the latest per kyc_id
            ->keyBy('kyc_id');

        foreach ($kycForms as $kycForm) {
            $userKyc = $userKycs->get($kycForm->id);

            if (!$userKyc) {
                return response()->json([
                    'status' => 'error',
                    'data' =>'You have not submitted the ' . $kycForm->name . ' form.'
                ]);
            }

            if ($userKyc->status == 0) {
                return response()->json(['status' => 'error', 'data' => "Your {$userKyc->kyc_type} form is pending."]);
            }

            if ($userKyc->status == 2) {
                return response()->json(['status' => 'error', 'data' => "Your {$userKyc->kyc_type} form has been rejected."]);
            }
        }
        return $next($request);
    }
}
