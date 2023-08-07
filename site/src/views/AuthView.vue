<template>
    <div class="home">
      <!--<img alt="Vue logo" src="../assets/logo.png">-->
      <auth />
    </div>
  </template>
  
  <script>
  // @ is an alias to /src
  import Auth from '@/components/Auth.vue'
  
  export default {
    name: 'AuthView',
    components: {
      Auth
    }
  }
  </script>