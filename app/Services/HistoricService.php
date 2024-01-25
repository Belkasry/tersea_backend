<?php

namespace App\Services;

use App\Models\Historic;

class HistoricService
{
    /**
     * @param string $type
     * @param string $realisedAt
     * @param int $admin_id
     * @param int $employee_id
     * @param int $company_id
     * @param string|null $description
     * @return Historic
     */
    public function saveHistoric($type, $realisedAt, $admin_id, $employee_id, $company_id, $description = null)
    {
        $data = [
            'type' => $type,
            'realised_at' => $realisedAt,
            'admin_id' => $admin_id,
            'employee_id' => $employee_id,
            'company_id' => $company_id,
            'description' => $description
        ];

        $historic = Historic::create($data);

        return $historic;
    }
}
