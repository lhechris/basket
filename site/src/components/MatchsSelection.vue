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
import Selection from '@/components/Selection.vue'
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

.select-container {
    margin: 10px;
    width: 150px;
}

.styled-select {
    width: 100%;
    padding: 10px;
    font-size: 1em;
    border: 2px solid #ccc;
    border-radius: 5px;
    background-color: #f8f8f8;
    color: #333;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url('data:image/svg+xml;utf8,<svg fill="black" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
    background-repeat: no-repeat;
    background-position: right 10px center;
    cursor: pointer;
}

/* Style au survol */
.styled-select:hover {
    border-color: #888;
}

/* Style au focus */
.styled-select:focus {
    outline: none;
    border-color: #4a90e2;
    box-shadow: 0 0 5px rgba(74, 144, 226, 0.5);
}

/* Style des options (limité selon les navigateurs) */
.styled-select option {
    padding: 10px;
    background-color: white;
    color: #333;
}

.selection {
    display:block;
    margin-left:auto;
    margin-right:auto;    
    width: 1200px;
    height : 800px;
    /*background-color:darkcyan;*/
}



</style>