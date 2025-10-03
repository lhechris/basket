<template>
    <div class="flex md:grid md:grid-cols-8 flex-col">
        <div class="md:col-span-4 md:col-start-3" v-if="page==index+1">

            <entete v-model="page" :nbpages="nbpages" :couleur="couleur">
                <slot name="titre" ></slot>
            </entete>
            
            <slot></slot>
            
        </div>
    </div>

</template>

<script setup>
import Entete from './Entete.vue';

const props = defineProps(["nbpages","index", "couleur"])
const page = defineModel()

</script>