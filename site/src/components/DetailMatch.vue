<template>
    <div class="matchs">
        <table>
            <thead>

            </thead>
            <tbody>
            <tr> <td>Rencontre</td><td>{{matchdetail.titre}}</td></tr>
            <tr> <td>Jour</td><td>{{matchdetail.jour }}</td></tr>            
            <tr> <td>Equipe</td><td>{{matchdetail.equipe }}</td></tr>
            <tr> <td>Score</td><td>{{matchdetail.score }}</td></tr>
            <tr> <td>Collation</td><td>{{matchdetail.Collation }}</td></tr>
            <tr> <td>OTM</td><td>{{matchdetail.otm }}</td></tr>
            <tr> <td>Maillots</td><td>{{matchdetail.maillots }}</td></tr>
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
</template>

<script>
  export default {
    name: 'DetailMatch',

    props : ['matchdetail'],

}

</script>



<style scoped>

</style>