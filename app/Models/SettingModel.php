<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['key', 'value', 'type', 'setting_group', 'label'];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    public function getGrouped(): array
    {
        $all = $this->orderBy('setting_group', 'ASC')->findAll();
        $grouped = [];
        foreach ($all as $s) {
            $group = $s['setting_group'] ?? 'general';
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][] = $s;
        }
        return $grouped;
    }

    public function getValue(string $key): ?string
    {
        $row = $this->where('key', $key)->first();
        return $row ? $row['value'] : null;
    }
}
