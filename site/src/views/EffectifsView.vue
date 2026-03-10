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
            <joueuses v-if="display==1" :value="players" @onSave="enregistrer($event)"></joueuses>
            <staff v-if="display==2" :value="staffdata" @onAdd="addStaff($event)" @onDelete="deleteStaff($event)" @onUpdate="updateStaff($event)"/>
        </div>
    </div>
</template>

 <script setup>
    // @ is an alias to /src
    import {getUsers,setUsers,getStaff,setStaff} from '@/js/api.js'
    import {ref} from "vue"
    import Joueuses from '../components/Joueuses.vue'
    import Staff from '../components/Staff.vue'

    const players = ref([])
    const display = ref(1)
    const staffdata = ref([])


    getUsers().then( u => {
        players.value = u
    })

    function enregistrer() {            
        setUsers(players.value).then( u => {
            players.value = u
        })
    }
 

  getStaff().then( u => {
        staffdata.value = u
  })

  const addStaff = (val) => {
    staffdata.value.push(val);
    setStaff(val);
  }

  const deleteStaff = (id) => {
    const member = staffdata.value.find(m => m.id === id);
    if (member) {
      member.todelete = true;
      setStaff(member);
      staffdata.value = staffdata.value.filter(m => m.id !== id);
    }
  }

  const updateStaff = ({ id, data }) => {
    const index = staffdata.value.findIndex(m => m.id === id);
    if (index !== -1) {
      staffdata.value[index] = { ...staffdata.value[index], ...data };
      setStaff(staffdata.value[index]);
    }
  }

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