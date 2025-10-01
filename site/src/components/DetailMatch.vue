<template>
    <div class="matchs">
        <div class="descr bg-1">
            <span class="titre">{{matchdetail.titre}}</span><span v-if="currentmatch.horaire">({{currentmatch.horaire}})</span><span>&nbsp;Equipe {{matchdetail.equipe }}</span>
        </div>
        <div class="main">
            <table>
                <thead>
                </thead>
                <tbody>
                <tr v-if="currentmatch.score!=''"> <td>Score</td><td>{{currentmatch.score}}</td></tr>
                <tr v-if="currentmatch.collation!='N/A'"> <td>Collation</td><td> {{currentmatch.collation}}</td></tr>
                <tr v-if="currentmatch.otm!='N/A'"> <td>OTM</td><td>{{currentmatch.otm}}</td></tr>
                <tr> <td>Maillots</td><td>{{currentmatch.maillots}}</td></tr>
                <tr> <td>Lieu</td><td>{{currentmatch.adresse}}</td></tr>
                <tr> <td>Rendez-vous</td><td class="gras">{{currentmatch.rendezvous}}</td></tr>                
                </tbody>
            </table>
            <span class="joueurs" v-for="u in matchdetail.selections" :key="u.user">{{ u.prenom }}</span>
            
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


    const onChange = () => {
        emit('changeMatch',currentmatch.value)
    }
        
    const debouncedOnChange = debounce(onChange, 2000);


    onBeforeUnmount(() => {
        debouncedOnChange.cancel();
    });

    watch(() => props.matchdetail, (nouvelleValeur) => {
    currentmatch.value = nouvelleValeur
    });

</script>



<style scoped>
table {
    width:100%;
}
td {
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