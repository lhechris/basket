<template>
    <div class="main" >
        <div v-for="(e,n) in presences" :key="n">
            <div v-if="page==n+1">
                <div class="descr bg-2" >
                    <span class="date">{{displaydate(e.date) }}</span>
                    <span class="date">&nbsp; &nbsp;[{{ countJoueuses(e.id) }}]</span>
                    <span><cust-pagination message="" v-model="page" :nbpages="presences.length" /></span>
                </div>
                <table>
                <tr v-for="(u,j) in e.users" :key="j">
                    <th class="text-xl">{{ u.prenom }} ({{ u.nbent }})</th>
                    <td>
                        <Presence :sel="u.pres" @onUpdate="update(u.id,e.id,$event)"/>
                    </td>
                </tr>
                </table>
            </div>
        </div>
        <cust-pagination message="" v-model="page" :nbpages="presences.length" />
    </div>
</template>

<script setup>
import {getPresences,setPresence,displaydate} from '@/js/api.js'
import Presence from '@/components/Presence.vue'
import CustPagination from '@/components/CustPagination.vue'
import {ref} from 'vue'

import '@coreui/coreui/dist/css/coreui.min.css'

const presences = ref([])
const page = ref(1)

getPresences().then( p => {
    presences.value = p

    //selectionne la prochaine page par rapport 
    //au jour actuel
    let d1=new Date()
    for (let i in p) {
        let d2=new Date(p[i].date)
        d2.setDate(d2.getDate()+1)
        if (d2 > d1)  {
            page.value=parseInt(i) + 1
            break
        }                
    }
    countEntrainementParJoueuse()
   
})

//compte le nombre d'entrainement par joueuses
function countEntrainementParJoueuse() {
    let tabjoueuses=[]
    for (let p of presences.value) {
        for (let u of p.users) { 
            if (!tabjoueuses[u.prenom]) {
                tabjoueuses[u.prenom]=0
            }
            if (u.pres == 1) {
                tabjoueuses[u.prenom]++;                
            }            
            u.nbent=tabjoueuses[u.prenom];            
        }
    }
}

// Compte le nombre de joueuse par entrainement
function countJoueuses(mid) {
    let nb=0
    for (let p of presences.value) {
        if (p.id == mid) {
            for (let u of p.users) {                
                if (u.pres == 1) {
                    nb=nb+1                   
                }
            }
        }
    }
    return nb
}


function update(usr,match,val) {    
    setPresence(usr,match,val).then( p => {
        presences.value = p 
        countEntrainementParJoueuse()   
    })
}

</script>
<style scoped>

.date {
    font-weight:600;
    font-size : 1.2rem;
}

.lieu {
    font-size: 0.8rem;
}

table {
    margin-top : 1rem;
    width : 100%;
}
tr,td, th  {
    border : 2px none grey;
}

/*th {
    font-size : 0.9rem;
    text-align: right;
}*/


</style>