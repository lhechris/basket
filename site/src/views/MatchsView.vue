<template>
    <div class="flex md:grid md:grid-cols-6 flex-col">
      <div class="md:col-span-4 md:col-start-2" v-for="(jour,i) of matchs" :key="i" >
        <div class="flex flex-col gap-1" v-if="page==i+1" >
          <div class="bg-amber-500 rounded-md font-bold text-xl" >
              <span>{{displaydate(jour["jour"])}} </span>
              <cust-pagination message="" v-model="page" :nbpages="matchs.length" />
          </div>
          <div v-for="match of jour['matchs']" class="pr-4 pl-4">
              <detail-match :matchdetail="match" @change-match="updateMatch"/> 
          </div>
        </div>
      </div>  
    </div>
  </template>
  

  <script setup>
  // @ is an alias to /src
  import DetailMatch from '@/components/DetailMatch.vue'
  import {getMatchsAvecSel,displaydate} from '@/js/api.js'
  import {ref} from "vue"
  import CustPagination from '@/components/CustPagination.vue'
  
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

  function updateMatch(newmatch) {
    //setMatch(newmatch)
    console.log("Update match n'est pas encore disponible",newmatch)
  }

  refreshMatch();

</script>
<style scoped>

.detailmatch {   
    margin-top:5px;
}

</style>