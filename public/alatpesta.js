
    // ==================== KONFIGURASI ====================
    const API_URL = 'https://alatpesta.nuscode.id';
    let isProcessing = false;
    let isChatbotOpen = false;
    let isProductsModalOpen = false;
    let currentBannerIndex = 0;
    let bannerInterval;
    let currentSessionId = 'main_session';
    let currentSlideIndex = 0;
    let autoSlideInterval;
    let isDragging = false;
    let startPosX = 0;
    let currentTranslate = 0;
    let prevTranslate = 0;

    // ==================== DATA PRODUK DENGAN GAMBAR ====================
const productsWithImages = [
    { 
        id: 1, 
        name: "Kursi", 
        icon: "fas fa-chair", 
        description: "Pilihan kursi yang siap digunakan untuk menghadirkan suasana pernikahan yang rapi dan nyaman.", 
        query: "harga sewa kursi",
        image: "foto/produk/kursi.webp"
    },
    { 
        id: 2, 
        name: "Meja", 
        icon: "fas fa-table", 
        description: "Meja pendukung resepsi yang membantu menciptakan tata ruang yang indah dan tertata.", 
        query: "harga sewa meja",
        image: "foto/produk/meja.webp"
    },
    { 
        id: 3, 
        name: "Sound System", 
        icon: "fas fa-volume-up", 
        description: "Perangkat audio yang memastikan setiap rangkaian acara terdengar jelas dan berkesan.", 
        query: "harga sewa sound system",
        image: "foto/produk/soundsystem.webp"
    },
    { 
        id: 4, 
        name: "Tenda", 
        icon: "fas fa-campground", 
        description: "Solusi peneduh acara yang menghadirkan kenyamanan sekaligus tampilan yang elegan.", 
        query: "harga sewa tenda",
        image: "foto/produk/tenda.webp"
    },
    { 
        id: 5, 
        name: "Panggung", 
        icon: "fas fa-music", 
        description: "Area utama acara yang dirancang untuk menonjolkan momen penting secara profesional.", 
        query: "harga sewa panggung",
        image: "foto/produk/panggung.webp"
    },
    { 
        id: 6, 
        name: "Karpet", 
        icon: "fas fa-rug", 
        description: "Elemen pelengkap yang memberi sentuhan mewah pada jalur dan area spesial pernikahan.", 
        query: "harga sewa karpet",
        image: "foto/produk/karpet.webp"
    },
    { 
        id: 7, 
        name: "Pelaminan", 
        icon: "fas fa-heart", 
        description: "Latar utama pernikahan yang menghadirkan nuansa sakral, indah, dan berkelas.", 
        query: "harga paket pelaminan",
        image: "foto/produk/pelaminan.webp"
    },
    { 
        id: 8, 
        name: "Lighting", 
        icon: "fas fa-lightbulb", 
        description: "Pencahayaan dekoratif yang memperkuat suasana romantis dan keindahan dekorasi.", 
        query: "harga sewa lighting",
        image: "foto/produk/lighting.webp"
    },
    { 
        id: 9, 
        name: "Alat Makan", 
        icon: "fas fa-utensils", 
        description: "Perlengkapan jamuan yang mendukung penyajian hidangan secara rapi dan berkelas.", 
        query: "harga sewa alat makan",
        image: "foto/produk/alatmakan.webp"
    },
    { 
        id: 10, 
        name: "Podium", 
        icon: "fas fa-microphone", 
        description: "Pelengkap prosesi yang memberi kesan formal dan tertata pada acara pernikahan.", 
        query: "harga sewa podium",
        image: "foto/produk/podium.webp"
    }
];




    // ==================== KONFIGURASI LOCALSTORAGE ====================
    const CHAT_STORAGE_KEY = 'alatpesta_chat_history';
    const MAX_LAST_MESSAGES = 2;

    // ==================== INISIALISASI ====================
    document.addEventListener('DOMContentLoaded', function() {
        console.log('[INIT] Sistem diinisialisasi');
        loadBanners();
        loadLastChatHistory();
        checkConnection();
        
        setInterval(checkConnection, 30000);
        
        window.addEventListener('beforeunload', saveLastChatHistory);
        window.addEventListener('pagehide', saveLastChatHistory);
        
        document.addEventListener('click', handleDocumentClick);
        document.addEventListener('keydown', handleEscapeKey);
        
        const chatContainer = document.getElementById('chatbotContainer');
        chatContainer.addEventListener('click', function(event) {
            event.stopPropagation();
        });
        
        const modalContainer = document.getElementById('productsModal');
        modalContainer.addEventListener('click', function(event) {
            event.stopPropagation();
        });
        
        console.log('[INIT] Siap digunakan');
    });

    // ==================== FUNGSI UTILITY ====================
    function delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    function scrollToBottom() {
        const chatArea = document.getElementById('chatArea');
        setTimeout(() => {
            chatArea.scrollTop = chatArea.scrollHeight;
        }, 100);
    }

    function formatMessageText(text) {
        if (!text) return '';
        
        text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        text = text.replace(/\n/g, '<br>');
        
        // Format WhatsApp links
        const waRegex = /(\+?62\s?|0)(\d{2,3}[\.\-\s]?\d{2,4}[\.\-\s]?\d{3,4}[\.\-\s]?\d{0,4})/g;
        
        text = text.replace(waRegex, function(match, prefix, number) {
            let cleanNumber = number.replace(/\D/g, '');
            
            if (cleanNumber.startsWith('0')) {
                cleanNumber = '62' + cleanNumber.substring(1);
            } else if (cleanNumber.startsWith('8')) {
                cleanNumber = '62' + cleanNumber;
            }
            
            let displayNumber = formatPhoneNumber(cleanNumber);
            
            return `<a href="https://wa.me/${cleanNumber}" target="_blank" class="whatsapp-link">
                ${displayNumber}
            </a>`;
        });
        
        return text;
    }

    function formatPhoneNumber(number) {
        const cleaned = number.replace(/\D/g, '');
        
        if (cleaned.startsWith('62')) {
            const localNumber = cleaned.substring(2);
            
            if (localNumber.length === 10) {
                return localNumber.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
            } else if (localNumber.length === 11) {
                return localNumber.replace(/(\d{4})(\d{4})(\d{3})/, '$1-$2-$3');
            } else if (localNumber.length === 12) {
                return localNumber.replace(/(\d{4})(\d{4})(\d{4})/, '$1-$2-$3');
            }
        }
        
        if (cleaned.length === 10) {
            return cleaned.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
        } else if (cleaned.length === 11) {
            return cleaned.replace(/(\d{4})(\d{4})(\d{3})/, '$1-$2-$3');
        } else if (cleaned.length === 12) {
            return cleaned.replace(/(\d{4})(\d{4})(\d{4})/, '$1-$2-$3');
        }
        
        return number;
    }

    // ==================== FUNGSI BANNER ====================
    function loadBanners() {
        const bannerContainer = document.querySelector('.banner-container');
        const placeholder = document.getElementById('bannerPlaceholder');
        
        const bannerFiles = [
            'banner1.webp', 'banner2.webp', 'banner3.webp', 
            'banner4.webp', 'banner5.webp', 'banner.png',
            'banner1.png', 'banner2.png', 'banner3.png'
        ];
        
        let loadedBanners = [];
        
        bannerFiles.forEach((filename, index) => {
            const img = new Image();
            img.src = `foto/produk/${filename}`;
            img.className = 'banner-image';
            img.alt = `Banner ${index + 1}`;
            img.onload = function() {
                loadedBanners.push(img);
                
                if (loadedBanners.length === 1) {
                    placeholder.style.display = 'none';
                    bannerContainer.appendChild(img);
                    img.classList.add('active');
                    startBannerRotation(loadedBanners);
                }
            };
            
            img.onerror = function() {
                console.log(`Banner ${filename} tidak ditemukan`);
            };
        });
        
        setTimeout(() => {
            if (loadedBanners.length === 0) {
                placeholder.innerHTML = `
                    <i class="fas fa-images" style="font-size: 3rem; margin-bottom: 15px; color: var(--accent-color);"></i>
                    <p>Tidak ada banner ditemukan</p>
                    <p style="font-size: 0.8rem; margin-top: 10px;">Tambahkan file banner ke folder 'foto'</p>
                `;
            }
        }, 2000);
    }

    function startBannerRotation(banners) {
        if (banners.length <= 1) return;
        
        clearInterval(bannerInterval);
        
        bannerInterval = setInterval(() => {
            showNextBanner(banners);
        }, 5000);
    }

    function showNextBanner(banners) {
        const currentBanner = document.querySelector('.banner-image.active');
        if (currentBanner) {
            currentBanner.classList.remove('active');
        }
        
        currentBannerIndex = (currentBannerIndex + 1) % banners.length;
        banners[currentBannerIndex].classList.add('active');
    }

    // ==================== FUNGSI MODAL PRODUK ====================
    function openProductsModal() {
        console.log('[MODAL] Membuka modal produk');
        const modal = document.getElementById('productsModal');
        const overlay = document.getElementById('productsModalOverlay');
        
        modal.classList.add('active');
        overlay.classList.add('active');
        isProductsModalOpen = true;
        document.body.style.overflow = 'hidden';
        
        setTimeout(() => {
            document.querySelector('.close-modal').focus();
            initInfiniteCarousel();
            setupCarouselTouchEvents();
        }, 100);
    }

    function closeProductsModal() {
        console.log('[MODAL] Menutup modal produk');
        const modal = document.getElementById('productsModal');
        const overlay = document.getElementById('productsModalOverlay');
        
        modal.classList.remove('active');
        overlay.classList.remove('active');
        isProductsModalOpen = false;
        document.body.style.overflow = '';
        
        // Hentikan auto slide
        stopAutoSlide();
        removeCarouselTouchEvents();
    }

    // ==================== INFINITE CAROUSEL FUNCTIONS ====================
    function initInfiniteCarousel() {
        console.log('[CAROUSEL] Menginisialisasi infinite carousel');
        const carouselTrack = document.getElementById('infiniteCarouselTrack');
        const indicatorsContainer = document.getElementById('carouselIndicators');
        
        if (!carouselTrack) {
            console.error('[CAROUSEL] Carousel track tidak ditemukan');
            return;
        }
        
        // Kosongkan carousel track
        carouselTrack.innerHTML = '';
        
        // Buat slide untuk setiap produk
        productsWithImages.forEach((product, index) => {
            const slide = document.createElement('div');
            slide.className = 'carousel-slide';
            slide.dataset.index = index;
            
            // Buat placeholder sementara
            const placeholderHTML = `
                <div class="slide-placeholder" id="slidePlaceholder${index}">
                    <i class="${product.icon}" style="font-size: 4rem;"></i>
                    <p>${product.name}</p>
                    <p style="font-size: 0.8rem;">Sedang memuat gambar...</p>
                </div>
                <div class="image-overlay"></div>
                <div class="slide-content">
                    <h3>${product.name}</h3>
                    <p>${product.description}</p>
                    <button class="slide-ask-button" onclick="askProductFromSlide(${index})">
                        <i class="fas fa-comment-alt"></i>
                        Tanya Harga
                    </button>
                </div>
            `;
            
            slide.innerHTML = placeholderHTML;
            
            // Load gambar secara async
            loadSlideImage(slide, product, index);
            
            carouselTrack.appendChild(slide);
        });
        
        // Buat indikator
        updateCarouselIndicators();
        
        // Tampilkan slide pertama
        goToSlide(currentSlideIndex, true);
        
        // Mulai auto slide
        startAutoSlide();
    }

    function loadSlideImage(slideElement, product, index) {
        const img = new Image();
        img.className = 'carousel-slide-image';
        img.alt = product.name;
        
        img.onload = function() {
            console.log(`[CAROUSEL] Gambar ${product.image} berhasil dimuat`);
            
            // Hapus placeholder
            const placeholder = slideElement.querySelector(`#slidePlaceholder${index}`);
            if (placeholder) {
                placeholder.style.display = 'none';
            }
            
            // Tambahkan gambar ke slide
            slideElement.insertBefore(img, slideElement.firstChild);
            
            // Tambahkan kelas fade-in untuk animasi
            setTimeout(() => {
                img.classList.add('fade-in');
            }, 50);
        };
        
        img.onerror = function() {
            console.log(`[CAROUSEL] Gambar ${product.image} tidak ditemukan`);
            
            // Update placeholder untuk menunjukkan error
            const placeholder = slideElement.querySelector(`#slidePlaceholder${index}`);
            if (placeholder) {
                placeholder.innerHTML = `
                    <i class="${product.icon}" style="font-size: 4rem;"></i>
                    <p>${product.name}</p>
                    <p style="font-size: 0.8rem; color: #ff6b6b;">(gambar tidak ditemukan)</p>
                `;
            }
        };
        
        // Mulai load gambar
        img.src = product.image;
    }

    function updateCarouselIndicators() {
        const indicatorsContainer = document.getElementById('carouselIndicators');
        if (!indicatorsContainer) return;
        
        indicatorsContainer.innerHTML = '';
        
        productsWithImages.forEach((_, index) => {
            const indicator = document.createElement('div');
            indicator.className = 'carousel-indicator';
            if (index === currentSlideIndex) {
                indicator.classList.add('active');
            }
            indicator.onclick = () => goToSlide(index);
            indicatorsContainer.appendChild(indicator);
        });
    }

    function goToSlide(index, instant = false) {
        const totalSlides = productsWithImages.length;
        
        // Handle infinite loop
        if (index < 0) {
            index = totalSlides - 1;
        } else if (index >= totalSlides) {
            index = 0;
        }
        
        currentSlideIndex = index;
        
        const carouselTrack = document.getElementById('infiniteCarouselTrack');
        if (carouselTrack) {
            // Set transition untuk efek smooth
            carouselTrack.style.transition = instant ? 'none' : 'transform 2s ease';
            
            // Geser ke slide yang dipilih
            carouselTrack.style.transform = `translateX(-${currentSlideIndex * 100}%)`;
            
            // Update indikator
            updateCarouselIndicators();
        }
        
        console.log(`[CAROUSEL] Pindah ke slide: ${currentSlideIndex}`);
        
        // Reset auto slide timer
        if (!instant) {
            restartAutoSlide();
        }
    }

    function nextSlide() {
        goToSlide(currentSlideIndex + 1);
    }

    function prevSlide() {
        goToSlide(currentSlideIndex - 1);
    }

    function startAutoSlide() {
        // Hentikan interval sebelumnya jika ada
        stopAutoSlide();
        
        // Mulai interval baru
        autoSlideInterval = setInterval(() => {
            nextSlide();
        }, 5000); // Ganti slide setiap 5 detik
    }

    function stopAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
            autoSlideInterval = null;
        }
    }

    function restartAutoSlide() {
        stopAutoSlide();
        startAutoSlide();
    }

    // ==================== TOUCH EVENTS UNTUK CAROUSEL ====================
    function setupCarouselTouchEvents() {
        const carouselTrack = document.getElementById('infiniteCarouselTrack');
        if (!carouselTrack) return;
        
        // Mouse events untuk desktop
        carouselTrack.addEventListener('mousedown', startDrag);
        carouselTrack.addEventListener('mousemove', drag);
        carouselTrack.addEventListener('mouseup', endDrag);
        carouselTrack.addEventListener('mouseleave', endDrag);
        
        // Touch events untuk mobile
        carouselTrack.addEventListener('touchstart', startDragTouch);
        carouselTrack.addEventListener('touchmove', dragTouch);
        carouselTrack.addEventListener('touchend', endDrag);
    }

    function removeCarouselTouchEvents() {
        const carouselTrack = document.getElementById('infiniteCarouselTrack');
        if (!carouselTrack) return;
        
        carouselTrack.removeEventListener('mousedown', startDrag);
        carouselTrack.removeEventListener('mousemove', drag);
        carouselTrack.removeEventListener('mouseup', endDrag);
        carouselTrack.removeEventListener('mouseleave', endDrag);
        
        carouselTrack.removeEventListener('touchstart', startDragTouch);
        carouselTrack.removeEventListener('touchmove', dragTouch);
        carouselTrack.removeEventListener('touchend', endDrag);
    }

    function startDrag(e) {
        isDragging = true;
        startPosX = e.clientX;
        prevTranslate = currentTranslate;
        
        const carouselTrack = document.getElementById('infiniteCarouselTrack');
        if (carouselTrack) {
            carouselTrack.style.transition = 'none';
        }
        
        // Hentikan auto slide saat drag
        stopAutoSlide();
    }

    function startDragTouch(e) {
        isDragging = true;
        startPosX = e.touches[0].clientX;
        prevTranslate = currentTranslate;
        
        const carouselTrack = document.getElementById('infiniteCarouselTrack');
        if (carouselTrack) {
            carouselTrack.style.transition = 'none';
        }
        
        // Hentikan auto slide saat drag
        stopAutoSlide();
    }

    function drag(e) {
        if (!isDragging) return;
        e.preventDefault();
        
        const currentPosX = e.clientX;
        const diff = currentPosX - startPosX;
        const carouselTrack = document.getElementById('infiniteCarouselTrack');
        
        if (carouselTrack) {
            // Batasi drag maksimal 100px
            const limitedDiff = Math.max(Math.min(diff, 100), -100);
            carouselTrack.style.transform = `translateX(calc(-${currentSlideIndex * 100}% + ${limitedDiff}px))`;
        }
    }

    function dragTouch(e) {
        if (!isDragging) return;
        e.preventDefault();
        
        const currentPosX = e.touches[0].clientX;
        const diff = currentPosX - startPosX;
        const carouselTrack = document.getElementById('infiniteCarouselTrack');
        
        if (carouselTrack) {
            // Batasi drag maksimal 100px
            const limitedDiff = Math.max(Math.min(diff, 100), -100);
            carouselTrack.style.transform = `translateX(calc(-${currentSlideIndex * 100}% + ${limitedDiff}px))`;
        }
    }

    function endDrag() {
        if (!isDragging) return;
        isDragging = false;
        
        const carouselTrack = document.getElementById('infiniteCarouselTrack');
        if (!carouselTrack) return;
        
        const diff = currentTranslate - prevTranslate;
        
        // Jika drag cukup jauh, pindah slide
        if (Math.abs(diff) > 50) {
            if (diff > 0) {
                prevSlide();
            } else {
                nextSlide();
            }
        } else {
            // Kembali ke posisi semula
            goToSlide(currentSlideIndex);
        }
        
        // Reset nilai
        startPosX = 0;
        currentTranslate = 0;
        prevTranslate = 0;
        
        // Mulai auto slide lagi
        restartAutoSlide();
    }

    // ==================== FUNGSI TANYA HARGA DARI SLIDE ====================
    function askProductFromSlide(index) {
        console.log(`[CAROUSEL] Menanya harga dari slide index: ${index}`);
        
        if (index < 0 || index >= productsWithImages.length) {
            console.error(`[CAROUSEL] Index slide tidak valid: ${index}`);
            showNotification('Produk tidak ditemukan', 'error');
            return;
        }
        
        const product = productsWithImages[index];
        console.log(`[CAROUSEL] Produk ditemukan: ${product.name}, Query: "${product.query}"`);
        
        // Tutup modal produk
        closeProductsModal();
        
        // Buka chatbot dan kirim query
        openChatbot(() => {
            console.log(`[CHAT] Chat terbuka, mengirim query: "${product.query}"`);
            openChatAndAsk(product.query);
        });
    }

    function openChatAndAsk(query) {
        console.log(`[CHAT] openChatAndAsk dipanggil dengan query: "${query}"`);
        
        const input = document.getElementById('userInput');
        
        if (!input) {
            console.error('[CHAT] Input element tidak ditemukan!');
            return;
        }
        
        const chatContainer = document.getElementById('chatbotContainer');
        if (!chatContainer.classList.contains('active')) {
            console.error('[CHAT] Chat container tidak aktif!');
            return;
        }
        
        console.log(`[CHAT] Mengisi input dengan: "${query}"`);
        input.value = query;
        
        setTimeout(() => {
            console.log(`[CHAT] Memanggil sendMessage() untuk: "${query}"`);
            sendMessage();
        }, 50);
    }

    // ==================== FUNGSI CHATBOT ====================
    function handleEscapeKey(event) {
        if (event.key === 'Escape') {
            if (isChatbotOpen) {
                console.log('[CHAT] Menutup chat dengan Escape');
                closeChatbot();
            }
            if (isProductsModalOpen) {
                console.log('[MODAL] Menutup modal dengan Escape');
                closeProductsModal();
            }
        }
    }

    function handleDocumentClick(event) {
        const chatbotContainer = document.getElementById('chatbotContainer');
        const chatbotTrigger = document.getElementById('chatbotTrigger');
        
        if (isChatbotOpen && 
            !chatbotContainer.contains(event.target) && 
            !chatbotTrigger.contains(event.target)) {
            console.log('[CHAT] Menutup chat karena klik di luar');
            closeChatbot();
        }
    }

    function toggleChatbot() {
        console.log(`[CHAT] Toggle chat, status sebelumnya: ${isChatbotOpen}`);
        if (isChatbotOpen) {
            closeChatbot();
        } else {
            openChatbot();
        }
    }

    function openChatbot(onOpenCallback = null) {
        console.log('[CHAT] Membuka chat');
        
        const container = document.getElementById('chatbotContainer');
        const trigger = document.getElementById('chatbotTrigger');
        const overlay = document.getElementById('chatbotOverlay');
        
        container.classList.add('active');
        container.style.display = 'flex';
        trigger.classList.add('active');
        overlay.classList.add('active');
        isChatbotOpen = true;
        
        setTimeout(() => {
            const input = document.getElementById('userInput');
            input.focus();
            scrollToBottom();
            
            console.log(`[CHAT] Chat terbuka, callback: ${onOpenCallback ? 'tersedia' : 'tidak tersedia'}`);
            
            if (onOpenCallback && typeof onOpenCallback === 'function') {
                onOpenCallback();
            }
        }, 100);
    }

    function closeChatbot() {
        console.log('[CHAT] Menutup chat');
        saveLastChatHistory();
        
        const container = document.getElementById('chatbotContainer');
        const trigger = document.getElementById('chatbotTrigger');
        const overlay = document.getElementById('chatbotOverlay');
        
        container.classList.remove('active');
        container.style.display = 'none';
        trigger.classList.remove('active');
        overlay.classList.remove('active');
        isChatbotOpen = false;
    }

    // ==================== FUNGSI CHAT MESSAGES ====================
    function showWelcomeMessage() {
        const chatArea = document.getElementById('chatArea');
        const hasMessages = chatArea.querySelectorAll('.message').length > 0;
        
        if (!hasMessages) {
            console.log('[CHAT] Menampilkan welcome message');
            const welcomeText = `**hi, ada yang bisa kami bantu kak**`;
            addMessageToChat('bot', welcomeText, false);
        }
    }

    async function sendMessage() {
        console.log(`[CHAT] sendMessage() dipanggil, isProcessing: ${isProcessing}`);
        
        if (isProcessing) {
            console.log('[CHAT] Masih memproses, abaikan request');
            showNotification('Tunggu respons sebelumnya', 'warning');
            return;
        }
        
        const input = document.getElementById('userInput');
        const message = input.value.trim();
        
        console.log(`[CHAT] Pesan yang akan dikirim: "${message}"`);
        
        if (!message) {
            console.log('[CHAT] Pesan kosong, abaikan');
            showNotification('Masukkan pesan terlebih dahulu', 'warning');
            return;
        }
        
        // Tampilkan pesan user
        console.log(`[CHAT] Menambahkan pesan user ke UI: "${message}"`);
        addMessageToChat('user', message);
        input.value = '';
        
        // Simpan hanya 1 histori terakhir
        saveLastChatHistory();
        
        // Tampilkan typing indicator
        console.log('[CHAT] Menampilkan typing indicator');
        showTyping(true);
        
        // Nonaktifkan UI
        isProcessing = true;
        console.log('[CHAT] isProcessing = true');
        document.getElementById('sendBtn').disabled = true;
        input.disabled = true;
        
        try {
            console.log(`[API] Mengirim request ke: ${API_URL}/chat`);
            console.log(`[API] Data:`, {
                question: message,
                session_id: currentSessionId
            });
            
            const response = await fetch(`${API_URL}/chat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    question: message,
                    session_id: currentSessionId
                })
            });
            
            console.log(`[API] Response status: ${response.status}`);
            
            if (!response.ok) {
                throw new Error(`Gagal mengirim pesan (status: ${response.status})`);
            }
            
            const data = await response.json();
            console.log('[API] Response data:', data);
            
            // Tunggu agar typing terlihat natural
            await delay(800);
            
            // Tampilkan respons
            console.log('[CHAT] Menyembunyikan typing indicator');
            showTyping(false);
            
            if (data.status === 'success') {
                console.log(`[CHAT] Menambahkan respons bot: ${data.response.substring(0, 50)}...`);
                addMessageToChat('bot', data.response);
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
            
        } catch (error) {
            console.error('[ERROR] Detail error:', error);
            
            showTyping(false);
            const errorMessage = `**Maaf, sistem sementara tidak dapat diakses kak, silahkan hubungin admin langsung 081234567890 Atau coba lagi beberapa saat lagi.`;

            console.log('[CHAT] Menampilkan error message');
            addMessageToChat('bot', errorMessage);
        }
        
        // Aktifkan kembali UI
        isProcessing = false;
        console.log('[CHAT] isProcessing = false');
        document.getElementById('sendBtn').disabled = false;
        input.disabled = false;
        
        // Simpan hanya 1 histori terakhir
        saveLastChatHistory();
        
        input.focus();
        console.log('[CHAT] Proses selesai');
    }

    function addMessageToChat(sender, text, shouldSave = true) {
        console.log(`[CHAT] addMessageToChat: ${sender}, panjang: ${text.length} chars`);
        
        const chatArea = document.getElementById('chatArea');
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const formattedText = formatMessageText(text);
        
        messageDiv.innerHTML = `
            <div class="message-content">${formattedText}</div>
            <div style="font-size: 0.7rem; color: #888; margin-top: 5px; text-align: ${sender === 'user' ? 'right' : 'left'};">${timeString}</div>
        `;
        
        chatArea.appendChild(messageDiv);
        scrollToBottom();
        
        if (shouldSave) {
            saveLastChatHistory();
        }
    }

    function handleEnter(event) {
        if (event.key === 'Enter' && !isProcessing) {
            console.log('[CHAT] Enter ditekan, memanggil sendMessage()');
            sendMessage();
        }
    }

    // ==================== FUNGSI TYPING & NOTIFICATION ====================
    function showTyping(show) {
        console.log(`[CHAT] showTyping: ${show}`);
        document.getElementById('typingIndicator').style.display = show ? 'block' : 'none';
        if (show) scrollToBottom();
    }

    function showNotification(message, type = 'info', duration = 3000) {
        console.log(`[NOTIF] ${type}: ${message}`);
        const notification = document.getElementById('notification');
        notification.textContent = message;
        notification.style.display = 'block';
        
        const colors = {
            'success': '#2e7d32',
            'error': '#f44336',
            'warning': '#ff9800',
            'info': '#1a237e'
        };
        
        notification.style.background = colors[type] || colors.info;
        
        setTimeout(() => {
            notification.style.display = 'none';
        }, duration);
    }

    // ==================== FUNGSI 1 HISTORI TERAKHIR ====================
    function saveLastChatHistory() {
        try {
            const chatArea = document.getElementById('chatArea');
            if (!chatArea) return;
            
            const messages = [];
            const messageElements = chatArea.querySelectorAll('.message');
            
            // Ambil maksimal 2 pesan terakhir (1 user + 1 bot)
            const lastMessages = Array.from(messageElements)
                .slice(-MAX_LAST_MESSAGES * 2);
            
            console.log(`[STORAGE] Menyimpan ${lastMessages.length} pesan terakhir`);
            
            lastMessages.forEach(element => {
                const contentDiv = element.querySelector('.message-content');
                const timeDiv = element.querySelector('div[style*="font-size: 0.7rem"]');
                
                if (contentDiv) {
                    const sender = element.classList.contains('user-message') ? 'user' : 'bot';
                    const content = contentDiv.innerHTML;
                    const time = timeDiv ? timeDiv.textContent : new Date().toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    messages.push({
                        sender: sender,
                        content: content,
                        time: time,
                        timestamp: new Date().toISOString()
                    });
                }
            });
            
            // Simpan ke localStorage
            localStorage.setItem(CHAT_STORAGE_KEY, JSON.stringify(messages));
            
        } catch (error) {
            console.error('[STORAGE] Error saving chat history:', error);
        }
    }

    function loadLastChatHistory() {
        try {
            const savedChat = localStorage.getItem(CHAT_STORAGE_KEY);
            const chatArea = document.getElementById('chatArea');
            
            // Clear chat area dulu
            chatArea.innerHTML = '';
            
            if (savedChat) {
                const messages = JSON.parse(savedChat);
                console.log(`[STORAGE] Memuat ${messages.length} pesan dari storage`);
                
                // Tampilkan hanya pesan yang tersimpan
                messages.forEach(msg => {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = `message ${msg.sender}-message`;
                    messageDiv.innerHTML = `
                        <div class="message-content">${msg.content}</div>
                        <div style="font-size: 0.7rem; color: #888; margin-top: 5px; text-align: ${msg.sender === 'user' ? 'right' : 'left'};">${msg.time}</div>
                    `;
                    chatArea.appendChild(messageDiv);
                });
            }
            
            // Selalu tampilkan welcome message jika tidak ada chat
            if (chatArea.children.length === 0) {
                showWelcomeMessage();
            }
            
            scrollToBottom();
            
        } catch (error) {
            console.error('[STORAGE] Error loading chat history:', error);
            
            // Jika error, tampilkan welcome message saja
            const chatArea = document.getElementById('chatArea');
            chatArea.innerHTML = '';
            showWelcomeMessage();
        }
    }

    function clearChatHistory() {
        if (confirm('Apakah Anda yakin ingin menghapus riwayat percakapan?')) {
            console.log('[STORAGE] Menghapus chat history');
            
            // Hapus dari localStorage
            localStorage.removeItem(CHAT_STORAGE_KEY);
            
            // Clear chat area
            const chatArea = document.getElementById('chatArea');
            chatArea.innerHTML = '';
            
            // Tampilkan welcome message baru
            showWelcomeMessage();
            
            // Notifikasi
            showNotification('Riwayat percakapan telah dihapus', 'success');
        }
    }

    // ==================== FUNGSI LAINNYA ====================
    async function checkConnection() {
        try {
            console.log('[API] Mengecek koneksi ke API');
            const response = await fetch(`${API_URL}/health`, {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            });
            
            if (response.ok) {
                console.log('[API] Koneksi OK');
                return true;
            } else {
                console.log('[API] Koneksi gagal, status:', response.status);
                return false;
            }
        } catch (error) {
            console.log('[API] Koneksi error:', error.message);
            return false;
        }
    }

    function scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Handle resize untuk mobile
    window.addEventListener('resize', () => {
        const vh = window.innerHeight;
        const chatContainer = document.getElementById('chatbotContainer');
        if (chatContainer) {
            chatContainer.style.maxHeight = (vh - 50) + 'px';
        }
        scrollToBottom();
    });

    // Focus input handler untuk mobile
    const input = document.getElementById('userInput');
    input.addEventListener('focus', () => {
        setTimeout(scrollToBottom, 300);
    });

    // ==================== DEBUG FUNCTIONS ====================
    function debugProductSelection() {
        console.log('=== DEBUG PRODUCT SELECTION ===');
        console.log('Products array:', productsWithImages);
        console.log('Current slide:', currentSlideIndex);
        console.log('Total slides:', productsWithImages.length);
        console.log('isProcessing:', isProcessing);
        console.log('isChatbotOpen:', isChatbotOpen);
        console.log('isProductsModalOpen:', isProductsModalOpen);
        console.log('==============================');
    }

    // Ekspor fungsi untuk akses global
    window.askProductFromSlide = askProductFromSlide;
    window.nextSlide = nextSlide;
    window.prevSlide = prevSlide;
    window.openProductsModal = openProductsModal;
    window.closeProductsModal = closeProductsModal;
    window.toggleChatbot = toggleChatbot;
    window.closeChatbot = closeChatbot;
    window.sendMessage = sendMessage;
    window.clearChatHistory = clearChatHistory;
    window.handleEnter = handleEnter;
    window.debugProductSelection = debugProductSelection;
    
    // Inisialisasi chat history
    loadLastChatHistory();
