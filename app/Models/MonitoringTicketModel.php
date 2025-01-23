<?php

namespace App\Models;

use CodeIgniter\Model;

class MonitoringTicketModel extends Model
{
    protected $table            = 'monitoring_tickets';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['user_id', 'ticket_number', 'subject', 'status_ticket', 'status_approval', 'resolution', 'conversation'];
    protected $useTimestamps    = true;

    // Updated method to get ticket with technician name
    public function searchTicketByNumber($ticketNumber)
    {
        return $this->select('monitoring_tickets.*, users.fullname as technician_name')
            ->join('users', 'users.id = monitoring_tickets.user_id', 'left')
            ->where('monitoring_tickets.ticket_number', $ticketNumber)
            ->first();
    }

    // Other methods remain the same
    public function createTicket($data)
    {
        return $this->save($data);
    }

    public function getTicketHistory($userId)
    {
        return $this->where('user_id', $userId)
            ->findAll();
    }
}
