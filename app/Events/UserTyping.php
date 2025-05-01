<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserTyping implements ShouldBroadcast
{
use InteractsWithSockets, SerializesModels;

public $senderId;
public $receiverId;
public $senderType;
public $receiverType;

public function __construct($senderId, $receiverId, $senderType, $receiverType)
{
$this->senderId = $senderId;
$this->receiverId = $receiverId;
$this->senderType = $senderType;
$this->receiverType = $receiverType;
}

public function broadcastOn()
{
return new Channel('chat.' . $this->receiverId);
}

public function broadcastAs()
{
return 'typing..';
}
}
