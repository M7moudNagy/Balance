<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class StartCall implements ShouldBroadcast
{
    public $callerId;
    public $receiverId;

    public function __construct($callerId, $receiverId)
    {
        $this->callerId = $callerId;
        $this->receiverId = $receiverId;
    }

    public function broadcastOn()
    {
        return new Channel('chat.' . $this->receiverId);
    }

    public function broadcastAs()
    {
        return 'call.started';
    }
}
