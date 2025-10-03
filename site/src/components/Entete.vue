<template>
    <div class="rounded-md font-bold text-xl flex mb-2" :class="couleur">
        <cust-pagination message="" v-model="page" :nbpages="nbpages" />
        <slot></slot>
    </div>
</template>

<script setup>
    import CustPagination from '@/components/CustPagination.vue'

    const props = defineProps(["nbpages","couleur"])
    const page = defineModel()

</script>