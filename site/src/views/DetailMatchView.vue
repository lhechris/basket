<template>
    <div class="home">
      <DetailMatch :matchdetail="match" @changeOpp="updateOpp" @change-match="updateMatch"/> 
  
    </div>
  </template>
  
  <script setup>
  // @ is an alias to /src
  import DetailMatch from '@/components/DetailMatch.vue'
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
  