<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message">
        <span class="small red">{{ message }}</span>
    </div>
</template>
