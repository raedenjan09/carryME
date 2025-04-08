```php
<?php

namespace App\Services;

use PDF;

class PdfReceiptGenerator
{
    public function generateReceipt($order)
    {
        $pdf = PDF::loadView('pdfs.receipt', [
            'order' => $order,
            'user' => $order->user,
            'items' => $order->items
        ]);

        $path = storage_path('app/receipts/order-' . $order->id . '.pdf');
        $pdf->save($path);

        return $path;
    }
}
```