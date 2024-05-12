<template>
    <nav aria-label="Page navigation example" v-if="links?.length > 3" >
        <ul class="pagination">
            <li class="page-item" :class="{ 'active': link.active }" v-for="(link, key) in links" :key="key">
                <div
                    v-if="link.url === null"
                    class="page-link"
                    v-html="link.label"
                />

                <Link
                    v-else
                    class="page-link"
                    
                    :href="link.url"
                    v-html="link.label"
                />
            </li>
        </ul>
    </nav>
</template>

<script setup>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/vue3";
const props = defineProps({
    links: Array,
});
</script>