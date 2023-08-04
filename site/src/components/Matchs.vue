<template>
    <div class="main" >
        <div v-for="(c,n) in matches" :key="n">
            <table v-if="page==n">
                <tr>
                    <th></th>
                    <th class="coldate" v-for="(m,i) in matches[n]" :key="i">{{ m.date }}<br/><span class="lieu">{{ m.lieu }}</span> </th>
                </tr>
                <tr v-for="(u,j) in users" :key="j">
                    <th>{{ u.name }}</th>
                    <td v-for="(m,k) in matches[n]" :key="k">
                        <Presence :sel="presences[u.id][m.id]" @onUpdate="update(u.id,m.id,$event)"/>
                    </td>
                </tr>
            </table> 
        </div>

        <c-button 
            shape="rounded-pill"
            color="primary"
            v-if="page>0" @click="pagemoins()"
            >Précédent
        </c-button>

        <c-button 
            shape="rounded-pill"
            color="primary"
            v-if="page<matches.length-1" @click="pageplus()"
            >Suivant
        </c-button>

    </div>
</template>

<script>
import {getMatches, getUsers, getPresences,setPresences} from '@/js/api.js'
import Presence from '@/components/Presence.vue'
import {ref} from 'vue'
import {CButton} from "@coreui/vue"

import '@coreui/coreui/dist/css/coreui.min.css'
export default {

    components: {
        Presence,CButton
        
  },    
    setup() {
        const matches = ref([])
        const users = ref([]);
        const presences = ref([])
        const page = ref(0)

        getUsers().then( u => {
            users.value = u;
        })

        getPresences().then( p => {
            presences.value = p
        })

        getMatches().then( m => {
            matches.value = m
        })

        function update(usr,match,val) {
            presences.value[usr][match]=val
            setPresences(usr,match,val)
        }

        function pageplus() {
            if (page.value<(matches.value.length-1)) {
                page.value++
            }
        }

        function pagemoins() {
            if (page.value>0) {
                page.value--
            }
        }

        return {users,matches,presences,page,update,pageplus,pagemoins}
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