<template>
    <div class="matchs">
        <div class="descr bg-1">
            <span class="titre"><input v-model="currentmatch.titre"  @input="debouncedOnChange()"/></span>
            <span>&nbsp;Equipe<input v-model="currentmatch.equipe"  @input="debouncedOnChange()"/></span>
            <span><button class="btn btn-secondary btn-small" @click="supprime()">Supprimer</button></span>
            <p><input v-model="currentmatch.jour" /></p>

        </div>
        <div class="main">
            <table>
                <thead>
                </thead>
                <tbody>
                <tr><td>Score</td><td><input v-model="currentmatch.score"  @input="debouncedOnChange()"/></td></tr>
                <tr><td>Collation</td><td><input v-model="currentmatch.collation"  @input="debouncedOnChange()"/></td></tr>
                <tr><td>OTM</td><td><input v-model="currentmatch.otm"  @input="debouncedOnChange()"/></td></tr>
                <tr><td>Maillots</td><td><input v-model="currentmatch.maillots"  @input="debouncedOnChange()"/></td></tr>
                <tr><td>Adresse</td><td><input v-model="currentmatch.adresse"  @input="debouncedOnChange()"/></td></tr>
                <tr><td>Horaire</td><td><input v-model="currentmatch.horaire"  @input="debouncedOnChange()"/></td></tr>
                <tr><td>Rendez-vous</td><td><input v-model="currentmatch.rendezvous"  @input="debouncedOnChange()"/></td></tr>
                </tbody>
            </table>
            <table v-if="matchdetail.oppositions">            
                <thead><tr><th colspan="3">Opposition A</th><th colspan="3">Opposition B</th><th colspan="3"></th></tr></thead>
                <tbody v-for="r,i in tabselections" :key="i">
                    <tr>
                        <td>{{r[0].prenom}}</td>
                        <td>{{r[0].licence}}</td>
                        <td><select v-if="r[0].user" @change="$emit('changeOpp',matchdetail.id,r[0].user,$event.target.value)">
                                    <option value=""> </option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                </select></td>
                        <td>{{r[1].prenom}}</td>
                        <td>{{r[1].licence}}</td>
                        <td><select v-if="r[1].user" @change="$emit('changeOpp',matchdetail.id,r[1].user,$event.target.value)">
                                    <option value=""> </option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                </select></td>
                        <td>{{r[2].prenom}}</td>
                        <td></td>
                        <td><select v-if="r[2].user" @change="$emit('changeOpp',matchdetail.id,r[2].user,$event.target.value)">
                                    <option value=""> </option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                </select></td>
                    </tr>
                </tbody>
            </table>            
        </div>
    </div>
</template>

<script setup>
    import {ref,watch} from 'vue'
    import {onBeforeUnmount} from 'vue'
    import { debounce } from 'lodash';

    const props = defineProps (['matchdetail' ])
    const emit = defineEmits(['changeMatch','changeOpp'])

    const currentmatch = ref(props.matchdetail)
    const tabselections = ref([])
    updatetabselections()

    function supprime() {
        const result = confirm("Voulez vous vraiment supprimer le match ?")
        if (result) {
            currentmatch.value["todelete"]= true;
            emit('changeMatch',currentmatch.value)
        }
    }


    const onChange = () => {
        emit('changeMatch',currentmatch.value)
    }
        
    const debouncedOnChange = debounce(onChange, 2000);

    function updatetabselections() {
        tabselections.value = []
        if (currentmatch.value.oppositions) {
            const maxrow = Math.max(currentmatch.value.oppositions.A.length,currentmatch.value.oppositions.B.length,currentmatch.value.oppositions.Autres.length) 
            
            for (let i=0;i<maxrow;i++) {
                let A={"prenom":"","licence":"","user" : null}
                let B={"prenom":"","licence":"", "user" :null}
                let Autres={"prenom":"", "user":null}
                if (i<currentmatch.value.oppositions.A.length) {
                    A=currentmatch.value.oppositions.A[i]
                }
                if (i<currentmatch.value.oppositions.B.length) {
                    B=currentmatch.value.oppositions.B[i]
                }
                if (i<currentmatch.value.oppositions.Autres.length) {
                    Autres=currentmatch.value.oppositions.Autres[i]
                }
                tabselections.value.push([A,B,Autres])
            }
        }
        console.log("oppositions : ",tabselections.value)
    }


    onBeforeUnmount(() => {
        debouncedOnChange.cancel();
    });

    watch(() => props.matchdetail, (nouvelleValeur) => {
        currentmatch.value = nouvelleValeur
        updatetabselections()
    });


</script>



<style scoped>
table {
    width:100%;
}

td {
    text-align:left;
}
th {
    text-align:left;
}


.joueurs {
    margin-left : 10px;
    width:100%;
}

.main {
    height:auto;
}

</style>