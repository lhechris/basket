<template>
    <div class="home">
      <matchs />
    </div>
  </template>
  
  <script>
  // @ is an alias to /src
  import Matchs from '@/components/Matchs.vue'
  
  export default {
    name: 'MatchsView',
    components: {
      Matchs
    }
  }
  </script>
  