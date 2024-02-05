<template>
    <!--<img height="16" :class="selected==1 ? '' : 'imgback'" src="@/assets/verifie.png" @click="toggle(1)"/>
    <img height="16" :class="selected==2 ? '' : 'imgback'" src="@/assets/annuler.png" @click="toggle(2)"/>
    <img height="16" :class="selected==0 ? '' : 'imgback'" src="@/assets/warning.png" @click="toggle(0)"/>-->
    <span><button class="button-3 presente" :class="selected==1 ? '': 'unselected'" @click="toggle(1)">Présente</button></span>
    <span><button class="button-3 absente" :class="selected==2 ? '': 'unselected'" @click="toggle(2)">Absente</button></span>
    <span><button class="button-3 aucun" :class="selected==0 ? ' ': 'unselected'" @click="toggle(0)">Peut être</button></span>

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

span {
    margin-left:10px;
}

/* CSS */
.button-3 {
  appearance: none;
  background-color: #2ea44f;
  border: 1px solid rgba(27, 31, 35, .15);
  border-radius: 6px;
  box-shadow: rgba(27, 31, 35, .1) 0 1px 0;
  box-sizing: border-box;
  color: #fff;
  cursor: pointer;
  display: inline-block;
  font-family: -apple-system,system-ui,"Segoe UI",Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji";
  font-size: 0.8rem;
  font-weight: 600;
  line-height: 20px;
  padding: 3px 12px;
  position: relative;
  text-align: center;
  text-decoration: none;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  vertical-align: middle;
  white-space: nowrap;
}

.presente {
    background-color: #2ea44f;
}
.absente {
    background-color: #a42e2e;
}
.aucun {
    background-color: #d8a04d;
}


.button-3:focus:not(:focus-visible):not(.focus-visible) {
  box-shadow: none;
  outline: none;
}

.presente:hover {
  background-color: #2c974b;
}
.absente:hover {
  background-color: #792222;
}
.aucun:hover {
  background-color: #be8d42;
}

.unselected {
    background-color: #474646;
}

.button-3:disabled {
  background-color: #474646;
  border-color: rgba(27, 31, 35, .1);
  color: rgba(255, 255, 255, .8);
  cursor: default;
}

.presente:active {
  background-color: #30a752;
  box-shadow: rgba(20, 70, 32, .2) 0 1px 0 inset;
}
.absente:active {
  background-color: #cc3a3a;
  box-shadow: rgba(20, 70, 32, .2) 0 1px 0 inset;
}
.aucun:active {
  background-color: #f1b356;
  box-shadow: rgba(20, 70, 32, .2) 0 1px 0 inset;
}

</style>
