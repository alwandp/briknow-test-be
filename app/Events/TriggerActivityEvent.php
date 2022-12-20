<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TriggerActivityEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $activity_id;
    public $personal_number;

    public function __construct($activity_id, $personal_number)
    {
        $this->activity_id          = $activity_id;
        $this->personal_number      = $personal_number;
    }
}
