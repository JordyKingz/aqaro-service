<?php

namespace App\Services;

use App\Models\Enums\SubscriptionType;
use App\Repositories\SubscriptionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    protected SubscriptionRepository $subscriptionRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function subscribe($email, $type): JsonResponse
    {
        try {
            $subscription = $this->subscriptionRepository->getByEmail($email);
            // cast $type to SubscriptionType
            if (($subscription && $subscription->type === SubscriptionType::from($type))) {
                return response()->json([
                    'error' => 'Email already subscribed',
                    'message' => 'Email already subscribed',
                    'status' => 400
                ], 400);
            } else if (($subscription && $subscription->type === SubscriptionType::Both)) {
                return response()->json([
                    'error' => 'Email already subscribed',
                    'message' => 'Email already subscribed',
                    'status' => 400
                ], 400);
            } else if ($subscription) {
                $subscription = $this->subscriptionRepository->updateType($subscription, SubscriptionType::Both);

            } else {
                $subscription = $this->subscriptionRepository->store($email, $type);
            }

            return response()->json($subscription, 200);
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
}
