<template>
    <div class="double-range-slider">
        <div class="flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
            <span>Від: {{ minValue }}</span>
            <span>До: {{ maxValue }}</span>
        </div>
        <div class="relative h-6" ref="sliderContainer" @mousedown="handleTrackClick" @touchstart="handleTrackClick">
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
            
            <!-- Thumb Indicators (clickable) -->
            <div
                class="absolute top-1/2 w-5 h-5 bg-purple-600 dark:bg-purple-500 rounded-full shadow-lg transform -translate-y-1/2 -translate-x-1/2 transition-all duration-200 hover:scale-110 cursor-grab active:cursor-grabbing z-20"
                :style="{ left: `${minPercentage}%` }"
                @mousedown.stop="startDrag('min', $event)"
                @touchstart.stop="startDrag('min', $event)"
            ></div>
            <div
                class="absolute top-1/2 w-5 h-5 bg-purple-600 dark:bg-purple-500 rounded-full shadow-lg transform -translate-y-1/2 -translate-x-1/2 transition-all duration-200 hover:scale-110 cursor-grab active:cursor-grabbing z-20"
                :style="{ left: `${maxPercentage}%` }"
                @mousedown.stop="startDrag('max', $event)"
                @touchstart.stop="startDrag('max', $event)"
            ></div>
            
            <!-- Hidden inputs for value binding -->
            <input
                ref="minInput"
                type="range"
                :min="min"
                :max="max"
                :value="minValue"
                @input="updateMin"
                class="sr-only"
                tabindex="-1"
            />
            <input
                ref="maxInput"
                type="range"
                :min="min"
                :max="max"
                :value="maxValue"
                @input="updateMax"
                class="sr-only"
                tabindex="-1"
            />
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
            maxValue: this.value[1] || this.max,
            activeThumb: null,
            isDragging: false
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
                if (newVal && Array.isArray(newVal) && newVal.length === 2 && !this.isDragging) {
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
        getValueFromPosition(clientX) {
            const rect = this.$refs.sliderContainer.getBoundingClientRect();
            const percentage = Math.max(0, Math.min(100, ((clientX - rect.left) / rect.width) * 100));
            return Math.round(this.min + (percentage / 100) * (this.max - this.min));
        },
        handleTrackClick(event) {
            if (this.isDragging) return;
            
            const clientX = event.touches ? event.touches[0].clientX : event.clientX;
            const clickedValue = this.getValueFromPosition(clientX);
            const rect = this.$refs.sliderContainer.getBoundingClientRect();
            const clickedPercentage = ((clientX - rect.left) / rect.width) * 100;
            
            // Определяем, какой ползунок ближе к точке клика
            const minDist = Math.abs(clickedPercentage - this.minPercentage);
            const maxDist = Math.abs(clickedPercentage - this.maxPercentage);
            
            if (minDist < maxDist) {
                // Ближе к min ползунку
                const newMin = Math.max(this.min, Math.min(clickedValue, this.maxValue - 1));
                if (newMin !== this.minValue) {
                    this.minValue = newMin;
                    this.$emit('input', [this.minValue, this.maxValue]);
                    this.$emit('change', [this.minValue, this.maxValue]);
                }
            } else {
                // Ближе к max ползунку
                const newMax = Math.min(this.max, Math.max(clickedValue, this.minValue + 1));
                if (newMax !== this.maxValue) {
                    this.maxValue = newMax;
                    this.$emit('input', [this.minValue, this.maxValue]);
                    this.$emit('change', [this.minValue, this.maxValue]);
                }
            }
        },
        startDrag(thumb, event) {
            event.preventDefault();
            event.stopPropagation();
            this.activeThumb = thumb;
            this.isDragging = true;
            
            const moveHandler = (e) => {
                if (!this.isDragging) return;
                e.preventDefault();
                
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const value = this.getValueFromPosition(clientX);
                
                if (thumb === 'min') {
                    const newMin = Math.max(this.min, Math.min(value, this.maxValue - 1));
                    if (newMin !== this.minValue) {
                        this.minValue = newMin;
                        this.$emit('input', [this.minValue, this.maxValue]);
                    }
                } else {
                    const newMax = Math.min(this.max, Math.max(value, this.minValue + 1));
                    if (newMax !== this.maxValue) {
                        this.maxValue = newMax;
                        this.$emit('input', [this.minValue, this.maxValue]);
                    }
                }
            };
            
            const endHandler = () => {
                this.isDragging = false;
                this.activeThumb = null;
                this.$emit('change', [this.minValue, this.maxValue]);
                document.removeEventListener('mousemove', moveHandler);
                document.removeEventListener('mouseup', endHandler);
                document.removeEventListener('touchmove', moveHandler);
                document.removeEventListener('touchend', endHandler);
            };
            
            document.addEventListener('mousemove', moveHandler);
            document.addEventListener('mouseup', endHandler);
            document.addEventListener('touchmove', moveHandler, { passive: false });
            document.addEventListener('touchend', endHandler);
        },
        updateMin(event) {
            if (this.isDragging) return;
            const newMin = parseInt(event.target.value);
            if (newMin <= this.maxValue - 1) {
                this.minValue = newMin;
                this.$emit('input', [this.minValue, this.maxValue]);
                this.$emit('change', [this.minValue, this.maxValue]);
            }
        },
        updateMax(event) {
            if (this.isDragging) return;
            const newMax = parseInt(event.target.value);
            if (newMax >= this.minValue + 1) {
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

.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}
</style>
