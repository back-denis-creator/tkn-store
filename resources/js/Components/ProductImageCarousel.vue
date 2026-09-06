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
        class="group/carousel relative aspect-square w-full touch-pan-y overflow-hidden select-none cursor-grab active:cursor-grabbing"
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
        <button
            v-if="activeIndex > 0"
            type="button"
            class="absolute top-1/2 left-1 z-10 flex h-7 w-7 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-gray-700 opacity-0 shadow-sm transition-opacity group-hover/carousel:opacity-100 hover:bg-white"
            @click="goPrev"
        >
            <ChevronLeftIcon class="h-4 w-4" />
        </button>
        <button
            v-if="activeIndex < images.length - 1"
            type="button"
            class="absolute top-1/2 right-1 z-10 flex h-7 w-7 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-gray-700 opacity-0 shadow-sm transition-opacity group-hover/carousel:opacity-100 hover:bg-white"
            @click="goNext"
        >
            <ChevronRightIcon class="h-4 w-4" />
        </button>
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
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/solid'

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
        // A mouse/trackpad drag is rarely perfectly straight, so deciding the
        // axis from the first ~5px of jitter locked onto 'y' constantly and
        // made the swipe feel like it only worked "sometimes". Waiting for
        // more movement, and treating this as a horizontal carousel by
        // default (only locking vertical once the drag is CLEARLY more
        // vertical than horizontal), makes it reliable without breaking a
        // real vertical scroll gesture on touch.
        if (Math.abs(dx) < 10 && Math.abs(dy) < 10) return
        axisLocked = Math.abs(dy) > Math.abs(dx) * 1.5 ? 'y' : 'x'
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

// The arrows sit inside the same Inertia <Link> as the slides — clicking one
// must move the carousel, not navigate to the product.
const goPrev = (e) => {
    e.preventDefault()
    e.stopPropagation()
    if (activeIndex.value > 0) activeIndex.value--
}
const goNext = (e) => {
    e.preventDefault()
    e.stopPropagation()
    if (activeIndex.value < props.images.length - 1) activeIndex.value++
}

const trackStyle = computed(() => ({
    transform: `translateX(calc(-${activeIndex.value * 100}% + ${dragOffset.value}px))`,
    transition: isDragging.value ? 'none' : 'transform 300ms ease-out',
}))
</script>
