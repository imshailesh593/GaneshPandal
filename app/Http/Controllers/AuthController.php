<?php

namespace App\Http\Controllers;

use App\Models\Family;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Kreait\Firebase\JWT\Error\IdTokenVerificationFailed;
use Kreait\Firebase\JWT\IdTokenVerifier;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('member')->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function verifyToken(Request $request, IdTokenVerifier $idTokenVerifier)
    {
        $request->validate([
            'id_token' => ['required', 'string'],
        ]);

        try {
            $verifiedToken = $idTokenVerifier->verifyIdToken($request->input('id_token'));
        } catch (IdTokenVerificationFailed $e) {
            throw ValidationException::withMessages([
                'id_token' => 'OTP सेशन व्हेरिफाय होऊ शकलं नाही. परत ट्राय करा.',
            ]);
        }

        $payload = $verifiedToken->payload();
        $firebaseUid = $payload['sub'] ?? null;
        $phoneNumber = $payload['phone_number'] ?? null;

        if (! $phoneNumber) {
            throw ValidationException::withMessages([
                'id_token' => 'फोन नंबर सापडला नाही. परत ट्राय करा.',
            ]);
        }

        $member = Member::where('mobile', $phoneNumber)
            ->orWhere('firebase_uid', $firebaseUid)
            ->first();

        if ($member) {
            if ($member->firebase_uid !== $firebaseUid) {
                $member->update(['firebase_uid' => $firebaseUid]);
            }

            Auth::guard('member')->login($member, remember: true);
            $request->session()->regenerate();

            return response()->json(['redirect' => route('dashboard')]);
        }

        $request->session()->put('pending_registration', [
            'mobile' => $phoneNumber,
            'firebase_uid' => $firebaseUid,
        ]);

        return response()->json(['redirect' => route('auth.complete-profile')]);
    }

    public function showCompleteProfile(Request $request)
    {
        if (! $request->session()->has('pending_registration')) {
            return redirect()->route('login');
        }

        return view('auth.complete-profile', [
            'mobile' => $request->session()->get('pending_registration')['mobile'],
        ]);
    }

    public function completeProfile(Request $request)
    {
        $pending = $request->session()->get('pending_registration');

        if (! $pending) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'plot_number' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'तुमचं नाव टाका.',
            'plot_number.required' => 'प्लॉट नंबर टाका.',
        ]);

        $family = Family::query()
            ->whereRaw('LOWER(plot_number) = ?', [mb_strtolower(trim($validated['plot_number']))])
            ->first();

        if (! $family) {
            $family = Family::create(['plot_number' => trim($validated['plot_number'])]);
        }

        $member = Member::create([
            'name' => $validated['name'],
            'mobile' => $pending['mobile'],
            'firebase_uid' => $pending['firebase_uid'],
            'family_id' => $family->id,
        ]);

        $request->session()->forget('pending_registration');

        Auth::guard('member')->login($member, remember: true);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('member')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
