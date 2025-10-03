<template>
    <div class="flex flex-col gap-4 ">
        <div class="bg-teal-500 rounded-lg pt-2 pb-2">
            <span class="titre">{{matchdetail.titre}}</span><span v-if="currentmatch.horaire">({{currentmatch.horaire}})</span><span>&nbsp;Equipe {{matchdetail.equipe }}</span>
        </div>
        <div class="text-xl flex flex-col gap-4">
            <div class="flex gap-2" v-if="currentmatch.score!=''">
                <div class="w-30 text-left">Score</div>
                <div>{{currentmatch.score}}</div>
            </div>
            <div class="flex gap-2" v-if="currentmatch.collation!='N/A'">
                <div class="w-30 text-left">Collation</div>
                <div>{{currentmatch.collation}}</div>
            </div>
            <div class="flex gap-2" v-if="currentmatch.otm!='N/A'"> 
                <div class="w-30 text-left">OTM</div>
                <div>{{currentmatch.otm}}</div>
            </div>
            <div class="flex gap-2">
                <div class="w-30 text-left">Maillots</div>
                <div>{{currentmatch.maillots}}</div>
            </div>
            <div class="flex gap-2">
                <div class="w-30 text-left">Lieu</div>
                <div>{{currentmatch.adresse}}</div>
            </div>
            <div class="flex gap-2">
                <div class="w-30 text-left">Rendez-vous</div>
                <div class="gras">{{currentmatch.rendezvous}}</div>
            </div>                
            
            <div class="grid grid-cols-5 md:grid-cols-10 gap-1">
                <div class="text-left" v-for="u in matchdetail.selections" :key="u.user">{{ u.prenom }}</div>
            </div>
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