<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function download(string $orderNumber)
    {
        $order = Order::with('items.product')->where('order_number', $orderNumber)->firstOrFail();

        // Only owner or admin can download
        if (auth()->check()) {
            if (!auth()->user()->isAdmin() && $order->user_id !== auth()->id()) {
                abort(403);
            }
        } else {
            abort(403);
        }

        $storeName = setting('store_name', 'Shuriza Store');
        $storeAddress = setting('store_address', 'Kediri, Jawa Timur');
        $storePhone = setting('whatsapp_number', '');
        $storeEmail = setting('store_email', '');

        // Generate HTML invoice and return as download
        $html = view('invoice-pdf', compact('order', 'storeName', 'storeAddress', 'storePhone', 'storeEmail'))->render();

        $filename = "Invoice-{$order->order_number}.html";

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function show(string $orderNumber)
    {
        $order = Order::with('items.product')->where('order_number', $orderNumber)->firstOrFail();

        if (auth()->check()) {
            if (!auth()->user()->isAdmin() && $order->user_id !== auth()->id()) {
                abort(403);
            }
        } else {
            abort(403);
        }

        $storeName = setting('store_name', 'Shuriza Store');
        $storeAddress = setting('store_address', 'Kediri, Jawa Timur');
        $storePhone = setting('whatsapp_number', '');
        $storeEmail = setting('store_email', '');

        return view('invoice-pdf', compact('order', 'storeName', 'storeAddress', 'storePhone', 'storeEmail'));
    }
}
