<template>
    <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center"
            @click="updateselected()">
        <img class="fill-current w-4 h-4 mr-2" src="@/assets/note_13132398.png"/>
        <span>Changer la vue</span>
    </button>
    <div class="flex md:grid md:grid-cols-10 flex-col mt-2">
        <matchs-selection2  v-if="vueselected==0"/>
        <matchs-selection v-if="vueselected==1"/>
    </div>
  </template>
  
<script setup>
  // @ is an alias to /src
  import MatchsSelection from '@/components/MatchsSelection.vue'
  import MatchsSelection2 from '@/components/MatchsSelection2.vue'
  import {ref} from 'vue'

  const vueselected = ref(0)

  function updateselected() {
    if (vueselected.value==0) { vueselected.value=1}
    else { vueselected.value=0}
  }

</script>
  