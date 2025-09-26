<template>
    <div class="main" >
        <div v-for="(dispo,i) in disponibilites" :key="i">
            <div v-if="page==i">
                <div class="descr">
                    <span class="date">{{ displaydate(dispo.jour) }}</span><br/>
                        <c-pagination>
                            <c-pagination-item 
                                    href="#" 
                                    @click="pagemoins()" 
                                    :disabled="page<=0"
                                >Match précédent
                            </c-pagination-item>
                            <c-pagination-item 
                                    href="#" 
                                    @click="pageplus()" 
                                    :disabled="page>=disponibilites.length-1"
                                >Match suivant
                            </c-pagination-item>
                        </c-pagination>
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

        <c-pagination>
            <c-pagination-item 
                    href="#" 
                    @click="pagemoins()" 
                    :disabled="page<=0"
                >Match précédent
            </c-pagination-item>
            <c-pagination-item 
                    href="#" 
                    @click="pageplus()" 
                    :disabled="page>=disponibilites.length-1"
                >Match suivant
            </c-pagination-item>
        </c-pagination>
    </div>
</template>

<script>
import {getDisponibilites,setDisponibilite,displaydate} from '@/js/api.js'
import Presence from '@/components/Presence.vue'
import {ref} from 'vue'
import {CPagination,CPaginationItem} from "@coreui/vue"

import '@coreui/coreui/dist/css/coreui.min.css'
export default {

    components: {
        Presence,CPagination,CPaginationItem
        
  },    
    setup() {
        const disponibilites = ref([])
        const page = ref(0)

        getDisponibilites().then( p => {
            disponibilites.value = p

            //selectionne la page courante
            let d1=new Date()
            for (let i in p) {
                //let s=p[i].date.split("/")
                //let d2=new Date(s[2]+"-"+s[1]+"-"+s[0])
                let d2=new Date(p[i].jour)
                if (d2 > d1)  {
                    page.value=i
                    break
                }                
            }
        })

        function update(usr,jour,val) {            
            setDisponibilite(usr,jour,val).then( p => {
                disponibilites.value = p
            })
        }

        function pageplus() {
            if (page.value<(disponibilites.value.length-1)) {
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


        return {disponibilites,page,update,pageplus,pagemoins,pageselect,displaydate}
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