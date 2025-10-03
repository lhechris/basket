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
  import DetailMatch from '@/components/DetailMatch.vue'
  import {getMatchsAvecSel,displaydate} from '@/js/api.js'
  import {ref} from "vue"
  import Content from '@/components/Content.vue'
  
  const matchs = ref([])
  const page=ref(1)

  function refreshMatch() {
      getMatchsAvecSel().then( m => {
            matchs.value = m

            //selectionne la page courante
            let d1=new Date()
            for (let i in m) {
                let d2=new Date(m[i].jour)
                if (d2 > d1)  {
                    page.value= parseInt(i) + 1
                    break
                }                
            }

      })
  }

  refreshMatch();

</script>
<style scoped>

.detailmatch {   
    margin-top:5px;
}

</style>