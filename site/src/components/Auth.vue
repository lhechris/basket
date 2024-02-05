<template>   
    <CForm        
        class="row g-3 needs-validation" 
        novalidate 
        :validated="validatedCustom01" 
        @submit="handleSubmitCustom01"
    >

        <div v-if="logged" > 
            On est connecté
            <CButton type="submit" color="primary" >Se déconnecter</CButton>
        </div>
        <div v-else>
           <!-- <CRow class="mb-3">
            <CFormLabel for="staticEmail" class="col-sm-2 col-form-label">Email</CFormLabel>
            <div class="col-sm-8">
                <CFormInput type="text" id="Email" v-model="email"/>
            </div>
            </CRow>-->
            <CRow class="mb-3">
            <CFormLabel for="inputPassword" class="col-sm-2 col-form-label">Password</CFormLabel>
            <div class="col-sm-8">
                <CFormInput type="password" id="inputPassword" v-model="passwd"/>
            </div>
            </CRow>
            <CRow class="mb-3">
                <div class="col-sm-5" ></div>
                <div class="col-sm-2">
                    <CButton type="submit" color="primary">Se connecter</CButton>
                </div>
                <div class="col-sm-5" ></div>
            </CRow>
        </div>
    </CForm>
</template>

<script>
import {CRow,CFormLabel,CFormInput,CButton,CForm} from "@coreui/vue"

import '@coreui/coreui/dist/css/coreui.min.css'
import {login,islogged,logout} from "@/js/api.js"

import { ref } from 'vue'

export default {

    components: {
        CRow,CFormLabel,CFormInput,CButton,CForm
        
  },    
    setup() {

        const email=ref("coach")
        const passwd=ref("")
        const validatedCustom01=ref(null)
        const logged=ref(false)

        islogged().then(r => {            
            logged.value=r==1
        })

        function handleSubmitCustom01() {
            //const form = event.currentTarget
            if (!logged.value) {
                login(email.value,passwd.value).then( r => {
                    logged.value=r==1
                    window.location.reload()
                })
            } else {
                logout().then( r=> {
                    console.log(r)
                    logged.value=r==1
                    window.location.reload()
                })
            }
            email.value=""
            passwd.value=""
        }

        return {email,passwd,validatedCustom01,logged,handleSubmitCustom01}
    }
}
</script>