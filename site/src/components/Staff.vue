<template>
    <div class="w-full py-6 px-4 sm:px-6">
        <div class="space-y-6">
            <!-- Header -->
            <div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">Gestion du Staff</h1>
                <p class="text-gray-600 text-sm sm:text-base mt-1">Gérez les membres de votre staff sportif</p>
            </div>

            <!-- Form -->
            <form @submit.prevent="addStaff" class="w-full bg-white rounded-lg shadow p-4 sm:p-6 border border-gray-200">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
                    <div>
                        <input 
                            v-model="form.nom" 
                            placeholder="Nom" 
                            required 
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        />
                    </div>
                    <div>
                        <input 
                            v-model="form.prenom" 
                            placeholder="Prénom" 
                            required 
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        />
                    </div>
                    <div>
                        <input 
                            v-model="form.licence" 
                            placeholder="Licence" 
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        />
                    </div>
                    <div>
                        <input 
                            v-model="form.role" 
                            placeholder="Rôle" 
                            required 
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        />
                    </div>
                    <div>
                        <button 
                            type="submit" 
                            class="w-full px-4 py-2 sm:py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm sm:text-base font-semibold rounded-md transition duration-200"
                        >
                            Ajouter
                        </button>
                    </div>
                </div>
            </form>

            <!-- Table - Desktop and Tablet -->
            <div class="w-full hidden sm:block bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-base font-semibold text-gray-700">Nom</th>
                                <th class="px-6 py-4 text-left text-base font-semibold text-gray-700">Prénom</th>
                                <th class="px-6 py-4 text-left text-base font-semibold text-gray-700">Licence</th>
                                <th class="px-6 py-4 text-left text-base font-semibold text-gray-700">Rôle</th>
                                <th class="px-6 py-4 text-left text-base font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="member in props.value" :key="member.id" class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-base text-gray-900">
                                    <input v-if="editingId === member.id" v-model="editForm.nom" class="w-full px-2 py-1 border border-gray-300 rounded" />
                                    <span v-else>{{ member.nom }}</span>
                                </td>
                                <td class="px-6 py-4 text-base text-gray-600">
                                    <input v-if="editingId === member.id" v-model="editForm.prenom" class="w-full px-2 py-1 border border-gray-300 rounded" />
                                    <span v-else>{{ member.prenom }}</span>
                                </td>
                                <td class="px-6 py-4 text-base text-gray-600">
                                    <input v-if="editingId === member.id" v-model="editForm.licence" class="w-full px-2 py-1 border border-gray-300 rounded" />
                                    <span v-else>{{ member.licence }}</span>
                                </td>
                                <td class="px-6 py-4 text-base text-gray-600">
                                    <input v-if="editingId === member.id" v-model="editForm.role" class="w-full px-2 py-1 border border-gray-300 rounded" />
                                    <span v-else>{{ member.role }}</span>
                                </td>
                                <td class="px-6 py-4 space-x-2 flex">
                                    <button 
                                        v-if="editingId === member.id"
                                        @click="saveEdit(member.id)" 
                                        class="px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 font-semibold text-sm rounded transition"
                                    >
                                        Sauvegarder
                                    </button>
                                    <button 
                                        v-if="editingId === member.id"
                                        @click="cancelEdit" 
                                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded transition"
                                    >
                                        Annuler
                                    </button>
                                    <button 
                                        v-if="editingId !== member.id"
                                        @click="startEdit(member)" 
                                        class="px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold text-sm rounded transition"
                                    >
                                        Modifier
                                    </button>
                                    <button 
                                        v-if="editingId !== member.id"
                                        @click="deleteMember(member.id)" 
                                        class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-semibold text-sm rounded transition"
                                    >
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="props.value.length === 0" class="px-6 py-8 text-center text-base text-gray-500">
                    Aucun membre staff ajouté
                </div>
            </div>

            <!-- Cards - Mobile View -->
            <div class="w-full sm:hidden space-y-3">
                <div v-if="props.value.length === 0" class="bg-white rounded-lg shadow p-4 text-center text-sm text-gray-500">
                    Aucun membre staff ajouté
                </div>
                <div 
                    v-for="member in props.value" 
                    :key="member.id" 
                    class="bg-white rounded-lg shadow p-4 border border-gray-200 space-y-3"
                >
                    <div class="space-y-2">
                        <div class="flex justify-between gap-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Nom</p>
                                <p class="font-semibold text-gray-900">{{ member.nom }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Prénom</p>
                                <p class="font-semibold text-gray-900">{{ member.prenom }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Licence</p>
                                <p class="text-sm text-gray-900">{{ member.licence }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Rôle</p>
                                <p class="text-sm text-gray-900">{{ member.role }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2 pt-3 border-t border-gray-200">
                        <button 
                            @click="editMember(member)" 
                            class="flex-1 px-3 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold rounded text-xs transition"
                        >
                            Modifier
                        </button>
                        <button 
                            @click="deleteMember(member)" 
                            class="flex-1 px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-semibold rounded text-xs transition"
                        >
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const form = ref({ nom: '', prenom: '', licence: '', role: '' })
const editForm = ref({ nom: '', prenom: '', licence: '', role: '' })
const editingId = ref(null)

const emit = defineEmits(['onChange','onAdd','onDelete','onUpdate'])
const props= defineProps(['value'])

// const staff = ref(props.value || [])
// console.log(staff.value)

const addStaff = () => {
    emit('onAdd',form.value)
    // staff.value.push({ id: nextId.value++, ...form.value })
    form.value = { nom: '', prenom: '', licence: '', role: '' }
}

const startEdit = (member) => {
    editingId.value = member.id
    editForm.value = { ...member }
}

const saveEdit = (id) => {
    emit('onUpdate', { id, data: editForm.value })
    editingId.value = null
}

const cancelEdit = () => {
    editingId.value = null
}

const editMember = (member) => {
    form.value = { ...member }
    emit('onDelete', member.id)
    emit('onChange', member)
}

const deleteMember = (id) => {
    emit('onDelete', id)
}
</script>

