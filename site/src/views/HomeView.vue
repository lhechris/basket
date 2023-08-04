<template>
  <div class="home">
    <!--<img alt="Vue logo" src="../assets/logo.png">-->
    <Matchs />
  </div>
</template>

<script>
// @ is an alias to /src
import Matchs from '@/components/Matchs.vue'

export default {
  name: 'HomeView',
  components: {
    Matchs
  }
}
</script>
