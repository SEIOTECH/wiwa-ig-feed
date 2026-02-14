document.addEventListener('DOMContentLoaded', function() {
    const carousels = document.querySelectorAll('.wiwa-ig-carousel-container');

    carousels.forEach(container => {
        const track = container.querySelector('.wiwa-ig-carousel');
        const prevBtn = container.querySelector('.prev');
        const nextBtn = container.querySelector('.next');

        if (!track || !prevBtn || !nextBtn) return;

        nextBtn.addEventListener('click', () => {
            const itemWidth = track.querySelector('.wiwa-ig-item').offsetWidth + 20; // width + gap
            track.scrollBy({ left: itemWidth, behavior: 'smooth' });
        });

        prevBtn.addEventListener('click', () => {
            const itemWidth = track.querySelector('.wiwa-ig-item').offsetWidth + 20; // width + gap
            track.scrollBy({ left: -itemWidth, behavior: 'smooth' });
        });

        // Optional: Hide buttons at ends (implementation omitted for simplicity, can be added with scroll listener)
    });
});
