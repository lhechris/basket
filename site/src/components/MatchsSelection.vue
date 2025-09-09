<template>
    <div class="main" >
        <table >
            <thead>
            <tr>
                <th></th>
                <th></th>
                <th v-for="(u,j) in users" :key="j" >{{ u.nom }}<br/>{{ countMatchs(u.id) }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(s,n) in selections" :key="n">
                <th class="thmatch">{{ s.date }} - {{ s.lieu }} - {{ s.resultat }} </th>
                <th>{{ countJoueuses(s.id) }}</th>
                <td v-for="(u,j) in s.users" :key="j" >
                    <Selection :pres="u.dispo" 
                               :sel="u.selection" 
                               @onUpdate="update(u.id,s.id,$event)"/>
                </td>
            
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import {getSelections,setSelection} from '@/js/api.js'
import Selection from '@/components/Selection2.vue'
import {ref} from 'vue'

import '@coreui/coreui/dist/css/coreui.min.css'
export default {

    components: {
        Selection
        
  },    
    setup() {
        const selections = ref([])
        const users = ref([])

        getSelections().then( p => {
            selections.value = p
            for (let s of p) {
                users.value = s.users
                break;
            }
        })

        function update(usr,match,val) {
                setSelection(usr,match,val).then( p => {
                    selections.value = p
            })
        }

        function countJoueuses(mid) {
            let nb=0
            for (let match of selections.value) {
                if (match.id == mid) {
                    for (let u of match.users) {
                        if (u.selection == 1) {
                            nb=nb+1
                        }
                    }
                }
            }
            return nb
        }
        function countMatchs(uid) {
            let nb=0
            for (let match of selections.value) {
                for (let u of match.users) {
                    if ((u.id == uid) && (u.selection == 1)) {
                        nb=nb+1
                    }
                }
            }
            return nb
        }

        return {selections,users,update,countJoueuses,countMatchs}
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