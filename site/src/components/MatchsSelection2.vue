<template>
    <div class="md:col-span-8 md:col-start-2 text-xs md:text-base  overflow-x-scroll">
       
            <table class="table-auto" >
                <thead>
                <tr class="">
                    <th></th>
                    <th class="odd:bg-blue-100 max-w-5" v-for="(jour,i) in jours" :key="i">
                        <p class="rotate-60 pt-4">{{  displaydatemin(jour.jour) }}</p>
                        <div class="grid grid-cols-2 w-16">
                            <span v-for="(m,j) in jour.matchs" :key="j">[<RouterLink :to="'/match/'+m.id">{{ m.nb }}</RouterLink>]</span>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody class="mt-10">                    
                <tr class="border-2 border-solid border-red-500 odd:bg-purple-200 " v-for="(u,n) in joueurs" :key="n">                
                    <td class="border-1 border-solid sticky left-0 h-fit bg-purple-200">{{ u.prenom }} ({{u.nb}})</td>
                    <td class="border-1 border-solid" v-for="(jour,k) in u.jours" :key="k">
                        <div :class="jour.dispo==1 ? 'bg-green-400' : jour.dispo==2 ? 'bg-red-400' : 'bg-orange-300'" class="grid grid-cols-3 w-16 p-1">
                            <span>
                                <img v-if="jour.matchs[0].selection==1" src="../assets/one_10456722.png"/>
                                <img v-else src="../assets/one_10456722_grey.png" @click="update(u.id,jour.matchs[0].id,1)"/>
                            </span>
                            <span class="col-start-2" @click="update(u.id,jour.matchs[0].id,0)">
                                <!--<img  v-if="jour.matchs[0].selection==0 && jour.matchs[1].selection==0" src="../assets/multiplier.png"/>
                                <img  v-else src="../assets/multiplier_grey.png" @click="update(u.id,jour.matchs[0].id,0)" />     -->                           
                            </span>
                            <span class="col-start-3">
                                <img  v-if="jour.matchs[1].selection==1" src="../assets/two_10456778.png" />
                                <img  v-else src="../assets/two_10456778_grey.png" @click="update(u.id,jour.matchs[1].id,1)"/>
                            </span>
                        </div>
                    </td>
                    <!--<td class="odd:bg-blue-100 p-1" v-for="(u,j) in s.users" :key="j" >
                        <Selection :pres="u.dispo" 
                                :sel="u.selection" 
                                @onUpdate="update(u.id,s.id,$event)"/>
                    </td>
                    <td class="odd:bg-blue-100 p-1" v-for="(u,j) in s.autres" :key="j" >
                        <Selection :pres="u.dispo" 
                                :sel="u.selection" 
                                @onUpdate="update(u.id,s.id,$event)"/>
                    </td> -->               
                </tr>
                </tbody>
            </table>


    </div>
</template>

<script setup>
import {getSelections2,setSelection,displaydatemin} from '@/js/api.js'
import {ref} from 'vue'

import '@coreui/coreui/dist/css/coreui.min.css'
    const joueurs = ref([])
    const jours = ref([])

    getSelections2().then( p => {
        jours.value = p["jours"]
        joueurs.value = p["joueurs"]

    })

    function update(usr,match,val) {
            setSelection(usr,match,val).then( p => {
            jours.value = p["jours"]
            joueurs.value = p["joueurs"]
        })
    }


</script>
<style scoped>

.lieu {
    font-size: 0.8rem;
}
table {
    width:100%;
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