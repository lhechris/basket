<template>
    <div class="matchs">
        <table>
            <thead>
                <tr><th>Rencontre</th><th>Date</th><th>Equipe</th><th>Resultat</th><th>Collation</th><th>OTM</th><th>Maillots</th><th></th></tr>
            </thead>
            <tbody>
            <tr v-for="(m,i) in matches" :key="i" >
            <td><input v-model="m.lieu" :class="m.todelete==true ? 'disabled' : ''"/></td>
            <td><input v-model="m.date" class="inputdate" :class="m.todelete==true ? 'disabled' : ''"/></td>
            <td><input v-model="m.equipe" class="inputdate" :class="m.todelete==true ? 'disabled' : ''"/></td>
            <td><input v-model="m.resultat" class="inputresultat" :class="m.todelete==true ? 'disabled' : ''"/></td>
            <td><input v-model="m.collation" :class="m.todelete==true ? 'disabled' : ''"/></td>
            <td><input v-model="m.otm" :class="m.todelete==true ? 'disabled' : ''"/></td>
            <td><input v-model="m.maillots" :class="m.todelete==true ? 'disabled' : ''"/></td>
            <td>
                <button class="btn btn-delete" @click="supprime(m.id)">
                    <img src= "@/assets/annuler.png" width="16"/>
                </button>
            </td>
            </tr>
            </tbody>
        </table>
        <button class="btn btn-primary" @click="ajoute()">Nouveau</button>
        <button class="btn btn-secondary" @click="enregistrer()">Enregistrer</button>
    </div>
</template>

<script>
import {getMatches,setMatches} from '@/js/api.js'
import {ref} from "vue"

export default {
  
    setup() {

        const matches = ref([])

        // eslint-disable-next-line
        function supprime(id) {
            let indice=-1;
            matches.value.forEach( (e,k) => {
                if (e.id == id) { indice = k;}
            } )
            
            if (indice>=0) {
                matches.value[indice]["todelete"]= true;
            }
        }

        function ajoute() {
            matches.value.push({lieu:"lieu", date:"xx/xx",equipe:"1", resultat:""})
        }

        function enregistrer() {
            setMatches(matches.value).then( m => {
                matches.value = m
            })

        }

        getMatches().then( m => {
            matches.value = m
        })


        return {supprime,ajoute,enregistrer,matches}


    }
}
</script>

<style scoped>

.inputdate {
    width:6em;
}

.inputresultat {
    width:4em;

}


</style>