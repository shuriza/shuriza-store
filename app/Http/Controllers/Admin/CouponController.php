<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponUsage;
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
        $categories = Category::active()->ordered()->get(['id', 'name']);

        return view('admin.coupons.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'         => 'required|string|max:50|unique:coupons,code',
            'name'         => 'required|string|max:100',
            'type'         => 'required|in:fixed,percent',
            'value'        => ['required', 'integer', 'min:1', $request->type === 'percent' ? 'max:100' : 'max:999999999'],
            'min_order'    => 'nullable|integer|min:0',
            'max_discount' => 'nullable|integer|min:0',
            'usage_limit'  => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'min_total_items' => 'nullable|integer|min:1',
            'allowed_category_ids' => 'nullable|array',
            'allowed_category_ids.*' => 'integer|exists:categories,id',
            'campaign_name' => 'nullable|string|max:120',
            'starts_at'    => 'nullable|date',
            'expires_at'   => 'nullable|date|after_or_equal:starts_at',
        ]);

        $validated['code'] = Str::upper($validated['code']);
        $validated['min_order'] = $validated['min_order'] ?? 0;
        $validated['min_total_items'] = $validated['min_total_items'] ?? 1;
        $validated['allowed_category_ids'] = !empty($validated['allowed_category_ids'])
            ? array_map('intval', $validated['allowed_category_ids'])
            : null;
        $validated['is_active'] = true;

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Kupon berhasil dibuat!');
    }

    public function edit(Coupon $coupon)
    {
        $categories = Category::active()->ordered()->get(['id', 'name']);

        return view('admin.coupons.edit', compact('coupon', 'categories'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code'         => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'name'         => 'required|string|max:100',
            'type'         => 'required|in:fixed,percent',
            'value'        => ['required', 'integer', 'min:1', $request->type === 'percent' ? 'max:100' : 'max:999999999'],
            'min_order'    => 'nullable|integer|min:0',
            'max_discount' => 'nullable|integer|min:0',
            'usage_limit'  => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'min_total_items' => 'nullable|integer|min:1',
            'allowed_category_ids' => 'nullable|array',
            'allowed_category_ids.*' => 'integer|exists:categories,id',
            'campaign_name' => 'nullable|string|max:120',
            'starts_at'    => 'nullable|date',
            'expires_at'   => 'nullable|date|after_or_equal:starts_at',
        ]);

        $validated['code'] = Str::upper($validated['code']);
        $validated['min_order'] = $validated['min_order'] ?? 0;
        $validated['min_total_items'] = $validated['min_total_items'] ?? 1;
        $validated['allowed_category_ids'] = !empty($validated['allowed_category_ids'])
            ? array_map('intval', $validated['allowed_category_ids'])
            : null;

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

    public function usages(Coupon $coupon)
    {
        $usages = CouponUsage::with(['order', 'user'])
            ->where('coupon_id', $coupon->id)
            ->latest('used_at')
            ->paginate(25);

        $stats = [
            'total_uses'       => $coupon->used_count,
            'total_discount'   => CouponUsage::where('coupon_id', $coupon->id)->sum('discount_amount'),
            'unique_users'     => CouponUsage::where('coupon_id', $coupon->id)->whereNotNull('user_id')->distinct('user_id')->count('user_id'),
            'last_used_at'     => ($last = CouponUsage::where('coupon_id', $coupon->id)->max('used_at'))
                                  ? \Carbon\Carbon::parse($last)
                                  : null,
        ];

        return view('admin.coupons.usages', compact('coupon', 'usages', 'stats'));
    }
}
