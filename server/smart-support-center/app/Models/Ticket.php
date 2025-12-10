<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'title',
        'description',
        'priority',
        'category',
    ];
    // This fuction to get the customer who created this ticket 
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    // Get the agent assigned to this ticket
    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    // A ticket has many messages
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}

