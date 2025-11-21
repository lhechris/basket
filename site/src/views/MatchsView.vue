<template>
  <div class="flex flex-col">
      <content  v-for="(jour,i) of matchs" :key="i" :index="i" couleur="bg-teal-600" :nbpages="matchs.length" v-model="page">        
            <template #titre>
                <span>{{displaydate(jour["jour"])}}</span>
            </template>
            <template #default>
              <div v-for="match of jour['matchs']" class="pr-4 pl-4" >              
                  <detail-match :matchdetail="match" /> 
              </div>
            </template>
        </content>
    </div>  
  </template>
  

  <script setup>
  // @ is an alias to /src
  import DetailMatch from '../components/DetailMatch.vue'
  import {getMatchsAvecSel,displaydate,getFirstDateAfterNow} from '../js/api.js'
  import {ref} from "vue"
  import Content from '../components/Content.vue'

  const matchs = ref([])
  const page=ref(1)

  function refreshMatch() {
      getMatchsAvecSel().then( m => {
            matchs.value = m

            //selectionne la page courante
            page.value = 1 + getFirstDateAfterNow(m,false)

      })
  }

  refreshMatch();

</script>
<style scoped>

.detailmatch {   
    margin-top:5px;
}

</style>