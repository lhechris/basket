<template>
    <div class="flex flex-col text-xl" >
        <content v-for="(e,n) in presences" :key="n" :index="n" couleur="bg-emerald-500" :nbpages="presences.length" v-model="page">         

            <template #titre>
                    <span class="date">{{displaydate(e.date) }}</span>
                    <span class="date">&nbsp; &nbsp;[{{ countJoueuses(e.id) }}]</span>
            </template>
                
            <template #default>
                <div class="grid grid-cols-3 gap-1 pt-1" v-for="(u,j) in e.users" :key="j">
                    <div class="span-col-2 text-xl font-bold text-right">{{ u.prenom }} ({{ u.nbent }})</div>
                    <Presence :sel="u.pres" @onUpdate="update(u.id,e.id,$event)"/>
                </div>
            </template>
        </content>
    </div>
</template>

<script setup>
import {getPresences,setPresence,displaydate} from '@/js/api.js'
import Presence from '@/components/Presence.vue'
import Content from '@/components/Content.vue'

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