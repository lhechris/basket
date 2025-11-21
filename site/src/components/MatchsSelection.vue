<template>
    <div class="md:col-span-8 md:col-start-2 text-xs md:text-base">
        <div class="select-container">
            <select class="styled-select" @change="changeEquipe($event)">
                <option v-for="e in listeequipes" :key="e.id" :value="e">Equipe {{ e }}</option>
            </select>
        </div>
       
        <div v-for="(equipe,ie) in equipes" :key="ie">
            <table  v-if="equipe.equipe == equipeselected">
                <thead>
                <tr class="">
                    <th></th>
                    <th></th>
                    <th class="odd:bg-blue-100 max-w-5" v-for="(u,j) in equipe['joueurs']" :key="j">
                        
                        <p class="rotate-60 pt-4">{{ u.prenom }}</p>
                        <p>({{ u.nb }})</p>
                    </th>
                    <th class="odd:bg-blue-100 max-w-5" v-for="(u,j) in equipe['autrejoueurs']" :key="j">
                        <p class="rotate-60">{{ u.prenom }}</p>
                        <p>({{ u.nb }})</p>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(s,n) in equipe.matchs" :key="n">                
                    <th class="thmatch"><RouterLink :to="'/match/'+s.id">{{ displaydatemin(s.jour) }}</RouterLink></th>
                    <th>{{ s.nb }}</th>
                    <td class="odd:bg-blue-100 p-1" v-for="(u,j) in s.users" :key="j" >
                        <Selection :pres="u.dispo" 
                                :sel="u.selection"
                                :disabled="isjourdepasse(s.jour)" 
                                @onUpdate="update(u.id,s.id,$event,s.jour)"/>
                    </td>
                    <td class="odd:bg-blue-100 p-1" v-for="(u,j) in s.autres" :key="j" >
                        <Selection :pres="u.dispo" 
                                :sel="u.selection" 
                                :disabled="isjourdepasse(s.jour)" 
                                @onUpdate="update(u.id,s.id,$event,s.jour)"/>
                    </td>                
                </tr>
                </tbody>
            </table>
        </div>




    </div>
</template>

<script setup>
import {getSelections,setSelection,displaydatemin,isjourdepasse} from '@/js/api.js'
import Selection from '@/components/Selection.vue'
import {ref} from 'vue'

import '@coreui/coreui/dist/css/coreui.min.css'
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

    function update(usr,match,val,jour) {
        if (isjourdepasse(jour)) {
            return
        }
        setSelection(usr,match,val).then( () => {
            getSelections().then( p => {
                equipes.value = p
            })
        })
    }

    function changeEquipe(event) {
        equipeselected.value=event.target.value;
        
        
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