<template>
  <nav>
    <router-link to="/">Home</router-link> |
    <span v-if="logged" ><router-link to="/selection">Selection</router-link> | </span>
    <router-link to="/auth">
      <span v-if="logged" >Logout</span>
      <span v-else>Login</span>
    </router-link>
  </nav>
  <router-view/>
</template>

<script>

import {islogged} from "@/js/api.js"
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
  padding: 30px;
}

nav a {
  font-weight: bold;
  color: #2c3e50;
}

nav a.router-link-exact-active {
  color: #42b983;
}
</style>
