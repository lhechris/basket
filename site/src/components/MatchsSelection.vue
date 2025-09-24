<template>
    <div class="selection"  >
        <div class="select-container"><select class="styled-select" @change="changeEquipe($event)">
                <option v-for="e in listeequipes" :key="e.id" :value="e">Equipe {{ e }}</option>
            </select>
        </div>
        <div v-for="(equipe,ie) in equipes" :key="ie">
            <table  v-if="equipe.equipe == equipeselected">
                <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th v-for="(u,j) in equipe['joueurs']" :key="j">{{ u.prenom }}<br/>{{ u.nb }}</th>
                    <th v-for="(u,j) in equipe['autrejoueurs']" :key="j">{{ u.prenom }}<br/>{{ u.nb }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(s,n) in equipe.matchs" :key="n">                
                    <th class="thmatch"><RouterLink :to="'/match/'+s.id">{{ s.jour }} - {{ s.titre }}</RouterLink></th>
                    <th>{{ s.nb }}</th>
                    <td v-for="(u,j) in s.users" :key="j" >
                        <Selection :pres="u.dispo" 
                                :sel="u.selection" 
                                @onUpdate="update(u.id,s.id,$event)"/>
                    </td>
                    <td v-for="(u,j) in s.autres" :key="j" >
                        <Selection :pres="u.dispo" 
                                :sel="u.selection" 
                                @onUpdate="update(u.id,s.id,$event)"/>
                    </td>                
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import {getSelections,setSelection} from '@/js/api.js'
import Selection from '@/components/Selection2.vue'
import {ref} from 'vue'

import '@coreui/coreui/dist/css/coreui.min.css'
export default {

    components: {
        Selection
        
  },    
    setup() {
        const equipes = ref([])
        const listeequipes = ref([])
        const equipeselected = ref(0)

        getSelections().then( p => {
            equipes.value = p
           
            listeequipes.value=[]
            for (let e in p) {
                listeequipes.value.push(p[e]["equipe"])
            }
            equipeselected.value=p[0]['equipe']
        })

        function update(usr,match,val) {
                setSelection(usr,match,val).then( p => {
                    equipes.value = p
            })
        }

        function changeEquipe(event) {
            equipeselected.value=event.target.value;
            
           
        }

        return {equipes,equipeselected,update,changeEquipe,listeequipes}
    }
}
</script>
<style scoped>

.descr {
    border-radius: 6px;
    background-color: #70b6da;
}


.lieu {
    font-size: 0.8rem;
}
table {
    width:100%;
}
tr,td, th  {
    border : 2px none grey;
    
}

th {
    text-align: center;
}

.thmatch {
    text-align:left
}

</style>