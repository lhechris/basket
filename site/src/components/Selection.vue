<template>

    <div :class="pres==1 ? 'bg-green-400' : pres==2 ? 'bg-red-400' : 'bg-orange-300'" class="">
    <!--<Toggle v-model="btn.value" v-bind="btn" class="custombtn " @change="montoggle()"/>-->
    <input 
            v-model="btn.value" 
            id="default-checkbox" 
            type="checkbox" 
            @change="montoggle()"
            class="w-3 h-3 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
    >

    <!--<label class="inline-flex items-center me-5 cursor-pointer">
      <input type="checkbox" value="" class="sr-only peer" checked>
      <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-red-300 dark:peer-focus:ring-red-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-red-600 dark:peer-checked:bg-red-600"></div>
    </label>-->


    </div>
</template>

<script setup>

    import {ref} from "vue"
    import Toggle from '@vueform/toggle'

    const emits=defineEmits(['onUpdate'])
    const props=defineProps(['sel','pres'])

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
