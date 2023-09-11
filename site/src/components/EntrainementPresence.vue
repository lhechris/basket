<template>
    <div class="main" >
        <div v-for="(c,n) in entrainements" :key="n">
            <table v-if="page==n">
                <tr>
                    <th></th>
                    <th class="coldate" v-for="(m,i) in entrainements[n]" :key="i">{{ m.date }}<br/><span class="lieu">{{ m.lieu }}</span> </th>
                </tr>
                <tr v-for="(u,j) in users" :key="j">
                    <th>{{ u.name }}</th>
                    <td v-for="(m,k) in entrainements[n]" :key="k">
                        <Presence :sel="presences[u.id][m.id]" @onUpdate="update(u.id,m.id,$event)"/>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td v-for="(m,k) in entrainements[n]" :key="k">
                        {{ countJoueuses(m.id) }}
                    </td>
                </tr>
            </table> 
        </div>

        <c-pagination>
            <c-pagination-item 
                    href="#/entrainement" 
                    @click="pagemoins()" 
                    :disabled="page<=0"
                >&laquo;
            </c-pagination-item>
            <c-pagination-item 
                    :active="page==n"
                    href="#/entrainement" 
                    @click="pageselect(n)" 
                    v-for="(c,n) in entrainements" 
                    :key="n" 
                >{{ n }}
            </c-pagination-item>
            <c-pagination-item 
                    href="#/entrainement" 
                    @click="pageplus()" 
                    :disabled="page>=entrainements.length-1"
                >&raquo;
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
    width: 600px;
    height : 600px;
    /*overflow : scroll;
    scrollbar-color: rebeccapurple green;
    scrollbar-width: thin;*/
}

.lieu {
    font-size: 0.8rem;
}
tr,td, th  {
    border : 2px solid grey;
}

th {
    text-align: center;
}


</style>