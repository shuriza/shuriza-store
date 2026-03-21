<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-peri border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-peri-dark focus:bg-peri-dark active:bg-peri-darker focus:outline-none focus:ring-2 focus:ring-peri focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
