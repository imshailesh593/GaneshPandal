<?php

namespace App\Http\Controllers;

use App\Models\Family;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('member')->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function verifyToken(Request $request, FirebaseAuth $firebaseAuth)
    {
        $request->validate([
            'id_token' => ['required', 'string'],
        ]);

        try {
            $verifiedToken = $firebaseAuth->verifyIdToken($request->input('id_token'));
        } catch (FailedToVerifyToken $e) {
            throw ValidationException::withMessages([
                'id_token' => 'We could not verify that OTP session. Please try again.',
            ]);
        }

        $firebaseUid = $verifiedToken->claims()->get('sub');
        $phoneNumber = $verifiedToken->claims()->get('phone_number');

        if (! $phoneNumber) {
            throw ValidationException::withMessages([
                'id_token' => 'No phone number was found on the verified token.',
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
            'family_name' => ['required', 'string', 'max:255'],
        ]);

        $family = Family::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($validated['family_name']))])
            ->first();

        if (! $family) {
            $family = Family::create(['name' => trim($validated['family_name'])]);
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
