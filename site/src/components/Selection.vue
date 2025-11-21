<template>

    <div :class="pres==1 ? 'bg-green-400' : pres==2 ? 'bg-red-400' : 'bg-orange-300'">
    <input 
            v-model="btn.value" 
            id="default-checkbox" 
            type="checkbox" 
            :disabled="disabled"
            @change="montoggle()"
            class="w-3 h-3 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
    >
    </div>
</template>

<script setup>

    import {ref} from "vue"

    const emits=defineEmits(['onUpdate'])
    const props=defineProps(['sel','pres','disabled'])

    const btn = ref({
                value:props.sel==1,
                trueValue:1,
                falseValue:0,
            })

    function montoggle() {
        let v=0
        if (btn.value.value) {
            v=1
        }
        emits('onUpdate',v)
    }

</script>

<style src="@vueform/toggle/themes/default.css" />

<style scoped>
.custombtn {
    --toggle-border-off:rgba(153, 13, 13, 0.5);
    --toggle-bg-off:rgba(153, 13, 13, 0.5);
    --toggle-width:1.5rem;
    --toggle-height:0.8rem;
}

</style>
