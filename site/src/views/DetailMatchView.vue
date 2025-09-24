<template>
    <div class="home">
      <DetailMatch :matchdetail="match" @changeOpp="updateOpp"/> 
  
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


        function updateOpp(matchid,userid,val) {
            setOpposition(matchid,userid,val);
            refreshMatch();      
           
        } 

        refreshMatch();

        return {match,updateOpp}
    }
  }
  </script>
  