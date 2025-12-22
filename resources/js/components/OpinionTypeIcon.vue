<template>
    <div v-if="opinionType" 
         class="flex items-center"
         :class="{
             'text-green-600 dark:text-green-400': opinionType === 'positive',
             'text-yellow-600 dark:text-yellow-400': opinionType === 'neutral',
             'text-red-600 dark:text-red-400': opinionType === 'negative'
         }">
        <span class="text-xl flex items-center justify-center" 
              :class="{
                  'bg-green-100 dark:bg-green-900/30': opinionType === 'positive',
                  'bg-yellow-100 dark:bg-yellow-900/30': opinionType === 'neutral',
                  'bg-red-100 dark:bg-red-900/30': opinionType === 'negative'
              }"
              :style="iconStyle">
            <i v-if="opinionType === 'positive'" class="fas fa-smile"></i>
            <i v-else-if="opinionType === 'neutral'" class="fas fa-meh"></i>
            <i v-else-if="opinionType === 'negative'" class="fas fa-frown"></i>
        </span>
    </div>
</template>

<script>
export default {
    name: 'OpinionTypeIcon',
    props: {
        opinionType: {
            type: String,
            default: null,
            validator: (value) => !value || ['positive', 'neutral', 'negative'].includes(value)
        },
        size: {
            type: String,
            default: 'md', // 'sm', 'md', 'lg'
            validator: (value) => ['sm', 'md', 'lg'].includes(value)
        }
    },
    computed: {
        iconStyle() {
            const sizes = {
                sm: { padding: '2px 6px', borderRadius: '6px', width: '24px', height: '24px', fontSize: '0.875rem' },
                md: { padding: '4px 8px', borderRadius: '8px', width: '32px', height: '32px', fontSize: '1rem' },
                lg: { padding: '6px 10px', borderRadius: '10px', width: '40px', height: '40px', fontSize: '1.25rem' }
            };
            return sizes[this.size];
        }
    }
}
</script>

<style scoped>
</style>

