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
            setUsers(joueuses.value).then( u => {
                joueuses.value = u
            })
        }

        return {joueuses,supprime,ajoute,enregistrer}
    }
  }
  </script>

<style scoped>
.main {
    display:block;
    margin-left:auto;
    margin-right:auto;    
    width: 400px;
    height : 500px;
    /*overflow : scroll;
    scrollbar-color: rebeccapurple green;
    scrollbar-width: thin;*/
    /*background-color:chocolate;*/
}

.disabled {
    background-color: grey;
}

.inputnom {
    width:100px;
}
.inputbool {
    width:20px;
}

.custom-checkbox {
  display: block;
  position: relative;
  padding-left: 30px;
  margin-bottom: 20px;
  cursor: pointer;
  font-size: 1em;
  user-select: none;
}

.custom-checkbox input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 20px;
  width: 20px;
  background-color: #eee;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.custom-checkbox:hover input ~ .checkmark {
  background-color: #ccc;
}

.custom-checkbox input:checked ~ .checkmark {
  background-color: #4CAF50; /* Couleur de fond quand coché */
}

.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

.custom-checkbox input:checked ~ .checkmark:after {
  display: block;
}

.custom-checkbox .checkmark:after {
  left: 7px;
  top: 3px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}



</style>