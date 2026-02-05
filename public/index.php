<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shoggun's Supper | Tokyo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600&family=Manrope:wght@300;400;600&family=Noto+Serif+JP:wght@400;700&display=swap" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/gh/studio-freight/lenis@1.0.29/bundled/lenis.min.js"></script>

    <style>
        :root {
            --bg-color: #F0EFEB; 
            --text-color: #1a1a1a;
        }
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Manrope', sans-serif;
            overflow-x: hidden;
        }
        .serif { font-family: 'Cinzel', serif; }
        .jp { font-family: 'Noto Serif JP', serif; }
        
        html.lenis { height: auto; }
        .lenis.lenis-smooth { scroll-behavior: auto; }

        .nav-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: #E6E2dd;
            z-index: 50;
            clip-path: circle(0% at 95% 5%);
            transition: clip-path 0.8s cubic-bezier(0.65, 0, 0.35, 1);
            display: flex; align-items: center; justify-content: center;
        }
        .nav-overlay.active { clip-path: circle(150% at 100% 0%); }

        .hero-wrap video { width: 100%; height: 100%; object-fit: cover; }
        .overlay { background: rgba(0,0,0,0.7); position: absolute; inset: 0; }
        .tate-gaki { writing-mode: vertical-rl; text-orientation: upright; letter-spacing: 0.5em; }

        .clip-circle { clip-path: circle(0% at 50% 50%); }

        .slice-container { display: flex; width: 100%; height: 100%; overflow: hidden; }
        .slice { 
            flex: 1; 
            height: 100%; 
            background-image: url('https://images.unsplash.com/photo-1599458347893-b695c0d29729?q=80&w=2000&auto=format&fit=crop'); 
            background-size: 400% 100%; 
            background-repeat: no-repeat;
            border-left: 1px solid rgba(0,0,0,0.2);
        }
        .slice:nth-child(1) { background-position: 0% 50%; }
        .slice:nth-child(2) { background-position: 33.33% 50%; }
        .slice:nth-child(3) { background-position: 66.66% 50%; }
        .slice:nth-child(4) { background-position: 100% 50%; }

        .curtain-panel { position: absolute; top: 0; height: 100%; width: 50%; background: #F0EFEB; z-index: 10; }
        .curtain-left { left: 0; border-right: 1px solid rgba(0,0,0,0.1); }
        .curtain-right { right: 0; border-left: 1px solid rgba(0,0,0,0.1); }
    </style>
</head>
<body>

    <button onclick="toggleMenu()" class="fixed top-8 right-8 z-[60] group flex flex-col items-end gap-1.5 cursor-pointer mix-blend-difference">
        <span class="w-8 h-[2px] bg-white group-hover:w-10 transition-all"></span>
        <span class="w-5 h-[2px] bg-white group-hover:w-10 transition-all"></span>
        <span class="text-[10px] text-white uppercase tracking-widest mt-1">Menu</span>
    </button>

    <div class="nav-overlay" id="menu-overlay">
        <div class="text-center space-y-6">
            <div class="overflow-hidden"><a href="menu.php" class="block serif text-6xl md:text-8xl text-neutral-800 hover:text-red-900 nav-item opacity-0 translate-y-10">THE MENU</a></div>
            <div class="overflow-hidden"><span class="block serif text-6xl md:text-8xl text-neutral-400 cursor-not-allowed nav-item opacity-0 translate-y-10">LOCATIONS</span></div>
        </div>
    </div>

    <header class="relative w-full h-screen flex flex-col items-center justify-center overflow-hidden">
        <div class="absolute inset-0 hero-wrap z-0">
             <video autoplay muted loop playsinline>
                <source src="img/79900-570532758.mp4" type="video/mp4">
            </video>
        </div>
        <div class="overlay"></div>

        <div class="relative z-10 text-white flex flex-col items-center">
            <h1 class="serif text-lg md:text-xl tracking-[0.5em] font-bold uppercase mb-12 hero-anim opacity-0">
                Shoggun's Supper
            </h1>
            <div class="jp text-8xl md:text-9xl font-black tate-gaki select-none hero-anim opacity-0 blur-sm mb-12">
                天才
            </div>
            <div class="text-center max-w-md hero-anim opacity-0 translate-y-4">
                <p class="text-[10px] md:text-xs tracking-[0.4em] uppercase opacity-80 leading-loose">
                    The Art of Precision Ramen<br>
                    Tokyo • New York • London
                </p>
                <div class="h-12 w-[1px] bg-white/30 mx-auto mt-8"></div>
            </div>
        </div>
    </header>

    <section class="relative h-[150vh] bg-[#F0EFEB] flex items-center justify-center overflow-hidden trigger-1">
        <div class="absolute z-0 text-center">
            <span class="text-red-900 text-xs font-bold tracking-widest uppercase mb-4 block">01. Craft</span>
            <h2 class="serif text-6xl md:text-8xl">KODAWARI</h2>
        </div>
        <div class="absolute inset-0 z-10 flex items-center justify-center pointer-events-none">
            <div class="clip-circle w-full h-full bg-black">
                <img src="img/pexels-tian-jin-505460776-28988072.jpg" class="w-full h-full object-cover opacity-90 scale-125 img-zoom">
                <div class="absolute bottom-10 w-full text-center text-white">
                    <p class="text-sm tracking-widest uppercase font-bold">Hokkaido Wheat • 12% Protein</p>
                </div>
            </div>
        </div>
    </section>

    <section class="relative h-[120vh] bg-black flex items-center justify-center overflow-hidden trigger-2">
        <div class="absolute inset-0 opacity-20">
            <img src="img/pexels-airamdphoto-16388598.jpg" class="w-full h-full object-cover blur-md">
        </div>
        <div class="slice-container w-full h-full max-w-7xl mx-auto relative z-10">
            <div class="slice translate-y-[-100%]"></div>
            <div class="slice translate-y-[100%]"></div>
            <div class="slice translate-y-[-100%]"></div>
            <div class="slice translate-y-[100%]"></div>
        </div>
        <div class="absolute z-20 text-center mix-blend-difference">
            <span class="text-orange-600 text-xs tracking-[0.5em] uppercase font-bold mb-4 block">Process</span>
            <h2 class="serif text-7xl md:text-9xl font-black text-white">FIRE</h2>
        </div>
    </section>

    <section class="relative h-[150vh] bg-[#F0EFEB] flex items-center justify-center overflow-hidden trigger-3">
        <div class="absolute inset-0 z-0">
            <img src="img/pexels-guilherme-simao-429126551-31317031.jpg" class="w-full h-full object-cover scale-110 final-dish">
            <div class="absolute inset-0 bg-black/30"></div>
        </div>
        <div class="curtain-panel curtain-left flex items-center justify-end pr-8">
            <h2 class="serif text-6xl md:text-8xl translate-x-20 opacity-0 curtain-text-l">THE</h2>
        </div>
        <div class="curtain-panel curtain-right flex items-center justify-start pl-8">
             <h2 class="serif text-6xl md:text-8xl -translate-x-20 opacity-0 curtain-text-r">SOUL</h2>
        </div>
        <div class="absolute bottom-20 z-20 text-center opacity-0 cta-reveal">
            <a href="menu.php" class="bg-white text-black px-10 py-4 text-xs tracking-widest uppercase hover:bg-red-900 hover:text-white transition duration-300">
                VIEW OUR MENU
            </a>
        </div>
    </section>

    <footer class="bg-[#1a1a1a] text-[#F0EFEB] py-24 text-center">
        <div class="jp text-4xl mb-6">天才</div>
        <p class="text-xs uppercase tracking-widest text-gray-500">© 2026 Shoggun's Supper.</p>
    </footer>

    <script>
        const lenis = new Lenis({ duration: 1.8, easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)), smooth: true });
        function raf(time) { lenis.raf(time); requestAnimationFrame(raf); }
        requestAnimationFrame(raf);

        gsap.registerPlugin(ScrollTrigger);

        gsap.to(".hero-anim", { opacity: 1, y: 0, filter: "blur(0px)", duration: 1.5, stagger: 0.3, delay: 0.5 });

        let isMenuOpen = false;
        function toggleMenu() {
            isMenuOpen = !isMenuOpen;
            document.getElementById('menu-overlay').classList.toggle('active', isMenuOpen);
            gsap.to(".nav-item", { y: isMenuOpen ? 0 : 40, opacity: isMenuOpen ? 1 : 0, stagger: 0.1 });
        }

        gsap.timeline({ scrollTrigger: { trigger: ".trigger-1", start: "top top", end: "+=100%", scrub: true, pin: true } })
            .to(".clip-circle", { clipPath: "circle(100% at 50% 50%)", ease: "none" })
            .to(".img-zoom", { scale: 1, ease: "none" }, "<");

        gsap.to(".slice", { y: "0%", ease: "power3.out", scrollTrigger: { trigger: ".trigger-2", start: "top center", end: "bottom bottom", scrub: 1 } });

        gsap.timeline({ scrollTrigger: { trigger: ".trigger-3", start: "top top", end: "+=150%", scrub: true, pin: true } })
            .to(".curtain-text-l, .curtain-text-r", { opacity: 1, x: 0, duration: 0.5 })
            .to(".curtain-left", { x: "-100%", duration: 2 }, "+=0.2")
            .to(".curtain-right", { x: "100%", duration: 2 }, "<")
            .to(".final-dish", { scale: 1, duration: 2 }, "<")
            .to(".cta-reveal", { opacity: 1, y: -20, duration: 0.5 });
    </script>
</body>
</html>
