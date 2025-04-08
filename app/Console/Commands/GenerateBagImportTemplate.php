<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenerateBagImportTemplate extends Command
{
    protected $signature = 'bags:template';
    protected $description = 'Generate bag import template';

    public function handle()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'name');
        $sheet->setCellValue('B1', 'description');
        $sheet->setCellValue('C1', 'price');
        $sheet->setCellValue('D1', 'stock');
        $sheet->setCellValue('E1', 'category_id');

        // Create storage directory if it doesn't exist
        if (!file_exists(public_path('templates'))) {
            mkdir(public_path('templates'), 0755, true);
        }

        // Save file
        $writer = new Xlsx($spreadsheet);
        $writer->save(public_path('templates/bags_import_template.xlsx'));

        $this->info('Template generated successfully at public/templates/bags_import_template.xlsx');
    }
}