<template>
    <div class="main" >
        <div v-for="(dispo,i) in disponibilites" :key="i">
            <div v-if="page==i+1">
                <div class="descr bg-1">
                    <span class="date">{{ displaydate(dispo.jour) }}</span><br/>
                    <cust-pagination message="Match" v-model="page" :nbpages="disponibilites.length" />
                </div>
                <table>
                <tr v-for="(u,j) in dispo.users" :key="j">
                    <th>{{ u.prenom }}</th>
                    <td>
                        <Presence :sel="u.dispo" @onUpdate="update(u.id,dispo.jour,$event)"/>
                    </td>
                </tr>
                </table>
            </div>
        </div>
        <cust-pagination message="Match" v-model="page" :nbpages="disponibilites.length" />
    </div>
</template>

<script>
import {getDisponibilites,setDisponibilite,displaydate} from '@/js/api.js'
import Presence from '@/components/Presence.vue'
import {ref} from 'vue'
import CustPagination from "@/components/CustPagination.vue"

import '@coreui/coreui/dist/css/coreui.min.css'
export default {

    components: {
        Presence,CustPagination
        
  },    
    setup() {
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

        return {disponibilites,page,update,displaydate}
    }
}
</script>
<style scoped>
.main {
    display:block;
    margin-left:auto;
    margin-right:auto; 
    width: 400px;
    height : 500px;
    /*overflow : scroll;
    scrollbar-color: rebeccapurple green;
    scrollbar-width: thin;*/
}


.lieu {
    font-weight: 600;
    font-size: 1em;
}
.date {
    font-weight:600;
    font-size : 1.2em;
}

.resultat {
    font-size : 0.8em;
}

table {
    margin-top : 1em;
    width : 100%;

}

tr,td, th  {
    border : 1px none grey;
}

th {
    text-align: right;
    font-size : 1em;
    
}


</style>