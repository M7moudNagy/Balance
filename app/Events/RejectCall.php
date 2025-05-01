<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RejectCall implements ShouldBroadcast
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
        return new Channel('chat.' . $this->callerId);
    }

    public function broadcastAs()
    {
        return 'call.rejected';
    }
}
