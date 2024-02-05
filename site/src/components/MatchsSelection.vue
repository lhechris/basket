<template>
    <div class="main" >
        <table >
            <tr>
                <th></th>
                <th></th>
                <th v-for="(u,j) in users" :key="j" >{{ u.name }}<br/>{{ countMatchs(u.id) }}</th>                
            </tr>
            <tr v-for="(m,n) in matches" :key="n">
                <th class="thmatch">{{ m.date }} - {{ m.lieu }} - {{ m.resultat }} </th>
                <th>{{ countJoueuses(m.id) }}</th>
                <td v-for="(u,j) in users" :key="j" >
                    <Selection :pres="disponibilites[u.id][m.id]" 
                               :sel="getSelection(u.id,m.id)" 
                               @onUpdate="update(u.id,m.id,$event)"/>
                </td>
            
            </tr>
        </table>

        <!--
        <div v-for="(m,n) in matches" :key="n">
            <div v-if="page==n">
                <div class="descr">
                    <span class="date">{{ m.date }}</span><br/>
                    <span class="lieu">{{ m.lieu }}</span><br/>
                    <span class="resultat">Nb joueuses: {{ countJoueuses(m.id) }}</span>
                </div>
                <table>
                <tr v-for="(u,j) in users" :key="j">
                    <th>{{ u.name }}</th>
                    <td>{{ countMatchs(u.id) }}</td>
                    <td>
                        <Selection :pres="disponibilites[u.id][m.id]" :sel="getSelection(u.id,m.id)" @onUpdate="update(u.id,m.id,$event)"/>
                    </td>
                </tr>
                </table>
            </div>
        </div>-->

        <!--<c-pagination>
            <c-pagination-item 
                    href="#/selection"
                    @click="pagemoins()" 
                    :disabled="page<=0"
                >Précédent
            </c-pagination-item>

            <c-pagination-item
                    href="#/selection" 
                    @click="pageplus()" 
                    :disabled="page>=matches.length-1"
                >Suivant
            </c-pagination-item>
        </c-pagination>-->

    </div>
</template>

<script>
import {getMatches, getUsers, getDisponibilites,getSelections,setSelection} from '@/js/api.js'
import Selection from '@/components/Selection2.vue'
import {ref} from 'vue'
//import {CPagination,CPaginationItem} from "@coreui/vue"

import '@coreui/coreui/dist/css/coreui.min.css'
export default {

    components: {
        Selection/*,CPagination,CPaginationItem*/
        
  },    
    setup() {
        const matches = ref([])
        const users = ref([]);
        const disponibilites = ref([])
        const selections = ref([])
        const page = ref(0)
        const value = ref(true)

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
            //selectionne la page courante
            let d1=new Date()
            for (let i in m) {
                let s=m[i].date.split("/")
                let d2=new Date(s[2]+"-"+s[1]+"-"+s[0])
                if (d2 > d1)  {
                    page.value=i
                    break
                }                
            }            
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

        return {value,users,matches,disponibilites,selections,page,getSelection,update,pageplus,pagemoins,pageselect,countJoueuses,countMatchs}
    }
}
</script>
<style scoped>
.main {
    display:block;
    margin-left:auto;
    margin-right:auto;    
    width: 1200px;
    height : 800px;
    /*overflow : scroll;
    scrollbar-color: rebeccapurple green;
    scrollbar-width: thin;*/
}

.descr {
    border-radius: 6px;
    background-color: #70b6da;
}


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

</style>