<template>
    <div class="flex flex-col gap-4 mb-4">
        <div class="bg-teal-500 rounded-lg pt-2 pb-2">
            <span class="titre">{{matchdetail.titre}}</span><span v-if="currentmatch.horaire">({{currentmatch.horaire}})</span><span>&nbsp;Equipe {{matchdetail.equipe }}</span>
        </div>
        <div class="text-xl flex flex-col gap-4">
            <div class="flex gap-2" v-if="currentmatch.score!=''">
                <div class="w-30 text-left">Score</div>
                <div>{{currentmatch.score}}</div>
            </div>
            <div class="flex gap-2" v-if="currentmatch.collation.length>0">
                <div class="w-30 text-left">Collation</div>
                <div><span v-for="(j,n) in currentmatch.collation"><span v-if="n>0">&nbsp;/&nbsp;</span>{{j.prenom}}</span></div>
            </div>
            <div class="flex gap-2" v-if="currentmatch.otm.length>0"> 
                <div class="w-30 text-left">OTM</div>
                <div><span v-for="(otm,n) in currentmatch.otm"><span v-if="n>0">&nbsp;/&nbsp;</span>{{otm.prenom}}</span></div>
            </div>
            <div class="flex gap-20" v-if="currentmatch.maillots.length>0">
                <div class="w-30 text-left">Maillots</div>
                <div><span v-for="(j,n) in currentmatch.maillots"><span v-if="n>0">&nbsp;/&nbsp;</span>{{j.prenom}}</span></div>
            </div>
            <div class="flex gap-2">
                <div class="w-30 text-left">Lieu</div>
                <div>
                    <a v-if="currentmatch.lien" target="_blank" :href="currentmatch.lien" >
                        <img src="../assets/destination.png" />
                    </a>
                </div>
                <div>{{currentmatch.adresse}}</div>
            </div>
            <div class="flex gap-2">
                <div class="w-30 text-left">Rendez-vous</div>
                <div class="font-bold">{{currentmatch.rendezvous}}</div>
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

    
    let adresse = currentmatch.value.adresse
    currentmatch.value.lien = null
    let i = adresse.indexOf("http")
    if (i>=0) {
        currentmatch.value.adresse = adresse.substring(0,i)
        currentmatch.value.lien = adresse.substring(i)
    }

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