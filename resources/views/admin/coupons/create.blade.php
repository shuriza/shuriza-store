@extends('layouts.admin')

@section('title', 'Tambah Kupon')
@section('page-title', 'Tambah Kupon')
@section('breadcrumb')
<a href="{{ route('admin.coupons.index') }}" class="text-gray-400 hover:text-white">Kupon</a>
<i class="fas fa-chevron-right mx-1 text-[0.5rem] text-gray-600"></i>
<span>Tambah</span>
@endsection

@section('content')
<div class="mx-auto max-w-2xl">
    <form action="{{ route('admin.coupons.store') }}" method="POST">
        @csrf
        @include('admin.coupons._form')
    </form>
</div>
@endsection
