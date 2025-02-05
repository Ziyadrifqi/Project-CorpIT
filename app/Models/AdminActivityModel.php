<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminActivityModel extends Model
{
    protected $table = 'admin_activities';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'task',
        'location',
        'start_time',
        'end_time',
        'activity_date',
        'description',
        'nik',
        'pbr_tugas',
        'no_tiket',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Get activities for specific admin
    public function getAdminActivities($userId, $startDate = null, $endDate = null)
    {
        $builder = $this->db->table('admin_activities a')
            ->select('a.*, u.username')
            ->join('users u', 'u.id = a.user_id')
            ->join('auth_groups_users agu', 'agu.user_id = u.id')
            ->where('agu.group_id', 1)
            ->where('a.user_id', $userId);

        if ($startDate && $endDate) {
            $builder->where('a.activity_date >=', $startDate)
                ->where('a.activity_date <=', $endDate);
        }

        return $builder->orderBy('a.activity_date', 'ASC')
            ->orderBy('a.start_time', 'ASC')
            ->get()
            ->getResultArray();
    }

    // Get all admin activities (for superadmin)
    public function getAllAdminActivities($startDate = null, $endDate = null, $selectedUser = null)
    {
        $builder = $this->db->table('admin_activities a')
            ->select('a.*, u.username')
            ->join('users u', 'u.id = a.user_id')
            ->join('auth_groups_users agu', 'agu.user_id = u.id')
            ->where('agu.group_id', 1);

        // Apply date range filter if provided
        if ($startDate && $endDate) {
            $builder->where('a.activity_date >=', $startDate)
                ->where('a.activity_date <=', $endDate);
        }

        // Apply user filter if provided and not 'all'
        if ($selectedUser && $selectedUser !== 'all') {
            if (is_array($selectedUser)) {
                $builder->whereIn('a.user_id', $selectedUser);
            } else {
                $builder->where('a.user_id', $selectedUser);
            }
        }

        return $builder->orderBy('a.activity_date', 'DESC')
            ->orderBy('a.start_time', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function bulkInsertActivities($activities)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);

        $inserted = 0;
        $skipped = 0;

        $db->transStart();

        foreach ($activities as $activity) {
            try {
                $result = $builder->insert($activity);
                if ($result) {
                    $inserted++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                log_message('error', 'Bulk insert error: ' . $e->getMessage());
                $skipped++;
            }
        }

        $db->transComplete();

        return [
            'success' => $db->transStatus(),
            'inserted' => $inserted,
            'skipped' => $skipped
        ];
    }

    public function storeActivity($data)
    {
        return $this->insert($data);
    }

    public function updateActivity($id, $data)
    {
        return $this->update($id, $data);
    }

    public function deleteActivity($id)
    {
        return $this->delete($id);
    }

    public function getActivityById($id)
    {
        return $this->find($id);
    }
}
