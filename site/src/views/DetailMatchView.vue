<template>
    <div class="home">
      <DetailMatch :matchdetail="match" @changeOpp="updateOpp" @change-match="updateMatch"/> 
  
    </div>
  </template>
  
  <script>
  // @ is an alias to /src
  import DetailMatch from '@/components/DetailMatch.vue'
  import {getMatch,setOpposition} from '@/js/api.js'
   import {ref} from "vue"
  
  export default {
    name: 'DetailMatchsView',
    components: {
      DetailMatch
    },
    props : ['id'],

    setup(props) {


        const match = ref([])

        function refreshMatch() {
          getMatch(props.id).then( m => {
              match.value = m
          })
        }

        function updateMatch(newmatch) {
          console.log("Update match ",newmatch)
        }

        function updateOpp(matchid,userid,val) {
            setOpposition(matchid,userid,val);
            refreshMatch();      
           
        } 

        refreshMatch();

        return {match,updateOpp,updateMatch}
    }
  }
  </script>
  