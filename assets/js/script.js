document.addEventListener('DOMContentLoaded', function() {
    // Carousel Logic
    const carousels = document.querySelectorAll('.wiwa-ig-carousel-container');

    carousels.forEach(container => {
        const track = container.querySelector('.wiwa-ig-carousel');
        const prevBtn = container.querySelector('.prev');
        const nextBtn = container.querySelector('.next');

        if (!track || !prevBtn || !nextBtn) return;

        nextBtn.addEventListener('click', () => {
            const itemWidth = track.querySelector('.wiwa-ig-item').offsetWidth + 20;
            track.scrollBy({ left: itemWidth, behavior: 'smooth' });
        });

        prevBtn.addEventListener('click', () => {
            const itemWidth = track.querySelector('.wiwa-ig-item').offsetWidth + 20;
            track.scrollBy({ left: -itemWidth, behavior: 'smooth' });
        });
    });

    // Lightbox Logic
    const links = document.querySelectorAll('.wiwa-ig-link');
    const lightbox = document.getElementById('wiwa-lightbox');
    const lightboxMedia = document.getElementById('wiwa-lightbox-media');
    const closeBtn = document.querySelector('.wiwa-lightbox-close');

    if (lightbox && closeBtn) {
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                const mode = this.getAttribute('data-mode');
                
                if (mode === 'lightbox') {
                    e.preventDefault();
                    
                    const mediaType = this.getAttribute('data-media-type');
                    const mediaUrl = this.getAttribute('data-media-url');
                    
                    lightboxMedia.innerHTML = ''; // Clear previous

                    if (mediaType === 'VIDEO') {
                        const video = document.createElement('video');
                        video.src = mediaUrl;
                        video.controls = true;
                        video.autoplay = true;
                        lightboxMedia.appendChild(video);
                    } else {
                        const img = document.createElement('img');
                        img.src = mediaUrl;
                        lightboxMedia.appendChild(img);
                    }

                    lightbox.classList.add('active');
                    document.body.style.overflow = 'hidden'; // Prevent scrolling
                }
            });
        });

        const closeLightbox = () => {
            lightbox.classList.remove('active');
            lightboxMedia.innerHTML = ''; // Stop video
             document.body.style.overflow = '';
        };

        closeBtn.addEventListener('click', closeLightbox);
        
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && lightbox.classList.contains('active')) {
                closeLightbox();
            }
        });
    }
});
