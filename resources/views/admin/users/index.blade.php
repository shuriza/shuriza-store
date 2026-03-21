@extends('layouts.admin')

@section('title', 'Kelola Pelanggan')
@section('page-title', 'Pelanggan')
@section('breadcrumb')
<span>Pelanggan</span>
@endsection

@section('content')
<div x-data="{ confirmDelete: null, confirmToggle: null }">

{{-- Page Header --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-white">Kelola Pelanggan</h1>
        <div class="mt-2 flex flex-wrap gap-2">
            <span class="rounded-full bg-gray-800 px-3 py-1 text-xs font-semibold text-gray-300">Total: {{ $totalUsers }}</span>
            <span class="rounded-full bg-peri/10 px-3 py-1 text-xs font-semibold text-peri">Admin: {{ $totalAdmins }}</span>
            <span class="rounded-full bg-green-500/10 px-3 py-1 text-xs font-semibold text-green-400">Customer: {{ $totalCustomers }}</span>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<form method="GET" action="{{ route('admin.users.index') }}">
    <div class="mb-6 rounded-2xl border border-gray-800 bg-gray-900 p-4">
        <div class="flex flex-wrap items-end gap-3">
            <div class="min-w-[200px] flex-1">
                <label class="mb-1 block text-xs font-medium text-gray-400">Cari</label>
                <div class="relative">
                    <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-500"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau email..."
                           class="w-full rounded-xl border border-gray-700 bg-gray-800 py-2.5 pl-9 pr-4 text-sm text-white placeholder-gray-500 focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri" />
                </div>
            </div>
            <div class="w-44">
                <label class="mb-1 block text-xs font-medium text-gray-400">Role</label>
                <select name="role"
                        class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-2.5 text-sm text-white focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri">
                    <option value="">Semua</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Customer</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-peri px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-peri-dark">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-gray-700 px-4 py-2.5 text-sm font-medium text-gray-400 transition hover:text-white">
                    <i class="fas fa-redo-alt"></i> Reset
                </a>
            </div>
        </div>
    </div>
</form>

@if($users->count())
<div class="overflow-hidden rounded-2xl border border-gray-800 bg-gray-900">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-800 text-xs uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Pesanan</th>
                    <th class="px-4 py-3">Bergabung</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @foreach($users as $user)
                <tr class="transition hover:bg-gray-800/50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-peri/20 text-xs font-bold text-peri">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            <span class="font-medium text-white">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-400">{{ $user->email }}</td>
                    <td class="px-4 py-3">
                        @if($user->role === 'admin')
                            <span class="rounded-full bg-peri/10 px-2.5 py-1 text-xs font-semibold text-peri">Admin</span>
                        @else
                            <span class="rounded-full bg-gray-700 px-2.5 py-1 text-xs font-semibold text-gray-300">Customer</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-white">{{ $user->orders_count }}</td>
                    <td class="px-4 py-3 text-gray-400">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.users.show', $user) }}"
                               class="rounded-lg bg-peri/10 px-3 py-1.5 text-xs font-semibold text-peri transition hover:bg-peri/20">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <button @click="confirmToggle = {{ $user->id }}"
                                    class="rounded-lg bg-yellow-500/10 px-3 py-1.5 text-xs font-semibold text-yellow-400 transition hover:bg-yellow-500/20">
                                <i class="fas fa-user-shield"></i>
                            </button>
                            <button @click="confirmDelete = {{ $user->id }}"
                                    class="rounded-lg bg-red-500/10 px-3 py-1.5 text-xs font-semibold text-red-400 transition hover:bg-red-500/20">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </div>

                        @if($user->id !== auth()->id())
                        {{-- Toggle Role Confirmation --}}
                        <div x-show="confirmToggle === {{ $user->id }}" x-cloak x-transition
                             class="mt-2 rounded-lg border border-yellow-500/20 bg-yellow-500/5 p-3 text-left">
                            <p class="mb-2 text-xs text-yellow-400">Ubah role {{ $user->name }} menjadi {{ $user->role === 'admin' ? 'Customer' : 'Admin' }}?</p>
                            <div class="flex gap-2">
                                <form action="{{ route('admin.users.toggle-role', $user) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="rounded-lg bg-yellow-600 px-3 py-1 text-xs font-semibold text-white transition hover:bg-yellow-700">Ya</button>
                                </form>
                                <button @click="confirmToggle = null" class="rounded-lg border border-gray-700 px-3 py-1 text-xs text-gray-400 transition hover:text-white">Batal</button>
                            </div>
                        </div>
                        {{-- Delete Confirmation --}}
                        <div x-show="confirmDelete === {{ $user->id }}" x-cloak x-transition
                             class="mt-2 rounded-lg border border-red-500/20 bg-red-500/5 p-3 text-left">
                            <p class="mb-2 text-xs text-red-400">Hapus user {{ $user->name }}?</p>
                            <div class="flex gap-2">
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-red-600 px-3 py-1 text-xs font-semibold text-white transition hover:bg-red-700">Hapus</button>
                                </form>
                                <button @click="confirmDelete = null" class="rounded-lg border border-gray-700 px-3 py-1 text-xs text-gray-400 transition hover:text-white">Batal</button>
                            </div>
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $users->links() }}
</div>
@else
<div class="rounded-2xl border border-gray-800 bg-gray-900 py-16 text-center">
    <i class="fas fa-users mb-4 text-4xl text-gray-700"></i>
    <p class="text-gray-400">Tidak ada pelanggan ditemukan.</p>
</div>
@endif

</div>
@endsection
