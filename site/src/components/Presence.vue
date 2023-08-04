<template>
    <img height="16" :class="selected==1 ? '' : 'imgback'" src="@/assets/verifie.png" @click="toggle(1)"/>
    <img height="16" :class="selected==2 ? '' : 'imgback'" src="@/assets/annuler.png" @click="toggle(2)"/>
</template>

<script>

import {ref} from "vue"

export default ({    

    emits: ['onUpdate'],
    props : ['sel'],
    
    setup(props,ctx) {
        const selected = ref(props.sel)
        function toggle(clicked) {
            if (selected.value!=clicked) {
                ctx.emit('onUpdate',clicked)
            }
            selected.value = clicked
        }
        return {selected,toggle}
    },
})
</script>

<style scoped>
.imgback {
    opacity : 0.3;
    filter:alpha(opacity=30)
}

</style>
