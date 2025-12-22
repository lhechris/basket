<template>
    <div class="flex md:grid md:grid-cols-6 flex-col">
        <div class="md:col-span-4 md:col-start-2" >
            <div class="flex flex-col gap-4 text-xl">
                
                <!-- ENTETE -->
                <div class="bg-teal-500 rounded-lg pt-2 pb-2">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pl-2">
                        <span class="font-bold">{{info.titre}}</span>
                        <span>&nbsp;Equipe {{info.equipe}}</span>
                        <span>{{displaydate(info.jour)}} {{ info.horaire }}</span>
                    </div>
                </div>

                <!-- Contenu -->
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col text-lg" v-if="info.oppositions">
                        <div class="font-bold bg-gray-200">Opposition B</div>
                        <div class="flex flex-col gap-2">
                            <div class="flex gap-2 text-left" v-for="opp of info.oppositions.B">
                                <span class="min-w-25">{{ opp.licence }}</span>
                                <span class="min-w-40">{{opp.nom}}</span>
                                <span class="min-w-20">{{ opp.prenom }}</span>
                                <span >{{ opp.numero }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col text-lg" v-if="info.oppositions">
                        <div class="font-bold bg-gray-200">Opposition A</div>
                        <div class="flex flex-col gap-2 ">
                            <div class="flex gap-2 text-left" v-for="opp of info.oppositions.A">
                                <span class="min-w-25">{{ opp.licence }}</span>
                                <span class="min-w-40">{{opp.nom}}</span>
                                <span class="min-w-20">{{ opp.prenom }}</span>
                                <span >{{ opp.numero }}</span>
                            </div>
                        </div>
                    </div>
                </div>        
            </div>

        </div>
    </div>
</template>

<script setup>
  import {displaydate} from '../js/api.js'
  import Content from '@/components/Content.vue'

  const props = defineProps (['info' ])
  const emit = defineEmits(['change'])

</script>