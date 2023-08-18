<template>
    <div :class="pres==1 ? 'presok' : pres==2 ? 'prespasok' : 'presundef'" >
    <!--<img v-if="pres==1" height="12" src="@/assets/verifie.png" />
    <img v-if="pres==2" height="12" src="@/assets/annuler.png" />
    <img v-if="pres==0" height="12" src="@/assets/warning.png" />-->
    <img height="16" :class="selected==0 ? '' : 'imgback'" src="@/assets/annuler.png" @click="toggle(0)"/>
    <img height="16" :class="selected==1 ? '' : 'imgback'" src="@/assets/verifie.png" @click="toggle(1)"/>
    </div>
</template>

<script>

import {ref} from "vue"

export default ({    

    emits: ['onUpdate'],
    props : ['sel','pres'],
    
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

.presok {
    background: rgba(11, 226, 47, 0.5)
}

.prespasok {
    background: rgba(224, 14, 14, 0.5)
}
.presundef {
    background: rgba(250, 151, 4, 0.5)
}


</style>
