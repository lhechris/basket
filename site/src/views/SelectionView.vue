<template>
    <div class="flex md:grid md:grid-cols-10 flex-col">
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
  