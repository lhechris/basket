<template>

    <div class="flex flex-col text-xl" >
        <content v-for="(e,n) in disponibilites" :key="n" :index="n" couleur="bg-blue-400" :nbpages="disponibilites.length" v-model="page">         

            <template #titre>
                    <span class="date">{{displaydate(e.jour) }}</span>
            </template>
                
            <template #default>
                <div class="grid grid-cols-3 gap-2 pt-2" v-for="(u,j) in e.users" :key="j">
                    <div class="span-col-2 text-xl font-bold text-right">{{ u.prenom }}</div>
                    <Presence :sel="u.dispo" @onUpdate="update(u.id,e.jour,$event)"/>
                </div>
            </template>
        </content>
    </div>







<!--    <div class="main" >
        <div v-for="(dispo,i) in disponibilites" :key="i">
            <div v-if="page==i+1">
                <div class="descr bg-1">
                    <span class="date">{{ displaydate(dispo.jour) }}</span><br/>
                    <cust-pagination message="Match" v-model="page" :nbpages="disponibilites.length" />
                </div>
                <table>
                <tr v-for="(u,j) in dispo.users" :key="j">
                    <th class="text-xl">{{ u.prenom }}</th>
                    <td>
                        <Presence :sel="u.dispo" @onUpdate="update(u.id,dispo.jour,$event)"/>
                    </td>
                </tr>
                </table>
            </div>
        </div>
        <cust-pagination message="Match" v-model="page" :nbpages="disponibilites.length" />
    </div>-->
</template>

<script setup>
import {getDisponibilites,setDisponibilite,displaydate} from '@/js/api.js'
import Presence from '@/components/Presence.vue'
import {ref} from 'vue'
//import CustPagination from "@/components/CustPagination.vue"
import Content from '@/components/Content.vue'

import '@coreui/coreui/dist/css/coreui.min.css'

const disponibilites = ref([])
const page = ref(1)

getDisponibilites().then( p => {
    disponibilites.value = p

    //selectionne la page courante
    let d1=new Date()
    for (let i in p) {
        
        //let s=p[i].date.split("/")
        //let d2=new Date(s[2]+"-"+s[1]+"-"+s[0])
        let d2=new Date(p[i].jour)
        if (d2 > d1)  {
            page.value= parseInt(i) + 1
            break
        }                
    }
})

function update(usr,jour,val) {            
    setDisponibilite(usr,jour,val).then( p => {
        disponibilites.value = p
    })
}
</script>