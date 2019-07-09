<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    //
    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id', 'service_id', 'subscription_date', 'unsubscription_date', 'is_subscribed'
    ];


}
