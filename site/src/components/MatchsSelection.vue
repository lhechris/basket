<template>
    <div class="main" >
        <div v-for="(c,n) in matches" :key="n">
            <table v-if="page==n">
                <tr>
                    <th colspan="2"></th>
                    <th class="coldate" v-for="(m,i) in matches[n]" :key="i">{{ m.date }}<br/><span class="lieu">{{ m.lieu }}</span> </th>
                </tr>
                <tr v-for="(u,j) in users" :key="j">
                    <th>{{ u.name }}</th>
                    <td>{{ countMatchs(u.id) }}</td>
                    <td v-for="(m,k) in matches[n]" :key="k">
                        <Selection :pres="disponibilites[u.id][m.id]" :sel="getSelection(u.id,m.id)" @onUpdate="update(u.id,m.id,$event)"/>
                    </td>
                </tr>
                <tr>
                    <th colspan="2"></th>
                    <td v-for="(m,k) in matches[n]" :key="k">
                        {{ countJoueuses(m.id) }}
                    </td>
                </tr>
            </table> 
        </div>

        <c-pagination>
            <c-pagination-item 
                    @click="pagemoins()" 
                    :disabled="page<=0"
                >&laquo;
            </c-pagination-item>
            <c-pagination-item 
                    :active="page==n"
                    @click="pageselect(n)" 
                    v-for="(c,n) in matches" 
                    :key="n" 
                >{{ n }}
            </c-pagination-item>
            <c-pagination-item 
                    @click="pageplus()" 
                    :disabled="page>=matches.length-1"
                >&raquo;
            </c-pagination-item>
        </c-pagination>

    </div>
</template>

<script>
import {getMatches, getUsers, getDisponibilites,getSelections,setSelection} from '@/js/api.js'
import Selection from '@/components/Selection.vue'
import {ref} from 'vue'
import {CPagination,CPaginationItem} from "@coreui/vue"

import '@coreui/coreui/dist/css/coreui.min.css'
export default {

    components: {
        Selection,CPagination,CPaginationItem
        
  },    
    setup() {
        const matches = ref([])
        const users = ref([]);
        const disponibilites = ref([])
        const selections = ref([])
        const page = ref(0)

        getUsers().then( u => {
            users.value = u;
        })

        getDisponibilites().then( p => {
            disponibilites.value = p
        })

        getSelections().then( p => {
            selections.value = p
        })
        

        getMatches().then( m => {
            matches.value = m
        })

        function getSelection(uid,mid) {
            if (selections.value.length>uid) {
                if (selections.value[uid].length>mid) {
                    return selections.value[uid][mid];
                } else {
                    return -1
                }
            } else {
                return -1
            }
            
        }

        function update(usr,match,val) {
            if (getSelection(usr,match)>=0) {
                selections.value[usr][match]=val
                setSelection(usr,match,val)
            }
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

        function pageselect(n) {
            page.value=n
        }

        function countJoueuses(mid) {
            let nb=0
            for (let s in selections.value) {
                if (selections.value[s][mid]==1) {
                    nb=nb+1
                }
            }
            return nb
        }
        function countMatchs(uid) {
            let nb=0
            let matchs = selections.value[uid]
            for (let m in matchs) {
                if (matchs[m]==1) {
                    nb=nb+1
                }
            }
            return nb
        }

        return {users,matches,disponibilites,selections,page,getSelection,update,pageplus,pagemoins,pageselect,countJoueuses,countMatchs}
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