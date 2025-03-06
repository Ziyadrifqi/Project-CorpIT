<?php

namespace App\Controllers;

use App\Models\Autosigned\AplikasiModel;
use App\Models\Autosigned\MappingModel;
use App\Models\Autosigned\PjStatusModel;
use DateTime;

class PjAssignmentController extends BaseController
{
    protected $aplikasiModel;
    protected $mappingModel;
    protected $pjStatusModel;

    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->aplikasiModel = new AplikasiModel();
        $this->mappingModel = new MappingModel();
        $this->pjStatusModel = new PjStatusModel();
    }

    public function index()
    {
        // Get all PJ assignments from the model
        $all_pj_assignments = $this->pjStatusModel->getAllPjAssignments();

        // Group PJ assignments by application name
        $grouped_pj_assignments = [];
        foreach ($all_pj_assignments as $pj) {
            $app_name = $pj['nama_aplikasi'];

            if (!isset($grouped_pj_assignments[$app_name])) {
                $grouped_pj_assignments[$app_name] = [];
            }

            $grouped_pj_assignments[$app_name][] = $pj;
        }

        $data = [
            'title' => 'PJ Assignment System',
            'aplikasi' => $this->aplikasiModel->findAll(),
            'selected_pj' => session()->getFlashdata('selected_pj'),
            'all_pj_assignments' => $all_pj_assignments,
            'grouped_pj_assignments' => $grouped_pj_assignments
        ];

        return view('pj_assignment/index', $data);
    }

    public function getUsers($aplikasi_id)
    {
        // Get all users mapped to this application
        $mappings = $this->mappingModel->where('aplikasi_id', $aplikasi_id)->findAll();

        $users = [];
        foreach ($mappings as $mapping) {
            $status = $this->pjStatusModel->where('mapping_id', $mapping['id'])->first();

            // If status doesn't exist, create it with default 0
            if (!$status) {
                $this->pjStatusModel->insert([
                    'mapping_id' => $mapping['id'],
                    'status' => 0
                ]);
                $status = $this->pjStatusModel->where('mapping_id', $mapping['id'])->first();
            }

            $users[] = [
                'id' => $mapping['id'],
                'nama_user' => $mapping['nama_user'],
                'status' => $status['status']
            ];
        }

        return $this->response->setJSON(['users' => $users]);
    }

    public function getPj($aplikasi_id)
    {
        if (!$aplikasi_id) {
            session()->setFlashdata('error', 'Select the application first');
            return redirect()->to('/pj-assignment');
        }

        // Get all users mapped to this application
        $mappings = $this->mappingModel->where('aplikasi_id', $aplikasi_id)->findAll();

        if (empty($mappings)) {
            session()->setFlashdata('error', 'There are no users associated with this application');
            return redirect()->to('/pj-assignment');
        }

        // Check if all users for this application have status 1
        $allAssigned = true;
        $availableMapping = null;

        foreach ($mappings as $mapping) {
            $status = $this->pjStatusModel->where('mapping_id', $mapping['id'])->first();

            // If status doesn't exist, create it with default 0
            if (!$status) {
                $this->pjStatusModel->insert([
                    'mapping_id' => $mapping['id'],
                    'status' => 0
                ]);
                $status = $this->pjStatusModel->where('mapping_id', $mapping['id'])->first();
            }

            // If we find a user with status 0, they're next in round-robin
            if ($status['status'] == 0) {
                $allAssigned = false;
                $availableMapping = $mapping;
                break;
            }
        }

        // If all users have status 1, reset all to 0 and select the first one
        if ($allAssigned) {
            foreach ($mappings as $mapping) {
                $this->pjStatusModel->where('mapping_id', $mapping['id'])->set(['status' => 0])->update();
            }
            $availableMapping = $mappings[0];
        }

        // Update the selected user's status to 1 with current timestamp
        $currentTime = date('Y-m-d H:i:s');
        $this->pjStatusModel->where('mapping_id', $availableMapping['id'])->set([
            'status' => 1,
            'created_at' => $currentTime // Store assignment time
        ])->update();

        // Get the application name
        $aplikasi = $this->aplikasiModel->find($aplikasi_id);

        // Set the result in flash data
        session()->setFlashdata('success', 'New PJ successfully determined');
        session()->setFlashdata('selected_pj', [
            'nama_user' => $availableMapping['nama_user'],
            'nama_aplikasi' => $aplikasi['nama_aplikasi'],
            'created_at' => $currentTime  // Include the timestamp
        ]);

        return redirect()->to('/pj-assignment');
    }
}
