<?php

namespace App\Imports;

use App\Models\Bag;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class BagsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Bag([
            'name' => $row['name'],
            'slug' => Str::slug($row['name']),
            'description' => $row['description'],
            'price' => $row['price'],
            'stock' => $row['stock'] ?? 0,
            'category_id' => $row['category_id'] ?? null,
        ]);
    }
}