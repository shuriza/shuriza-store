<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(20);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'            => 'required|string|max:50|unique:coupons,code',
            'name'            => 'required|string|max:100',
            'type'            => 'required|in:fixed,percent',
            'value'           => ['required', 'integer', 'min:1', $request->type === 'percent' ? 'max:100' : 'max:999999999'],
            'min_order'       => 'nullable|integer|min:0',
            'max_discount'    => 'nullable|integer|min:0',
            'usage_limit'     => 'nullable|integer|min:1',
            'usage_per_user'  => 'nullable|integer|min:1',
            'first_order_only' => 'boolean',
            'starts_at'       => 'nullable|date',
            'expires_at'      => 'nullable|date|after_or_equal:starts_at',
        ]);

        $validated['code'] = Str::upper($validated['code']);
        $validated['min_order'] = $validated['min_order'] ?? 0;
        $validated['first_order_only'] = $request->boolean('first_order_only');
        $validated['is_active'] = true;

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Kupon berhasil dibuat!');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code'            => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'name'            => 'required|string|max:100',
            'type'            => 'required|in:fixed,percent',
            'value'           => ['required', 'integer', 'min:1', $request->type === 'percent' ? 'max:100' : 'max:999999999'],
            'min_order'       => 'nullable|integer|min:0',
            'max_discount'    => 'nullable|integer|min:0',
            'usage_limit'     => 'nullable|integer|min:1',
            'usage_per_user'  => 'nullable|integer|min:1',
            'first_order_only' => 'boolean',
            'starts_at'       => 'nullable|date',
            'expires_at'      => 'nullable|date|after_or_equal:starts_at',
        ]);

        $validated['code'] = Str::upper($validated['code']);
        $validated['min_order'] = $validated['min_order'] ?? 0;
        $validated['first_order_only'] = $request->boolean('first_order_only');

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Kupon berhasil diperbarui!');
    }

    public function toggleActive(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);
        $status = $coupon->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Kupon {$coupon->code} berhasil {$status}.");
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Kupon berhasil dihapus.');
    }
}
