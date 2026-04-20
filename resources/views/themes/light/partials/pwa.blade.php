<!-- Modal section start -->
<div class="pwa-popup" id="pwaInstallPopup">
    <div class="header">

        <div class="d-flex flex-wrap">
            <img src="{{isset($pwa['single']['media']->icon_image)?getFile($pwa['single']['media']->icon_image->driver,$pwa['single']['media']->icon_image->path):""}}" alt="PWA Logo"
                 class="pwa-logo">
            <div class="header-text ms-3">
                <h2>{{$pwa['single']['title']??''}}</h2>
                <p>{{ $_SERVER['HTTP_HOST']}}</p>
            </div>
        </div>
        <button class="close-btn carousel_close_btn">×</button>
    </div>
    <p class="description">
        {{$pwa['single']['short_description']??''}}
    </p>
    <p class="sub-description">
        {{$pwa['single']['description']??''}}
    </p>
    <div class="carousel-container" id="carouselContainer">
        <div class="carousel">
            <button class="carousel-btn left" id="prevBtn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                </svg>
            </button>
            <div class="carousel-content" id="carouselContent">
                @foreach(collect($pwa['multiple'])->toArray() as $item)
                    <img src="{{isset($item['media']->carousel_image)?getFile($item['media']->carousel_image->driver,$item['media']->carousel_image->path):''}}" alt="Carousel Image">
                @endforeach

            </div>
            <button class="carousel-btn right" id="nextBtn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z"/>
                </svg>
            </button>
        </div>
    </div>
    <div class="actions">
        <button class="action-btn less" id="toggleCarousel">@lang('More')</button>
        <button class="action-btn install" id="installButton">@lang('Install')</button>
    </div>
</div>


<script>
    const popup = document.getElementById('pwaInstallPopup');
    const showButton = document.getElementById('toggleCarousel'); // Example button to show
    const closeButton = document.querySelector('.close-btn');

    document.querySelector('.carousel_close_btn').addEventListener('click',()=>{
        var $modal = $("#pwaInstallPopup");
        $modal.hide();

        localStorage.setItem('pwa_install','not_install');
    })



    const carouselContainer = document.getElementById('carouselContainer');
    const toggleCarouselBtn = document.getElementById('toggleCarousel');
    const carouselContent = document.getElementById('carouselContent');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    let currentIndex = 0;

    // Toggle visibility of the carousel with dynamic height
    toggleCarouselBtn.addEventListener('click', () => {
        if (carouselContainer.classList.contains('active')) {
            // Collapse the carousel
            carouselContainer.style.height = '0';
            carouselContainer.classList.remove('active');
            toggleCarouselBtn.textContent = 'More';
        } else {
            // Expand the carousel to 80% of the viewport height
            const carouselHeight = window.innerHeight * 0.5;
            carouselContainer.style.height = `${carouselHeight}px`;
            carouselContainer.classList.add('active');
            toggleCarouselBtn.textContent = 'Less';
        }
    });

    // Carousel navigation logic
    prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        }
    });

    nextBtn.addEventListener('click', () => {
        if (currentIndex < carouselContent.children.length - 1) {
            currentIndex++;
            updateCarousel();
        }
    });

    function updateCarousel() {
        const offset = -currentIndex * 100; // Move carousel by 100% per slide
        carouselContent.style.transform = `translateX(${offset}%)`;
    }




    window.addEventListener('load', () => {
        const isFirefox = navigator.userAgent.includes('Firefox');
        let deferredPrompt = null;

        if (!isFirefox && 'BeforeInstallPromptEvent' in window) {
            window.addEventListener('beforeinstallprompt', (e) => {

                e.preventDefault();
                deferredPrompt = e;

                const installButton = document.getElementById('installButton');
                let pwaStatus = localStorage.getItem('pwa_install');
                if (pwaStatus !== 'not_install' && pwaStatus !== 'install') {
                    $("#pwaInstallPopup").show(); // Show your modal
                }

                installButton.addEventListener('click', async () => {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    deferredPrompt = null;
                    if (outcome === 'accepted') {
                        localStorage.setItem('pwa_install', 'install');
                    } else {
                        localStorage.setItem('pwa_install', 'not_install');
                    }
                });
            });
        } else if (isFirefox) {
            const installButton = document.getElementById('installButton');
            let pwaStatus = localStorage.getItem('pwa_install');
            if (pwaStatus !== 'not_install' && pwaStatus !== 'install') {
                $("#pwaInstallPopup").show(); // Show your modal with Firefox-specific instructions
            }

            installButton.addEventListener('click', () => {
                // Show custom instructions for Firefox
                alert("To install the app on Firefox, use a mobile device. Open the browser menu (three dots in the top-right corner) and select 'Add to Home Screen'.");
                localStorage.setItem('pwa_install', 'install');
            });
        }
    });


</script>
