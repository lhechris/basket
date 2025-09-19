<template>
    <div class="main" >
        <div v-for="(e,n) in presences" :key="n">
            <div v-if="page==n">
                <div class="descr" >
                    <span class="date">{{displaydate(e.date) }}</span>
                    <span class="date">&nbsp; &nbsp;[{{ countJoueuses(e.id) }}]</span>
                    <span><c-pagination>
                        <c-pagination-item 
                                href="#/entrainement" 
                                @click="pagemoins()" 
                                :disabled="page<=0"
                            >Précédent
                        </c-pagination-item>
                        <c-pagination-item 
                                href="#/entrainement" 
                                @click="pageplus()" 
                                :disabled="page>=presences.length-1"
                            >Suivant
                        </c-pagination-item>
                    </c-pagination>  </span>                    
                </div>
                <table>
                <tr v-for="(u,j) in e.users" :key="j">
                    <th>{{ u.prenom }}</th>
                    <td>
                        <Presence :sel="u.pres" @onUpdate="update(u.id,e.id,$event)"/>
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
                    :disabled="page>=presences.length-1"
                >Suivant
            </c-pagination-item>
        </c-pagination>
    </div>
</template>

<script>
import {getPresences,setPresence,displaydate} from '@/js/api.js'
import Presence from '@/components/Presence.vue'
import {ref} from 'vue'
import {CPagination,CPaginationItem} from "@coreui/vue"

import '@coreui/coreui/dist/css/coreui.min.css'
export default {

    components: {
        Presence,CPagination,CPaginationItem
        
  },    
    setup() {
        const presences = ref([])
        const page = ref(0)

        getPresences().then( p => {
            presences.value = p
            let d1=new Date()
            for (let i in p) {
                //let s=p[i].date.split("/")
                //let d2=new Date(s[2]+"-"+s[1]+"-"+s[0]+1)
                let d2=new Date(p[i].date)
                d2.setDate(d2.getDate()+1)
                if (d2 > d1)  {
                    page.value=i
                    break
                }                
            }            

        })

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
            })
        }

        function pageplus() {
            if (page.value<(presences.value.length-1)) {
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


        return {presences,page,countJoueuses,update,pageplus,pagemoins,pageselect,displaydate}
    }
}
</script>
<style scoped>

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