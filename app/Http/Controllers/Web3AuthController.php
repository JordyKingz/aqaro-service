<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Web3\Web3;
use Elliptic\EC;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use kornrunner\Keccak;

class Web3AuthController extends Controller
{
    public function signature(Request $request, string $wallet): string
    {
        // Generate some random nonce
        $nonce = Str::random(12);

        // create user if not exists
        $wallet = User::firstOrCreate([
            'eth_address' => $wallet
        ]);

        $wallet->nonce = $nonce;
        $wallet->save();

        // Create message with nonce
        return $this->_getSignatureMessage($nonce);
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'signature' => 'required|string',
            'address' => 'required|string'
        ]);

        $wallet = User::where('eth_address', $request->address)->firstOrFail();

        if (!$wallet) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $verifyResult = Web3::verifySignature(
            $this->_getSignatureMessage($wallet->nonce),
            $request->signature,
            $request->address,
        );

        if (!$verifyResult) {
            return response()->json([
                'message' => 'Invalid signature',
                'nonce' => $this->_getSignatureMessage(session()->get('metamask-nonce'))
            ], 401);
        }

        $token = $wallet->createToken('web3-auth', ['can-refresh', 'is-web3-auth'])->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'message' => 'Successfully authenticated',
        ], 200);
    }

    private function _getSignatureMessage($code)
    {
        return __("I have read and accept the terms and conditions.\nPlease sign me in.\n\nSecurity code (you can ignore this): :nonce", [
            'nonce' => $code
        ]);
    }
}
