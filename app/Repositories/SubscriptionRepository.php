<?php

namespace App\Repositories;

use App\Models\Subscription;

class SubscriptionRepository
{
    public function store($email, $type): Subscription
    {
        return Subscription::create([
            'email' => $email,
            'type' => $type
        ]);
    }

    public function getByEmail($email): Subscription | null
    {
        return Subscription::where('email', $email)->first();
    }

    public function updateType($model, $type): Subscription
    {
        $model->type = $type;
        $model->save();
        return $model;
    }
}
