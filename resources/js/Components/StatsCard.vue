<script setup>
defineProps({
    title: String,
    value: [String, Number],
});
</script>

<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-sm font-medium text-gray-500 truncate">{{ title }}</h3>
            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ value }}</p>
        </div>
    </div>
</template>
