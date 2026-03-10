<template>
    <div class="main-container max-w-6xl mx-auto p-6">
        <!-- header slot allows parent to supply title -->
        <slot name="header"></slot>

        <div class="bg-white rounded-lg shadow-lg overflow-x-auto">
            <!-- Table Header -->
            <div :class="headerClass">
                <div v-for="f in fields" :key="f.key" :class="f.align || ''">{{ f.label }}</div>
                <div class="text-center">Actions</div>
            </div>

            <!-- Table Body -->
            <div class="divide-y divide-gray-200">
                <div v-for="(u, i) in props.value" :key="i" :class="rowClass(u, i)">
                    <template v-for="f in fields" :key="f.key">
                        <div v-if="f.type === 'checkbox'" class="flex justify-center">
                            <label class="custom-checkbox" :class="[u.todelete ? 'disabled' : '', editingId !== i ? 'disabled' : '']">
                                <input type="checkbox" v-model="u[f.key]" :disabled="editingId !== i">
                                <span class="checkmark checkmark-mobile"></span>
                            </label>
                        </div>
                        <div v-else>
                            <input
                                v-model="u[f.key]"
                                :placeholder="f.label"
                                :disabled="editingId !== i"
                                class="input-field input-mobile"
                                :class="[u.todelete ? 'disabled-input' : '', editingId !== i ? 'input-disabled' : '']"
                            />
                        </div>
                    </template>
                    <div class="flex justify-center gap-2">
                        <button
                            v-if="editingId !== i"
                            class="btn-edit"
                            @click="editer(i)"
                            title="Éditer"
                        >
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                            </svg>
                        </button>
                        <button
                            v-if="editingId === i"
                            class="btn-save"
                            @click="sauvegarder(i)"
                            title="Sauvegarder"
                        >
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M19.414 1.586A2 2 0 0018 1H2a2 2 0 00-1.414.586L0 3v14a2 2 0 002 2h16a2 2 0 002-2V3l-1.586-1.414zM9 15a3 3 0 110-6 3 3 0 010 6zm8-9H3V4h14v2z"/>
                            </svg>
                        </button>
                        <button
                            class="btn-delete"
                            @click="supprime(u.id)"
                            title="Supprimer"
                        >
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <div class="flex justify-center mt-4 md:mt-8">
            <button
                class="btn btn-primary text-sm md:text-base"
                @click="ajoute()"
            >
                ➕ Ajouter
            </button>
        </div>
    </div>
</template>

  <script setup>
  import { ref, computed } from 'vue'
  
   const props = defineProps({
       value: { type: Array, default: () => [] },
       fields: { type: Array, required: true },
       newItem: { type: Object, default: () => ({}) }
   })
   const emit = defineEmits(['onSave'])
   const editingId = ref(-1)

   const headerClass = computed(() => {
       const cols = props.fields.length + 1
       return `grid grid-cols-${cols} gap-1 md:gap-2 lg:gap-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold p-2 md:p-4 text-xs md:text-base`
   })

   function rowClass(u, i) {
       const base = 'grid ' +
           `grid-cols-${props.fields.length + 1} ` +
           'gap-1 md:gap-2 lg:gap-4 p-2 md:p-4 items-center text-xs md:text-base'
       const state = u.todelete ? 'bg-red-50' : 'hover:bg-gray-50'
       return `${base} ${state}`
   }

    function supprime(id) {
        let indice=-1;
        props.value.forEach( (e,k) => {
            if (e.id == id) { indice = k;}
        } )
        
        if (indice>=0) {
            props.value[indice]["todelete"]= true;
            emit('onSave', props.value)
        }
    }

    function ajoute() {
        const template = { ...props.newItem }
        props.value.push(template)
        editingId.value = props.value.length - 1
    }

    function editer(index) {
        editingId.value = index
    }

    function sauvegarder(index) {
        editingId.value = -1
        emit('onSave', props.value)
    }

  </script>

<style scoped>
.input-field {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    transition: all 0.2s;
    font-size: inherit;
}

.input-mobile {
    padding: 0.35rem 0.5rem;
}

.input-field:focus {
    outline: none;
    ring: 2px;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
}

.input-field:disabled {
    background-color: #f3f4f6;
    cursor: not-allowed;
}

.disabled-input {
    background-color: #d1d5db;
    cursor: not-allowed;
}

.input-disabled {
    background-color: #f3f4f6;
    cursor: not-allowed;
    border-color: #e5e7eb;
}

.btn {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background-color: #2563eb;
    color: white;
}

.btn-primary:hover {
    background-color: #1d4ed8;
}

.btn-primary:active {
    background-color: #1e40af;
}

.btn-edit {
    color: #16a34a;
    padding: 0.35rem;
    border-radius: 0.5rem;
    transition: all 0.2s;
    border: none;
    background: transparent;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-edit:hover {
    color: #15803d;
    background-color: #dcfce7;
}

.btn-save {
    color: #2563eb;
    padding: 0.35rem;
    border-radius: 0.5rem;
    transition: all 0.2s;
    border: none;
    background: transparent;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-save:hover {
    color: #1d4ed8;
    background-color: #dbeafe;
}

.btn-delete {
    color: #dc2626;
    padding: 0.35rem;
    border-radius: 0.5rem;
    transition: all 0.2s;
    border: none;
    background: transparent;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-delete:hover {
    color: #7f1d1d;
    background-color: #fee2e2;
}

.custom-checkbox {
    display: block;
    position: relative;
    cursor: pointer;
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
    left: 0;
    height: 1.5rem;
    width: 1.5rem;
    background-color: #e5e7eb;
    border: 2px solid #d1d5db;
    border-radius: 0.25rem;
    transition: all 0.2s;
}

.checkmark-mobile {
    height: 1.2rem;
    width: 1.2rem;
}

.custom-checkbox:not(.disabled):hover input ~ .checkmark {
    background-color: #d1d5db;
}

.custom-checkbox input:checked ~ .checkmark {
    background-color: #22c55e;
    border-color: #16a34a;
}

.checkmark:after {
    content: "";
    position: absolute;
    display: none;
    left: 6px;
    top: 2px;
    width: 6px;
    height: 12px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.checkmark-mobile:after {
    left: 4px;
    top: 1px;
    width: 4px;
    height: 9px;
}

.custom-checkbox input:checked ~ .checkmark:after {
    display: block;
}

.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.main-container {
    overflow-x: auto;
}

@media (max-width: 640px) {
    .main-container {
        padding: 0.75rem;
    }
}
</style>