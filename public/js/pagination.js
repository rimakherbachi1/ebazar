 document.addEventListener('DOMContentLoaded', function() {
            
            const mainImage = document.getElementById('main-image');
            const thumbnails = document.querySelectorAll('.thumbnail-item');
            thumbnails.forEach(thumbnail => {
                thumbnail.addEventListener('click', function() {
                    mainImage.src = this.getAttribute('data-src');
                    thumbnails.forEach(item => item.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            const tabLinks = document.querySelectorAll('.tab-link');
            const tabContents = document.querySelectorAll('.tab-content');
            tabLinks.forEach(link => {
                link.addEventListener('click', function() {
                    tabLinks.forEach(l => l.classList.remove('active'));
                    tabContents.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById(this.getAttribute('data-tab')).classList.add('active');
                });
            });
        });