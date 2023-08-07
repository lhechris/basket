<template>
  <div class="home">
    <!--<img alt="Vue logo" src="../assets/logo.png">-->
    <matchs-dispo />
  </div>
</template>

<script>
// @ is an alias to /src
import MatchsDispo from '@/components/MatchsDispo.vue'

export default {
  name: 'HomeView',
  components: {
    MatchsDispo
  }
}
</script>
