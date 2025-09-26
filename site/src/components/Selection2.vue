<template>

    <div :class="pres==1 ? 'presok' : pres==2 ? 'prespasok' : 'presundef'" >
    <!--<span><button class="button-3 absente" :class="selected==0 ? '': 'unselected'" @click="toggle(0)">N</button></span>
    <span><button class="button-3 presente" :class="selected==1 ? '': 'unselected'" @click="toggle(1)">S</button></span>-->
    <Toggle v-model="btn.value" v-bind="btn" class="custombtn" @change="montoggle()"/>
    </div>
</template>

<script setup>

    import {ref} from "vue"
    import Toggle from '@vueform/toggle'

    const emits=defineEmits(['onUpdate'])
    const props=defineProps(['sel','pres'])

    const btn = ref({
                value:props.sel,
                trueValue:1,
                falseValue:0,
            })

    function montoggle() {
        emits('onUpdate',btn.value.value)
    }

</script>

<style src="@vueform/toggle/themes/default.css" />

<style scoped>
.custombtn {
    --toggle-border-off:rgba(153, 13, 13, 0.5);
    --toggle-bg-off:rgba(153, 13, 13, 0.5);
    --toggle-width:2rem;
    --toggle-height:1rem;
}

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

.button-3:hover {
  background-color: #2c974b;
}

.button-3:focus {
  box-shadow: rgba(46, 164, 79, .4) 0 0 0 3px;
  outline: none;
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

.button-3:active {
  background-color: #298e46;
  box-shadow: rgba(20, 70, 32, .2) 0 1px 0 inset;
}


</style>
