<template>
    <div class="double-range-slider">
        <div class="flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
            <span>Від: {{ minValue }}</span>
            <span>До: {{ maxValue }}</span>
        </div>
        <div class="relative h-6">
            <!-- Track -->
            <div class="absolute top-1/2 left-0 right-0 h-1 bg-gray-200 dark:bg-gray-700 rounded-full transform -translate-y-1/2"></div>
            
            <!-- Active Range -->
            <div 
                class="absolute top-1/2 h-1 bg-purple-600 dark:bg-purple-500 rounded-full transform -translate-y-1/2 transition-all duration-200"
                :style="{
                    left: `${minPercentage}%`,
                    width: `${maxPercentage - minPercentage}%`
                }"
            ></div>
            
            <!-- Min Thumb -->
            <input
                type="range"
                :min="min"
                :max="max"
                :value="minValue"
                @input="updateMin"
                class="absolute top-1/2 left-0 w-full h-0 opacity-0 cursor-pointer transform -translate-y-1/2"
                :style="{ zIndex: minValue > maxValue - 1 ? 5 : 3 }"
            />
            
            <!-- Max Thumb -->
            <input
                type="range"
                :min="min"
                :max="max"
                :value="maxValue"
                @input="updateMax"
                class="absolute top-1/2 left-0 w-full h-0 opacity-0 cursor-pointer transform -translate-y-1/2"
                :style="{ zIndex: maxValue < minValue + 1 ? 5 : 4 }"
            />
            
            <!-- Thumb Indicators -->
            <div
                class="absolute top-1/2 w-5 h-5 bg-purple-600 dark:bg-purple-500 rounded-full shadow-lg transform -translate-y-1/2 -translate-x-1/2 transition-all duration-200 hover:scale-110 cursor-grab active:cursor-grabbing"
                :style="{ left: `${minPercentage}%` }"
            ></div>
            <div
                class="absolute top-1/2 w-5 h-5 bg-purple-600 dark:bg-purple-500 rounded-full shadow-lg transform -translate-y-1/2 -translate-x-1/2 transition-all duration-200 hover:scale-110 cursor-grab active:cursor-grabbing"
                :style="{ left: `${maxPercentage}%` }"
            ></div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'DoubleRangeSlider',
    props: {
        min: {
            type: Number,
            default: 1
        },
        max: {
            type: Number,
            default: 10
        },
        value: {
            type: Array,
            default: () => [1, 10]
        }
    },
    data() {
        return {
            minValue: this.value[0] || this.min,
            maxValue: this.value[1] || this.max
        }
    },
    computed: {
        minPercentage() {
            return ((this.minValue - this.min) / (this.max - this.min)) * 100;
        },
        maxPercentage() {
            return ((this.maxValue - this.min) / (this.max - this.min)) * 100;
        }
    },
    watch: {
        value: {
            handler(newVal) {
                if (newVal && Array.isArray(newVal) && newVal.length === 2) {
                    const newMin = Math.max(this.min, Math.min(this.max, newVal[0]));
                    const newMax = Math.max(this.min, Math.min(this.max, newVal[1]));
                    
                    // Обновляем только если значения действительно изменились
                    if (this.minValue !== newMin) {
                        this.minValue = newMin;
                    }
                    if (this.maxValue !== newMax) {
                        this.maxValue = newMax;
                    }
                }
            },
            immediate: true
        }
    },
    methods: {
        updateMin(event) {
            const newMin = parseInt(event.target.value);
            if (newMin <= this.maxValue) {
                this.minValue = newMin;
                this.$emit('input', [this.minValue, this.maxValue]);
                this.$emit('change', [this.minValue, this.maxValue]);
            }
        },
        updateMax(event) {
            const newMax = parseInt(event.target.value);
            if (newMax >= this.minValue) {
                this.maxValue = newMax;
                this.$emit('input', [this.minValue, this.maxValue]);
                this.$emit('change', [this.minValue, this.maxValue]);
            }
        }
    }
}
</script>

<style scoped>
.double-range-slider {
    position: relative;
}

/* Стили для range inputs */
input[type="range"] {
    -webkit-appearance: none;
    appearance: none;
    background: transparent;
    cursor: pointer;
}

input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    background: #9333ea;
    border-radius: 50%;
    cursor: grab;
}

input[type="range"]::-webkit-slider-thumb:active {
    cursor: grabbing;
}

input[type="range"]::-moz-range-thumb {
    width: 20px;
    height: 20px;
    background: #9333ea;
    border-radius: 50%;
    border: none;
    cursor: grab;
}

input[type="range"]::-moz-range-thumb:active {
    cursor: grabbing;
}
</style>

