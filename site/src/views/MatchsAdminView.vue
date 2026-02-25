<template>
  <div class="flex flex-col">
    <div class="grid grid-cols-6 mb-2 pl-1">
        <button class="btn btn-primary" @click="ajouteMatch()">Créer un match</button>
    </div>
    <content  v-for="(jour,i) of matchs" :key="i" :index="i" couleur="bg-teal-600" :nbpages="matchs.length" v-model="page">        
            <template #titre>
                <span>{{displaydate(jour["jour"])}}</span>
            </template>
            <template #default>
              <div v-for="match of jour['matchs']" class="pr-4 pl-4" >              
                  <detail-match-admin :matchdetail="match" @change-match="updateMatch" @changeOpp="updateOpp" /> 
              </div>
              <convocation :value="jour['convocation']" />
            </template>
      </content>      
  </div>
</template>
  
  <script setup>
  // @ is an alias to /src
  import DetailMatchAdmin from '@/components/DetailMatchAdmin.vue'
  import {getMatchsAvecOpp,displaydate,setOpposition,setMatch,getFirstDateAfterNow} from '@/js/api.js'
  import {ref} from "vue"
  import Content from '@/components/Content.vue'
  import Convocation from '@/components/Convocation.vue'
  import moment from 'moment'

  const matchs = ref([])
  const page=ref(1)
  const props = defineProps(['id'])

  function refreshMatch(forcedate=0) {
      getMatchsAvecOpp().then( m => {
          matchs.value = m
          //selectionne la page courante
          page.value = 1 + getFirstDateAfterNow(m,false,forcedate)
      })
  }

  function updateMatch(newmatch) {
    setMatch(newmatch)
    //refreshMatch(newmatch.jour)
  }

  function updateOpp(matchid,userid,val,numero,commentaire) {
    let jour = matchs.value[parseInt(page.value)-1].jour  
    setOpposition(matchid,userid,val,numero,commentaire);
    //refreshMatch(jour);      
      
  } 

  function ajouteMatch() {

    let d = moment().day(6)

    const result = prompt("Quel jour ? YYYY-MM-JJ",d.format('YYYY-MM-DD'));  
    if (result) {
        const newmatch = {titre:"lieu", jour:result,equipe:"1",score:"",collation:"",otm:"",maillots:"",adresse:"",horaire:"",rendezvous:""}
        setMatch(newmatch)
        refreshMatch(result);
    }
  }


  refreshMatch();



</script>
<style scoped>

.detailmatch {   
    margin-top:5px;
}

</style>