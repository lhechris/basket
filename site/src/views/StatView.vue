<template>
    <div class="w-full mt-2">
        <Statistics :players="players"/>
    </div>
  </template>
  
<script setup>
  import Statistics from '@/components/Statistics.vue'
  import {ref} from 'vue'
  import {getStats} from '../js/api.js'

  const players = ref([
    { "id" : "1", "prenom" : "riri", "collationCount" : 2, "maillotsCount" : 1 , "matchCount" : 10},
    { "id" : "1", "prenom" : "fifi", "collationCount" : 0, "maillotsCount" : 1 , "matchCount" : 10},
    { "id" : "1", "prenom" : "loulou", "collationCount" : 1, "maillotsCount" : 1 , "matchCount" : 10},
    { "id" : "1", "prenom" : "nifnif", "collationCount" : 3, "maillotsCount" : 1 , "matchCount" : 10},
    { "id" : "1", "prenom" : "nafnaf", "collationCount" : 0, "maillotsCount" : 1 , "matchCount" : 10},
    { "id" : "1", "prenom" : "noufnouf", "collationCount" : 0, "maillotsCount" : 1 , "matchCount" : 10},
])

  getStats().then( u => {
        players.value = u
  })

</script>