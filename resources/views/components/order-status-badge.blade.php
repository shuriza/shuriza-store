@props(['status'])

@php
    $config = match($status) {
        'pending'    => ['label' => 'Menunggu',    'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400', 'icon' => 'fas fa-clock'],
        'processing' => ['label' => 'Diproses',    'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',       'icon' => 'fas fa-spinner'],
        'completed'  => ['label' => 'Selesai',     'class' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',    'icon' => 'fas fa-check-circle'],
        'cancelled'  => ['label' => 'Dibatalkan',  'class' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',           'icon' => 'fas fa-times-circle'],
        default      => ['label' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',       'icon' => 'fas fa-circle'],
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold ' . $config['class']]) }}>
    <i class="{{ $config['icon'] }} text-[10px]"></i>
    {{ $config['label'] }}
</span>
