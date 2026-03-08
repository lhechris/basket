<template>
    <div class="w-full bg-teal-300 p-2">
        <div class="grid grid-cols-2 gap-1">
            <div v-for="(selection, index) in selections" :key="index" class="flex gap-2">
                <select 
                    v-model="selections[index]" 
                    @change="handleChange()"
                    class="px-2 py-1 border rounded"
                >
                    <option value="">Sélectionner...</option>
                    <option v-for="otm in model" :key="otm.id || otm.prenom" :value="otm.prenom">
                        {{ otm.prenom }}
                    </option>
                </select>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, watch } from 'vue'

    const model = defineModel()
    const emit = defineEmits(['update'])
    const props= defineProps({max:Number})
    
    // Initialiser selections avec les éléments qui ont selected=true
    const selections = ref(
        Array.isArray(model.value)
            ? (() => {
                  const initial = model.value.filter(otm => otm.selected).map(otm => otm.prenom);
                  // si aucune sélection, on garde un champ vide
                  if (!initial.length) return [''];
                  // s'il y a déjà des sélections, ajouter un champ vide pour pouvoir en ajouter d'autres
                  if (initial.length < props.max) {
                    return [...initial, ''];
                  } else {
                    return initial;
                  }
              })()
            : ['']
    )

    watch(selections, () => {
        // Met à jour le modèle avec les sélections
        if (model && Array.isArray(model.value)) {
            model.value.forEach(otm => {
                otm.selected = selections.value.includes(otm.prenom)
            })
        }
    }, { deep: true })

    function handleChange() {
        // retirer les entrées vides (sauf s'il ne reste qu'un champ vide)
        if (selections.value.length > 1) {
            const filtered = selections.value.filter((v, i) => {
                if (v !== '') return true
                return i === selections.value.length - 1
            })
            selections.value = filtered
        }

        // garantir qu'il y a toujours un select vide en queue        
        if (!selections.value.includes('')) {
            if (selections.value.length < props.max) {
                selections.value.push('')
            }
        }

        onUpdate()
    }

    function onUpdate() {
        emit('update')
    }

</script>