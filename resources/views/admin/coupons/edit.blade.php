@extends('layouts.admin')

@section('title', 'Edit Kupon')
@section('page-title', 'Edit Kupon')
@section('breadcrumb')
<a href="{{ route('admin.coupons.index') }}" class="text-gray-400 hover:text-white">Kupon</a>
<i class="fas fa-chevron-right mx-1 text-[0.5rem] text-gray-600"></i>
<span>Edit {{ $coupon->code }}</span>
@endsection

@section('content')
<div class="mx-auto max-w-2xl">
    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.coupons._form', ['coupon' => $coupon])
    </form>
</div>
@endsection
