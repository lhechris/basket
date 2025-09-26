<template>
    <div class="matchs">
        <div class="descr">
            <span class="titre">{{matchdetail.titre}}</span><span>&nbsp;Equipe {{matchdetail.equipe }}</span>
            <p>{{matchdetail.jour }}</p>
        </div>
        <div class="main">
            <table>
                <thead>
                </thead>
                <tbody>
                <tr> <td>Score</td><td><input type="text" v-model="currentmatch.score" @input="debouncedOnChange()"/></td></tr>
                <tr> <td>Collation</td><td><input type="text" v-model="currentmatch.Collation" @input="debouncedOnChange()"/></td></tr>
                <tr> <td>OTM</td><td><input type="text" v-model="currentmatch.otm" @input="debouncedOnChange()"/></td></tr>
                <tr> <td>Maillots</td><td><input type="text" v-model="currentmatch.maillots" @input="debouncedOnChange()"/></td></tr>
                </tbody>
            </table>
            <table v-if="matchdetail.oppositions">            
                <thead><tr><th>Opposition A</th><th>Opposition B</th><th></th></tr></thead>
                <tbody>
                    <tr>
                        <td>
                            <p v-for="u in matchdetail.oppositions.A" :key="u.user">
                                <span>{{ u.prenom }}</span>
                                <select @change="$emit('changeOpp',matchdetail.id,u.user,$event.target.value)">
                                    <option value=""> </option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                </select>
                            </p>
                        </td>
                        <td>
                            <p v-for="u in matchdetail.oppositions.B" :key="u.user">
                                <span>{{ u.prenom }}</span>
                                <select @change="$emit('changeOpp',matchdetail.id,u.user,$event.target.value)">
                                    <option value=""> </option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                </select>
                            </p>
                        </td>
                        <td>
                            <p v-for="u in matchdetail.oppositions.Autres" :key="u.user">{{ u.prenom }}
                                <select @change="$emit('changeOpp',matchdetail.id,u.user,$event.target.value)">
                                    <option value=""> </option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                </select>
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
    import {ref,watch} from 'vue'
    import {onBeforeUnmount} from 'vue'
    import { debounce } from 'lodash';


    const props = defineProps (['matchdetail' ])
    const emit = defineEmits(['changeMatch','changeOpp'])

    const currentmatch = ref(props.matchdetail)


    const onChange = () => {
        emit('changeMatch',currentmatch.value)
    }
        
    const debouncedOnChange = debounce(onChange, 2000);


    onBeforeUnmount(() => {
        debouncedOnChange.cancel();
    });

    watch(() => props.matchdetail, (nouvelleValeur) => {
    currentmatch.value = nouvelleValeur
    });

</script>



<style scoped>
table {
    width:100%;
}
td {
    text-align:left;
}
</style>