<template>
    <div class="flex flex-col gap-1">
      <DetailMatchAdmin :matchdetail="match" @changeOpp="updateOpp" @change-match="updateMatch"/> 
  
    </div>
  </template>
  
  <script setup>
  // @ is an alias to /src
  import DetailMatchAdmin from '@/components/DetailMatchAdmin.vue'
  import {getMatch,setOpposition,setMatch} from '@/js/api.js'
  import {ref} from "vue"
  
  const props = defineProps(['id'])
  const match = ref([])

  function refreshMatch() {
      getMatch(props.id).then( m => {
              match.value = m
      })
  }

  function updateMatch(newmatch) {
    setMatch(newmatch)
  }

  function updateOpp(matchid,userid,val) {
      setOpposition(matchid,userid,val);
      refreshMatch();      
      
  } 

  refreshMatch();

</script>
  