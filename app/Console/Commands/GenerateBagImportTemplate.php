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

        // Add sample data
        $sheet->setCellValue('A2', 'Sample Bag');
        $sheet->setCellValue('B2', 'This is a sample description');
        $sheet->setCellValue('C2', '999.99');

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