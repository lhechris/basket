<template>                    
                    <c-pagination>
                        <c-pagination-item 
                                href="#" 
                                @click="moins()" 
                                :disabled="page<=1"
                            >{{message}} précédent
                        </c-pagination-item>
                        <c-pagination-item 
                                href="#" 
                               @click="plus()"
                                :disabled="page>=nbpages"
                            >{{message}} suivant
                        </c-pagination-item>
                    </c-pagination>
</template>

<script setup>
import {CPagination,CPaginationItem} from "@coreui/vue"

const props = defineProps(["message","nbpages"])
const page = defineModel()

function moins() {
    if (page.value>1) {
        page.value--
    }
}

function plus() {
    if (page.value<(props.nbpages)) {
        page.value++
    }
}


</script>