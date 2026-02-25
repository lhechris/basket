<template>
    <div class="w-full mt-2">
        <staff :value="staffdata" @onAdd="addStaff($event)" @onDelete="deleteStaff($event)" @onUpdate="updateStaff($event)"/>
    </div>
  </template>
  
<script setup>
  import Staff from '@/components/Staff.vue'
  import {ref} from 'vue'
  import {getStaff,setStaff} from '../js/api.js'

  const staffdata = ref([])

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
  