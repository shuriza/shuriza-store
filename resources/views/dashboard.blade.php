{{-- This view is unused — the "dashboard" route renders customer/dashboard.blade.php via CustomerDashboardController --}}
@extends('layouts.app')

@section('content')
<script>window.location.href = '{{ route("dashboard") }}';</script>
@endsection
