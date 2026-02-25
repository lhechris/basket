<template>
    <div class="mb-5 flex flex-col">
        <textarea class="w-full" rows="6">{{ value }}</textarea>
        <div class="flex gap-4">
            <button class="rounded middle none center mr-4 rounded-lg bg-indigo-400 py-1 px-2 font-sans text-xs font-bold uppercase text-white shadow-md shadow-indigo-400/20 transition-all hover:shadow-lg hover:shadow-indigo-400/40 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                    @click="copier(value)">Copier</button>                    
            <success v-if="showCopyOk" value="Texte copié" />
        </div>
    </div>
</template>

<script setup>

    import {ref} from 'vue'
    import Success from '@/components/Success.vue'
    
    const props = defineProps(['value'])

    const showCopyOk = ref(false)

    function copier(texte) {
        navigator.clipboard.writeText(texte).then(
            function () {
                showCopyOk.value = true;
                setTimeout(() => {
                    showCopyOk.value = false;
                }, 5000);
            },
            function () {
                //erreur
            }
        );
    }

</script>