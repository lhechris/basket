<template>
  <nav class="p-1 text-xs md:text-base font-bold">
    <ul class="p-0 m-0 flex flex-wrap justify-center bg-black">
    <li class="mx-2 my-1" ><router-link to="/" class="">Disponibilité</router-link></li>
    <li class="mx-2 my-1" v-if="logged" ><router-link to="/entrainement">Entrainement</router-link> | </li>
    <li class="mx-2 my-1" v-if="logged" ><router-link to="/selection">Selection</router-link> | </li>
    <li class="mx-2 my-1" v-if="logged" ><router-link to="/matchsadmin">Matchs</router-link> | </li>
    <li class="mx-2 my-1" v-else><router-link to="/matchs">Matchs</router-link></li>
    <li class="mx-2 my-1" v-if="logged" ><router-link to="/joueuses">Joueuses</router-link> | </li>    
    <li class="mx-2 my-1"><router-link to="/auth">
      <span v-if="logged" >Logout</span>
      <span v-else>Login</span>
    </router-link></li>
    </ul>
  </nav>
  <div class="@container">
      <router-view/>
  </div>

  <div class="h-24"></div>



</template>

<script setup>

import {islogged} from "./js/api.js"
import {ref} from "vue"

const logged=ref(false)

islogged().then( r=> {
  logged.value=r==1
})

</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  
}

nav a.router-link-exact-active {
  color: #42b983;
}

nav a {
    float: left;
    display: block;
    color: white;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;    
}

nav a:hover {
    background-color: #ddd;
    color: black;
}

</style>
