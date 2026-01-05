@props(['title', 'value', 'icon' => '📦'])

<div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg flex flex-col items-start justify-center">
    <div class="flex items-center gap-3 mb-2">
        <span class="text-2xl">{{ $icon }}</span>
        <h3 class="text-sm text-gray-500">{{ $title }}</h3>
    </div>
    <p class="text-3xl font-semibold text-indigo-600">{{ $value }}</p>
</div>