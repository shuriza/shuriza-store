<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date|after_or_equal:date_from',
        ]);

        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->input('date_to', now()->toDateString());

        // Revenue stats
        $revenue = Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->selectRaw('
                COUNT(*) as total_orders,
                SUM(total) as total_revenue,
                AVG(total) as avg_order
            ')->first();

        // Daily revenue for chart
        $dailyRevenue = Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->selectRaw("DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top products
        $topProducts = Product::withCount(['orderItems as sold' => function ($q) use ($dateFrom, $dateTo) {
                $q->whereHas('order', function ($oq) use ($dateFrom, $dateTo) {
                    $oq->where('status', '!=', 'cancelled')
                        ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59']);
                });
            }])
            ->withSum(['orderItems as revenue' => function ($q) use ($dateFrom, $dateTo) {
                $q->whereHas('order', function ($oq) use ($dateFrom, $dateTo) {
                    $oq->where('status', '!=', 'cancelled')
                        ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59']);
                });
            }], 'subtotal')
            ->orderByDesc('sold')
            ->limit(10)
            ->get()
            ->filter(fn ($p) => $p->sold > 0)
            ->values();

        // Status breakdown
        $statusBreakdown = Order::whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->selectRaw('status, COUNT(*) as count, SUM(total) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return view('admin.reports-index', compact(
            'dateFrom', 'dateTo', 'revenue', 'dailyRevenue', 'topProducts', 'statusBreakdown'
        ));
    }

    public function export(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date|after_or_equal:date_from',
        ]);

        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->input('date_to', now()->toDateString());

        // Limit date range to prevent memory exhaustion (max 1 year)
        $fromDate = \Carbon\Carbon::parse($dateFrom);
        $toDate = \Carbon\Carbon::parse($dateTo);
        if ($fromDate->diffInDays($toDate) > 365) {
            return back()->with('error', 'Rentang tanggal maksimal 1 tahun.');
        }

        $orders = Order::with('items')
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->orderBy('created_at')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Penjualan');

        // ── Styles ──
        $headerFill = [
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '6C63FF']],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ];
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '6C63FF']],
        ];
        $subtitleStyle = [
            'font' => ['size' => 10, 'color' => ['rgb' => '666666']],
        ];
        $borderAll = [
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'D0D0D0']]],
        ];
        $totalRowStyle = [
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0EFFF']],
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '6C63FF']],
        ];
        $currencyFormat = '#,##0';
        $evenRowFill = [
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9F9FB']],
        ];

        // ── Title rows ──
        $sheet->mergeCells('A1:N1');
        $sheet->setCellValue('A1', setting('store_name', 'Shuriza Store') . ' — Laporan Penjualan');
        $sheet->getStyle('A1')->applyFromArray($titleStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->mergeCells('A2:N2');
        $sheet->setCellValue('A2', 'Periode: ' . $dateFrom . '  s/d  ' . $dateTo . '   |   Diexport: ' . now()->format('d/m/Y H:i'));
        $sheet->getStyle('A2')->applyFromArray($subtitleStyle);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // ── Column headers (row 4) ──
        $columns = ['A' => 'No', 'B' => 'No Order', 'C' => 'Tanggal', 'D' => 'Customer', 'E' => 'Telepon', 'F' => 'Email', 'G' => 'Produk', 'H' => 'Qty', 'I' => 'Subtotal (Rp)', 'J' => 'Diskon (Rp)', 'K' => 'Kupon', 'L' => 'Total (Rp)', 'M' => 'Pembayaran', 'N' => 'Status'];

        $row = 4;
        foreach ($columns as $col => $label) {
            $sheet->setCellValue($col . $row, $label);
        }
        $sheet->getStyle('A4:N4')->applyFromArray($headerFill);
        $sheet->getRowDimension(4)->setRowHeight(28);

        // ── Data rows ──
        $row = 5;
        $no = 1;
        $grandSubtotal = 0;
        $grandDiscount = 0;
        $grandTotal = 0;
        $grandQty = 0;

        // Status color map
        $statusColors = [
            'pending'    => 'F59E0B',
            'processing' => '3B82F6',
            'completed'  => '10B981',
            'cancelled'  => 'EF4444',
        ];

        foreach ($orders as $order) {
            $products = $order->items->map(fn($i) => $i->product_name . ' (x' . $i->quantity . ')')->join("\n");
            $subtotal = $order->items->sum('subtotal');
            $discount = $order->discount_amount ?? 0;
            $qty = $order->items->sum('quantity');

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $order->order_number);
            $sheet->setCellValue("C{$row}", $order->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue("D{$row}", $order->name);
            $sheet->setCellValueExplicit("E{$row}", $order->phone, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("F{$row}", $order->email ?? '-');
            $sheet->setCellValue("G{$row}", $products);
            $sheet->setCellValue("H{$row}", $qty);
            $sheet->setCellValue("I{$row}", $subtotal);
            $sheet->setCellValue("J{$row}", $discount);
            $sheet->setCellValue("K{$row}", $order->coupon_code ?? '-');
            $sheet->setCellValue("L{$row}", $order->total);
            $sheet->setCellValue("M{$row}", $order->payment_method ? ucfirst($order->payment_method) : 'Manual/WA');
            $sheet->setCellValue("N{$row}", $order->status_label);

            // Number format for currency columns
            $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode($currencyFormat);
            $sheet->getStyle("J{$row}")->getNumberFormat()->setFormatCode($currencyFormat);
            $sheet->getStyle("L{$row}")->getNumberFormat()->setFormatCode($currencyFormat);

            // Wrap text for product names
            $sheet->getStyle("G{$row}")->getAlignment()->setWrapText(true);

            // Center alignment for some cols
            foreach (['A', 'H', 'N'] as $c) {
                $sheet->getStyle("{$c}{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }

            // Status badge color
            $statusColor = $statusColors[$order->status] ?? '999999';
            $sheet->getStyle("N{$row}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => $statusColor]],
            ]);

            // Zebra striping (even rows)
            if ($no % 2 === 0) {
                $sheet->getStyle("A{$row}:N{$row}")->applyFromArray($evenRowFill);
            }

            // Vertical center
            $sheet->getStyle("A{$row}:N{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $grandSubtotal += $subtotal;
            $grandDiscount += $discount;
            $grandTotal += $order->total;
            $grandQty += $qty;

            $row++;
        }

        // ── Total row ──
        $totalRow = $row + 1;
        $sheet->mergeCells("A{$totalRow}:G{$totalRow}");
        $sheet->setCellValue("A{$totalRow}", 'GRAND TOTAL (' . $orders->count() . ' pesanan)');
        $sheet->setCellValue("H{$totalRow}", $grandQty);
        $sheet->setCellValue("I{$totalRow}", $grandSubtotal);
        $sheet->setCellValue("J{$totalRow}", $grandDiscount);
        $sheet->setCellValue("L{$totalRow}", $grandTotal);

        $sheet->getStyle("I{$totalRow}")->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle("J{$totalRow}")->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle("L{$totalRow}")->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle("A{$totalRow}:N{$totalRow}")->applyFromArray($totalRowStyle);
        $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$totalRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension($totalRow)->setRowHeight(30);
        $sheet->getStyle("A{$totalRow}:N{$totalRow}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // ── Borders for all data ──
        $lastDataRow = $row - 1;
        if ($lastDataRow >= 5) {
            $sheet->getStyle("A4:N{$lastDataRow}")->applyFromArray($borderAll);
        }
        $sheet->getStyle("A{$totalRow}:N{$totalRow}")->applyFromArray($borderAll);

        // ── Auto column widths ──
        $widths = ['A' => 5, 'B' => 22, 'C' => 16, 'D' => 20, 'E' => 16, 'F' => 24, 'G' => 35, 'H' => 6, 'I' => 15, 'J' => 12, 'K' => 12, 'L' => 15, 'M' => 13, 'N' => 12];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        // ── Freeze pane below header ──
        $sheet->freezePane('A5');

        // ── Output ──
        $filename = "Laporan-Penjualan-{$dateFrom}-sd-{$dateTo}.xlsx";

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
