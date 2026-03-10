<template>
    <div class="w-full min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <!-- Buttons Section -->
        <div class="flex justify-center items-center py-8">
            <div class="flex gap-6">
                <button 
                    @click="display=1"
                    :class="display === 1 ? 'btn-active' : 'btn-inactive'"
                    class="btn-toggle"
                >
                    <span class="text-2xl mr-3">👥</span>
                    Joueuses
                </button>
                <button 
                    @click="display=2"
                    :class="display === 2 ? 'btn-active' : 'btn-inactive'"
                    class="btn-toggle"
                >
                    <span class="text-2xl mr-3">👔</span>
                    Staff
                </button>
            </div>
        </div>

        <!-- Content Section -->
        <div class="px-4">
            <EditableTable
                v-if="display==1"
                :value="players"
                :fields="playerFields"
                :new-item="playerTemplate"
                @onSave="enregistrer($event)"
            >
                <template #header>
                    <div class="mb-4 md:mb-8">
                        <h1 class="text-2xl md:text-4xl font-bold text-gray-800 mb-1 md:mb-2">👥 Gestion des Joueuses</h1>
                        <p class="text-xs md:text-base text-gray-600">Gérez les informations de vos joueuses</p>
                    </div>
                </template>
            </EditableTable>
            <EditableTable
                v-if="display==2"
                :value="staffdata"
                :fields="staffFields"
                :new-item="staffTemplate"
                @onSave="saveStaff($event)"
            >
                <template #header>
                    <div class="mb-4 md:mb-8">
                        <h1 class="text-2xl md:text-4xl font-bold text-gray-800 mb-1 md:mb-2">👔 Gestion du Staff</h1>
                        <p class="text-xs md:text-base text-gray-600">Gérez les membres du staff</p>
                    </div>
                </template>
            </EditableTable>
        </div>
    </div>
</template>

 <script setup>
    // @ is an alias to /src
    import {getUsers,setUsers,getStaff,setStaff} from '@/js/api.js'
    import {ref} from "vue"
    import EditableTable from '../components/EditableTable.vue'

    const players = ref([])
    const display = ref(1)
    const staffdata = ref([])

    const playerFields = [
        { key: 'prenom', label: 'Prénom' },
        { key: 'nom', label: 'Nom' },
        { key: 'equipe', label: 'Équipe' },
        { key: 'licence', label: 'Licence' },
        { key: 'charte', label: 'Charte', type: 'checkbox', align: 'text-center' },
        { key: 'otm', label: 'OTM', type: 'checkbox', align: 'text-center' }
    ]
    const playerTemplate = { prenom:'', nom:'', equipe:'1', licence:'', charte:false, otm:false }

    const staffFields = [
        { key: 'nom', label: 'Nom' },
        { key: 'prenom', label: 'Prénom' },
        { key: 'licence', label: 'Licence' },
        { key: 'role', label: 'Rôle' }
    ]
    const staffTemplate = { nom:'', prenom:'', licence:'', role:'' }


    getUsers().then( u => {
        players.value = u
    })

    function enregistrer() {            
        setUsers(players.value).then( u => {
            players.value = u
        })
    }

    function saveStaff(data) {
        // we simply replace whole array for now
        staffdata.value = data
        // optionally persist changes per element
        data.forEach(member => setStaff(member))
    }
 

  getStaff().then( u => {
        staffdata.value = u
  })

</script>

<style scoped>
.btn-toggle {
    padding: 1rem 2rem;
    border-radius: 0.75rem;
    font-size: 1.1rem;
    font-weight: 600;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    white-space: nowrap;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.btn-active {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 8px 15px rgba(59, 130, 246, 0.4);
    transform: translateY(-2px);
}

.btn-inactive {
    background: white;
    color: #6b7280;
    border-color: #e5e7eb;
}

.btn-toggle:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}

.btn-inactive:hover {
    background: #f9fafb;
    color: #374151;
    border-color: #d1d5db;
}

.btn-active:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    box-shadow: 0 12px 25px rgba(59, 130, 246, 0.5);
}

@media (max-width: 640px) {
    .btn-toggle {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }

    .btn-toggle span {
        font-size: 1.5rem;
    }
}
</style>