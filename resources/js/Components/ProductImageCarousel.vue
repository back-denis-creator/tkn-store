<template>
    <img
        v-if="images.length === 1"
        :src="images[0].original_url"
        :alt="alt"
        class="aspect-square w-full object-cover transition-transform duration-500 group-hover:scale-110"
        @error="$emit('broken')"
    />
    <div
        v-else
        class="relative aspect-square w-full touch-pan-y overflow-hidden select-none"
        @touchstart="onDragStart"
        @touchmove="onDragMove"
        @touchend="onDragEnd"
        @mousedown="onMouseDown"
        @click="onClick"
    >
        <div class="flex h-full" :style="trackStyle">
            <img
                v-for="image in images"
                :key="image.id"
                :src="image.original_url"
                :alt="alt"
                class="aspect-square w-full shrink-0 object-cover"
                draggable="false"
                @error="$emit('broken')"
            />
        </div>
        <div class="absolute inset-x-0 bottom-2 flex justify-center gap-1">
            <span
                v-for="(image, i) in images"
                :key="image.id"
                class="h-1.5 w-1.5 rounded-full transition-colors"
                :class="i === activeIndex ? 'bg-amber-400' : 'bg-white/70'"
            ></span>
        </div>
    </div>
</template>
<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    images: {
        type: Array,
        default: () => [],
    },
    alt: {
        type: String,
        default: '',
    },
})
defineEmits(['broken'])

const SWIPE_THRESHOLD = 40

const activeIndex = ref(0)
const dragOffset = ref(0)
const isDragging = ref(false)
let startX = 0
let startY = 0
let axisLocked = null
let didSwipe = false

const pointFromEvent = (e) => {
    if (e.touches && e.touches.length) return { x: e.touches[0].clientX, y: e.touches[0].clientY }
    if (e.changedTouches && e.changedTouches.length) return { x: e.changedTouches[0].clientX, y: e.changedTouches[0].clientY }
    return { x: e.clientX, y: e.clientY }
}

const onDragStart = (e) => {
    const p = pointFromEvent(e)
    startX = p.x
    startY = p.y
    axisLocked = null
    isDragging.value = true
    dragOffset.value = 0
}

const onDragMove = (e) => {
    if (!isDragging.value) return
    const p = pointFromEvent(e)
    const dx = p.x - startX
    const dy = p.y - startY
    if (axisLocked === null) {
        if (Math.abs(dx) < 5 && Math.abs(dy) < 5) return
        axisLocked = Math.abs(dx) > Math.abs(dy) ? 'x' : 'y'
    }
    if (axisLocked === 'x') {
        if (e.cancelable) e.preventDefault()
        dragOffset.value = dx
    }
}

const onDragEnd = () => {
    if (!isDragging.value) return
    isDragging.value = false
    if (axisLocked === 'x' && Math.abs(dragOffset.value) > SWIPE_THRESHOLD) {
        if (dragOffset.value < 0 && activeIndex.value < props.images.length - 1) {
            activeIndex.value++
        } else if (dragOffset.value > 0 && activeIndex.value > 0) {
            activeIndex.value--
        }
        didSwipe = true
    }
    dragOffset.value = 0
    axisLocked = null
}

// Mouse-drag support (desktop) mirrors the touch handlers above via the same
// start/move/end trio, tracked on window since the pointer can leave the card
// mid-drag.
const onMouseDown = (e) => {
    onDragStart(e)
    const onMove = (ev) => onDragMove(ev)
    const onUp = (ev) => {
        onDragEnd(ev)
        window.removeEventListener('mousemove', onMove)
        window.removeEventListener('mouseup', onUp)
    }
    window.addEventListener('mousemove', onMove)
    window.addEventListener('mouseup', onUp)
}

// The card image sits inside an Inertia <Link> — a swipe that just changed
// the slide must not also trigger the link's navigation, so a completed
// swipe cancels the very click it produces.
const onClick = (e) => {
    if (didSwipe) {
        e.preventDefault()
        e.stopPropagation()
        didSwipe = false
    }
}

const trackStyle = computed(() => ({
    transform: `translateX(calc(-${activeIndex.value * 100}% + ${dragOffset.value}px))`,
    transition: isDragging.value ? 'none' : 'transform 300ms ease-out',
}))
</script>
