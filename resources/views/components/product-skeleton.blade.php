@props(['count' => 4])

@for($i = 0; $i < $count; $i++)
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden flex flex-col">
    <div class="h-[195px] skeleton"></div>
    <div class="p-3 space-y-2">
        <div class="h-4 skeleton w-full"></div>
        <div class="h-4 skeleton w-2/3"></div>
        <div class="flex justify-between items-end pt-2">
            <div class="h-5 skeleton w-20"></div>
            <div class="h-8 w-8 skeleton rounded-full"></div>
        </div>
    </div>
</div>
@endfor
