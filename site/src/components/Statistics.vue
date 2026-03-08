<template>
    <div class="statistics max-w-4xl mx-auto p-6 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl shadow-lg">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">📊 Statistiques</h2>
        
        <div class="grid md:grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Collation par joueur -->
            <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-green-500">
                <h3 class="text-xl font-semibold text-gray-700 mb-4 flex items-center">
                    <span class="text-green-500 mr-2">🍽️</span> Collations 
                </h3>
                <div class="space-y-2">
                    <div v-for="player in players" :key="player.id" class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="font-medium text-gray-800">{{ player.prenom  }}</span>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-semibold">{{ player.collationCount || 0 }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Maillots par joueur -->
            <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-500">
                <h3 class="text-xl font-semibold text-gray-700 mb-4 flex items-center">
                    <span class="text-blue-500 mr-2">👕</span> Maillots
                </h3>
                <div class="space-y-2">
                    <div v-for="player in players" :key="player.id" class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="font-medium text-gray-800">{{ player.prenom  }}</span>
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm font-semibold">{{ player.maillotsCount || 0 }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Matchs joués par joueuse -->
            <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-pink-500">
                <h3 class="text-xl font-semibold text-gray-700 mb-4 flex items-center">
                    <span class="text-pink-500 mr-2">⚽</span> Matchs Joués
                </h3>
                <div class="space-y-2">
                    <div v-for="player in femalePlayers" :key="player.id" class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="font-medium text-gray-800">{{ player.prenom  }}</span>
                        <span class="bg-pink-100 text-pink-800 px-2 py-1 rounded-full text-sm font-semibold">{{ player.matchCount || 0 }}</span>
                    </div>
                </div>
                <p v-if="femalePlayers.length === 0" class="text-gray-500 text-sm mt-4">Aucune joueuse trouvée</p>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { computed } from 'vue'

    const props = defineProps({
        players: {
            type: Array,
            default: () => []
        }
    })

    const femalePlayers = computed(() => {
        return props.players
    })
</script>

<style scoped>
    /* Aucun style supplémentaire nécessaire - tout est géré par Tailwind CSS */
</style>