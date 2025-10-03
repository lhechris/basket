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
            </template>
        </content>
    </div>
</template>
  
  <script setup>
  // @ is an alias to /src
  import DetailMatchAdmin from '@/components/DetailMatchAdmin.vue'
  import {getMatchsAvecOpp,displaydate,setOpposition,setMatch} from '@/js/api.js'
  import {ref} from "vue"
  import Content from '@/components/Content.vue'
  import moment from 'moment'

  const matchs = ref([])
  const page=ref(1)
  const props = defineProps(['id'])

  function refreshMatch(forcedate=0) {
      getMatchsAvecOpp().then( m => {
            matchs.value = m

            //selectionne la page courante
            let d1=new Date()
            if (forcedate!=0) {
                d1=new Date(forcedate)
            }
            for (let i in m) {
                let d2=new Date(m[i].jour)
                if (d2 >= d1)  {
                    page.value= parseInt(i) + 1
                    break
                }                
            }
            //console.log("refresh match",m,page.value)

      })
  }

  function updateMatch(newmatch) {
    setMatch(newmatch)
    //refreshMatch(newmatch.jour)
  }

  function updateOpp(matchid,userid,val) {
    let jour = matchs.value[parseInt(page.value)-1].jour  
    setOpposition(matchid,userid,val);
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