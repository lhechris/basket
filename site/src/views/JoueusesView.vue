<template>
    <div class="main">        
        <table>
            <thead>
                <tr><th>Prenom</th><th>Nom</th><th>Equipe</th><th>Licence</th><th>Charte</th><th>OTM</th></tr>
            </thead>
            <tbody>
            <tr v-for="(u,i) in joueuses" :key="i" >
            <td><input v-model="u.prenom" class="inputnom" :class="u.todelete==true ? 'disabled' : ''"/></td>
            <td><input v-model="u.nom" class="inputnom" :class="u.todelete==true ? 'disabled' : ''"/></td>
            <td><input v-model="u.equipe"  class="inputbool" :class="u.todelete==true ? 'disabled' : ''"/></td>
            <td><input v-model="u.licence" class="inputnom" :class="u.todelete==true ? 'disabled' : ''"/></td>
            <td>
                <label class="custom-checkbox" :class="u.todelete==true ? 'disabled' : ''">
                    <input type="checkbox" v-model="u.charte" >
                    <span class="checkmark"></span>                    
                </label>
            </td>
            <td>
                <label class="custom-checkbox" :class="u.todelete==true ? 'disabled' : ''">
                    <input type="checkbox" v-model="u.otm" >
                    <span class="checkmark"></span>                    
                </label>
            </td>
            <td>
                <button class="btn btn-delete" @click="supprime(u.id)">
                    <img src= "@/assets/annuler.png" width="16"/>
                </button>
            </td>
            </tr>
            </tbody>
        </table>
        <button class="btn btn-primary" @click="ajoute()">Nouveau</button>
        <button class="btn btn-secondary" @click="enregistrer()">Enregistrer</button>
    
    </div>

</template>

  <script>
  // @ is an alias to /src
    import {getUsers,setUsers} from '@/js/api.js'
  import {ref} from "vue"
  
  export default {
    name: 'JoueusesView',
    components: {
      
    },

    setup() {
        const joueuses = ref([])

        getUsers().then( u => {
            joueuses.value = u
        })

        function supprime(id) {
            let indice=-1;
            joueuses.value.forEach( (e,k) => {
                if (e.id == id) { indice = k;}
            } )
            
            if (indice>=0) {
                joueuses.value[indice]["todelete"]= true;
            }
        }

        function ajoute() {
            joueuses.value.push({prenom:"", equipe:"1"})
        }

        function enregistrer() {
            console.log(joueuses.value)
            setUsers(joueuses.value).then( u => {
                joueuses.value = u
            })
        }

        return {joueuses,supprime,ajoute,enregistrer}
    }
  }
  </script>

<!--  <style scoped>
.main {
    display:flex;
    margin-left:auto;
    margin-right:auto;    
    width: 400px;
    height : 500px;
    align-items:center;
    /*overflow : scroll;
    scrollbar-color: rebeccapurple green;
    scrollbar-width: thin;*/
}
.inputdate {
    width:6em;
}

.inputresultat {
    width:4em;

}

button {
    border-radius: 20%;
    background-color: coral;
}

.btndelete {
    border-radius: 4px;
    margin : 0;
    padding : 2px;
    background-color:  white;
}

.disabled {
    background-color: grey;
}
input {
    border-radius : 3px;
    background-color: rgb(141, 228, 228);
}
</style>-->