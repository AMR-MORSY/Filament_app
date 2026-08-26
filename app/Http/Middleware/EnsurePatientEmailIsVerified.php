<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePatientEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $patient= $request->user('patient');//////guard patient
        
        if(!$patient ||($patient instanceof MustVerifyEmail && !$patient->hasVerifiedEmail())){
            return redirect()->route('patient.verification.notice');
        }
        return $next($request);
    }
}
