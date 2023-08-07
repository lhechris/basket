<template>
    <div class="home">
      <!--<img alt="Vue logo" src="../assets/logo.png">-->
      <matchs-selection />
    </div>
  </template>
  
  <script>
  // @ is an alias to /src
  import MatchsSelection from '@/components/MatchsSelection.vue'
  
  export default {
    name: 'SelectionView',
    components: {
      MatchsSelection
    }
  }
  </script>
  