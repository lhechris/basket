<template>
    <div class="flex flex-col gap-1">
      <DetailMatchAdmin :info="info" @change="update"/> 
  
    </div>
  </template>
  
  <script setup>
  // @ is an alias to /src
  import DetailMatchAdmin from '@/components/FeuilleMatch.vue'
  import {getMatch} from '@/js/api.js'
  import {ref} from "vue"
  
  const props = defineProps(['id'])
  const info = ref([])

  function refresh() {
      getMatch(props.id).then( m => {
              info.value = m
      })
  }

  refresh();

</script>
  