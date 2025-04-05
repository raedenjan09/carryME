<?php

namespace App\Imports;

use App\Models\Bag;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BagsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Bag([
            'name' => $row['name'],
            'description' => $row['description'],
            'price' => $row['price'],
        ]);
    }
}