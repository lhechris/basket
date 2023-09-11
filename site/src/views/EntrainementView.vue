<template>
    <div class="home">
      <!--<img alt="Vue logo" src="../assets/logo.png">-->
      <entrainement-presence />
    </div>
  </template>
  
  <script>
  // @ is an alias to /src
  import EntrainementPresence from '@/components/EntrainementPresence.vue'
  
  export default {
    name: 'EntrainementView',
    components: {
      EntrainementPresence
    }
  }
  </script>
  