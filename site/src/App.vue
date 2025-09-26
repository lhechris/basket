<template>
  <nav class="menu">
    <ul>
    <li><router-link to="/">Disponibilité</router-link></li>
    <li v-if="logged" ><router-link to="/entrainement">Entrainement</router-link> | </li>
    <li v-if="logged" ><router-link to="/selection">Selection</router-link> | </li>
    <li v-if="logged" ><router-link to="/matchs">Matchs</router-link> | </li>
    <li v-if="logged" ><router-link to="/joueuses">Joueuses</router-link> | </li>
    <li><router-link to="/auth">
      <span v-if="logged" >Logout</span>
      <span v-else>Login</span>
    </router-link></li>
    </ul>
  </nav>
  <div class="conteneur">
      <router-view/>
  </div>
</template>

<script>

import {islogged} from "./js/api.js"
import {ref} from "vue"

export default {

  setup() {
    const logged=ref(false)

    islogged().then( r=> {
      logged.value=r==1
    })

    return {logged}

  }
}

</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}

nav {
  padding: 10px;
}

nav a {
  font-weight: bold;
  color: #2c3e50;
}

nav a.router-link-exact-active {
  color: #42b983;
}
</style>
