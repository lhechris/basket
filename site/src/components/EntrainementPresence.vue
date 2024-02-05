<template>
    <div class="main" >
        <div v-for="(m,n) in entrainements" :key="n">
            <div v-if="page==n">
                <div class="descr" >
                    <span class="date">{{ m.date }}</span><br/>
                    
                </div>
                <table>
                <tr v-for="(u,j) in users" :key="j">
                    <th>{{ u.name }}</th>
                    <td>
                        <Presence :sel="presences[u.id][m.id]" @onUpdate="update(u.id,m.id,$event)"/>
                    </td>
                </tr>
                </table>
            </div>
        </div>
        <c-pagination>
            <c-pagination-item 
                    href="#/entrainement" 
                    @click="pagemoins()" 
                    :disabled="page<=0"
                >Précédent
            </c-pagination-item>
            <c-pagination-item 
                    href="#/entrainement" 
                    @click="pageplus()" 
                    :disabled="page>=entrainements.length-1"
                >Suivant
            </c-pagination-item>
        </c-pagination>
    </div>
</template>

<script>
import {getEntrainements, getUsers, getPresences,setPresence} from '@/js/api.js'
import Presence from '@/components/Presence.vue'
import {ref} from 'vue'
import {CPagination,CPaginationItem} from "@coreui/vue"

import '@coreui/coreui/dist/css/coreui.min.css'
export default {

    components: {
        Presence,CPagination,CPaginationItem
        
  },    
    setup() {
        const entrainements = ref([])
        const users = ref([]);
        const presences = ref([])
        const page = ref(0)

        getUsers().then( u => {
            users.value = u;
        })

        getPresences().then( p => {
            presences.value = p
        })

        getEntrainements().then( m => {
            entrainements.value = m
            //selectionne la page courante
            let d1=new Date()
            for (let i in m) {
                let s=m[i].date.split("/")
                let d2=new Date(s[2]+"-"+s[1]+"-"+s[0]+1)
                if (d2 > d1)  {
                    page.value=i
                    break
                }                
            }            
        })

        function countJoueuses(mid) {
            let nb=0
            for (let s in presences.value) {
                if (presences.value[s][mid]==1) {
                    nb=nb+1
                }
            }
            return nb
        }

        function update(usr,match,val) {
            presences.value[usr][match]=val
            setPresence(usr,match,val)
        }

        function pageplus() {
            if (page.value<(entrainements.value.length-1)) {
                page.value++
            }
        }

        function pagemoins() {
            if (page.value>0) {
                page.value--
            }
        }

        function pageselect(n) {
            page.value=n
        }


        return {users,entrainements,presences,page,countJoueuses,update,pageplus,pagemoins,pageselect}
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

.date {
    font-weight:600;
    font-size : 1.2rem;
}

.descr {
    border-radius: 6px;
    background-color: #70da82;
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

th {
    font-size : 0.9rem;
    text-align: right;
}


</style>