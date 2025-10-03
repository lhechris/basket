<template>
    <div class="flex flex-col gap-4 text-xl">
        <div class="bg-teal-500 rounded-lg pt-2 pb-2">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pl-2">
                <span class="font-bold"><input class="pl-2" v-model="currentmatch.titre"  @input="debouncedOnChange()"/></span>
                <span>&nbsp;Equipe<input class="max-w-10" v-model="currentmatch.equipe"  @input="debouncedOnChange()"/></span>
                <input class="max-w-30 p-1" v-model="currentmatch.jour" />
                <span>
                    <button class="middle none center mr-4 rounded-lg bg-red-500 py-1 px-2 font-sans text-xs font-bold uppercase text-white shadow-md shadow-red-500/20 transition-all hover:shadow-lg hover:shadow-red-500/40 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none" 
                            @click="supprime()">Supprimer
                    </button>
                </span>
            </div>            

        </div>
        <div class="flex flex-col gap-2">
            <div class="flex gap-2"><div class="text-left w-30">Score</div><input  v-model="currentmatch.score"  @input="debouncedOnChange()"/></div>
            <div class="flex gap-2"><div class="text-left w-30">Collation</div><input v-model="currentmatch.collation"  @input="debouncedOnChange()"/></div>
            <div class="flex gap-2"><div class="text-left w-30">OTM</div><input v-model="currentmatch.otm"  @input="debouncedOnChange()"/></div>
            <div class="flex gap-2"><div class="text-left w-30">Maillots</div><input v-model="currentmatch.maillots"  @input="debouncedOnChange()"/></div>
            <div class="flex gap-2"><div class="text-left w-30">Adresse</div><input v-model="currentmatch.adresse"  @input="debouncedOnChange()"/></div>
            <div class="flex gap-2"><div class="text-left w-30">Horaire</div><input v-model="currentmatch.horaire"  @input="debouncedOnChange()"/></div>
            <div class="flex gap-2"><div class="text-left w-30">Rendez-vous</div><input v-model="currentmatch.rendezvous"  @input="debouncedOnChange()"/></div>
            <div class="grid grid-cols-8 text-lg">
                <div class="font-bold text-left col-span-3">Opposition A</div><div class="font-bold text-left col-span-3">Opposition B</div><div class="font-bold col-span-2">-</div>
                <div class="flex flex-col gap-2 col-span-3">
                    <div class="grid grid-cols-12 gap-2 text-left" v-for="opp of currentmatch.oppositions.A">
                        <span class="col-span-5">{{opp.prenom}}</span>
                        <span class="col-span-5">{{ opp.licence }}</span>
                        <button class="col-span-2" @click="updateOpp(currentmatch.id,opp.user,'B','A')">
                            <img src= "@/assets/fleche_droite.png" width="16"/>
                        </button>
                    </div>
                </div>
                <div class="flex flex-col gap-2 col-span-3">
                    <div class="grid grid-cols-12 text-left" v-for="opp of currentmatch.oppositions.B">
                        <button class="col-span-2" @click="updateOpp(currentmatch.id,opp.user,'A','B')">
                            <img width="16" src= "@/assets/fleche_gauche.png" />
                        </button>
                        <span class="col-span-5">{{opp.prenom}}</span>
                        <span class="col-span-5">{{ opp.licence }}</span>
                    </div>
                </div>
                <div class="flex flex-col gap-2 col-span-2">
                    <div class="grid grid-cols-3" v-for="opp of currentmatch.oppositions.Autres">
                        <span>{{opp.prenom}}</span>
                        <button2-choix val="0" texte1="A" texte2="B" val1="A" val2="B" @onUpdate="updateOpp(currentmatch.id,opp.user,$event,'Autres')"></button2-choix>
                    </div>
                </div>
            </div>        
        </div>        
    </div>
</template>

<script setup>
    import {ref,watch} from 'vue'
    import {onBeforeUnmount} from 'vue'
    import { debounce } from 'lodash';
    //import tableauoppositions from './tableauoppositions.vue';
    import Button2Choix from './Button2Choix.vue';

    const props = defineProps (['matchdetail' ])
    const emit = defineEmits(['changeMatch','changeOpp'])

    const currentmatch = ref(props.matchdetail)

    function supprime() {
        const result = confirm("Voulez vous vraiment supprimer le match ?")
        if (result) {
            currentmatch.value["todelete"]= true;
            emit('changeMatch',currentmatch.value)
        }
    }

    function updateOpp(matchid,userid,val,prev) {
        let from = null
        let arr = null
        console.log(matchid,userid,val)
        if (prev=='A') {
            arr = currentmatch.value.oppositions.A.filter( (item) => {
                    if (item.user==userid) {                    
                        from=item
                        return false
                    }
                    return true
                })
            currentmatch.value.oppositions.A = arr
        }
        if (prev=='B') {
            arr = currentmatch.value.oppositions.B.filter( (item) => {
                    if (item.user==userid) {                    
                        from=item
                        return false
                    }
                    return true
                })
            currentmatch.value.oppositions.B = arr
        }
        if (prev=='Autres') {
            arr = currentmatch.value.oppositions.Autres.filter( (item) => {
                    if (item.user==userid) {                    
                        from=item
                        return false
                    }
                    return true
                })
            currentmatch.value.oppositions.Autres = arr
        }
        if (val=='A') {currentmatch.value.oppositions.A.push(from)}
        if (val=='B') {currentmatch.value.oppositions.B.push(from)}
        if (val=='Autres') {currentmatch.value.oppositions.Autres.push(from)}

        console.log("from:",from," newarray:",arr)

        emit('changeOpp',matchid,userid,val)

    }


    const onChange = () => {
        emit('changeMatch',currentmatch.value)
    }
        
    const debouncedOnChange = debounce(onChange, 1000);

    onBeforeUnmount(() => {
        debouncedOnChange.cancel();
    });

    watch(() => props.matchdetail, (nouvelleValeur) => {
        currentmatch.value = nouvelleValeur
    });


</script>



<style scoped>

.radio input ~ label {
  background-color: rgb(233, 225, 225);
  color: rgb(158, 146, 146);
}
.radio input:checked ~ label {
  background-color: rgb(70, 230, 22);
  color: white;
}

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