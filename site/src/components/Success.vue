<template>
    <div class="flex inline-flex justify-between bg-teal-100 border border-teal-400 text-teal-700 px-4 rounded "
        role="alert">
        <span class="block sm:inline pl-2">                         
            {{value}}
        </span>
    </div>

</template>

<script setup>
    const props = defineProps(['value'])
</script>