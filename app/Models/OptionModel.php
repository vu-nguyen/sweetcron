<?php

namespace App\Models;

use CodeIgniter\Model;

class OptionModel extends Model
{
    protected $table = 'options';
    protected $primaryKey = 'option_id';
    protected $allowedFields = ['option_name', 'option_value', 'autoload'];
    protected $useTimestamps = false;

    public function value(string $name, ?string $default = null): ?string
    {
        $row = $this->where('option_name', $name)->first();
        return $row['option_value'] ?? $default;
    }

    public function setValue(string $name, string $value): void
    {
        $existing = $this->where('option_name', $name)->first();
        if ($existing) {
            $this->update($existing['option_id'], ['option_value' => $value]);
        } else {
            $this->insert(['option_name' => $name, 'option_value' => $value, 'autoload' => 'yes']);
        }
    }
}
