import { ref, onMounted, onUnmounted } from 'vue';

/**
 * Reactive countdown to December 31 of current (or next) year
 * Automatically rolls over every year
 *
 * @returns {Object} { countdown, startCountdown }
 */
export function useYearEndCountdown() {
    const targetDate = ref(getTargetDate());
    const countdown = ref({
        days: 0,
        hours: 0,
        minutes: 0,
        seconds: 0
    });

    let timer = null;

    function getTargetDate() {
        const now = new Date();
        const year = now.getFullYear();
        const target = new Date(`${year}-12-31T23:59:59`);

        return now > target ? new Date(`${year + 1}-12-31T23:59:59`) : target;
    }

    function updateCountdown() {
        const now = new Date();
        const distance = targetDate.value - now;

        if (distance <= 0) {
            targetDate.value = getTargetDate();
            return;
        }

        countdown.value = {
            days: Math.floor(distance / (1000 * 60 * 60 * 24)),
            hours: Math.floor((distance / (1000 * 60 * 60)) % 24),
            minutes: Math.floor((distance / (1000 * 60)) % 60),
            seconds: Math.floor((distance / 1000) % 60)
        };
    }

    function startCountdown() {
        updateCountdown();
        timer = setInterval(updateCountdown, 1000);
    }

    onMounted(startCountdown);
    onUnmounted(() => clearInterval(timer));

    return { countdown };
}
