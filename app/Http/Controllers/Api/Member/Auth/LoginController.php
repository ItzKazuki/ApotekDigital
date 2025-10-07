<?php

namespace App\Http\Controllers\Api\Member\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\Whatsapp\WhatsappNotificationInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{
    protected $whatsAppGateway;

    public function __construct(WhatsappNotificationInterface $whatsAppGateway)
    {
        $this->whatsAppGateway = $whatsAppGateway;
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|exists:members,phone'
        ]);

        $member = Member::where('phone', $request->phone)->first();

        $otpKey = "otp_" . $member->id;
        $otpAttemptsKey = "otp_attempts_" . $member->id;

        $attemptsOtp = Cache::get($otpAttemptsKey, 0);

        if ($attemptsOtp >= 3) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Too many OTP requests. Please try again later.'
            ], 429);
        }

        // generate 6 code otp and expired
        $otp = rand(100000, 999999);

        Cache::put($otpKey, $otp, now()->addMinutes(10));
        Cache::put($otpAttemptsKey, $attemptsOtp + 1, now()->addMinutes(10));

        $message = "Your OTP code is: $otp. It will expire in 10 minutes.";

        $response = $this->whatsAppGateway->sendWhatsAppMessage($member->phone, $message);

        if (!$response['status'] || (isset($response['data']['status']) && !$response['data']['status'])) {
            $errorReason = $response['data']['reason'] ?? 'Unknown error occurred';
            return response()->json(['message' => 'Error', 'error' => $errorReason], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'OTP send successfully'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|exists:members,phone',
            'otp' => 'required|digits:6'
        ]);

        $member = Member::where('phone', $request->phone)->first();
        $otp = (int) $request->otp;

        $otpKey = "otp_" . $member->id;
        $otpAttemptsKey = "otp_attempts_" . $member->id;

        $cachedOtp = Cache::get($otpKey);

        if (!$cachedOtp || $cachedOtp !== $otp) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid or expired OTP token.'
            ], 401);
        }

        Cache::forget($otpKey);
        Cache::forget($otpAttemptsKey);

        $token = $member->createToken('member-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login Successfull',
            'token' => $token
        ]);
    }
}
