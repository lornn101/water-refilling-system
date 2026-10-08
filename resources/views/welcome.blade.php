<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Poblacion Water Refilling Station</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS via CDN (for this page only) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #0c4a6e 0%, #0891b2 50%, #06b6d4 100%);
        }
        .service-card:hover {
            transform: translateY(-8px);
            transition: all 0.3s ease;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .service-card {
            transition: all 0.3s ease;
        }
        .water-drop {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .btn-primary {
            background: linear-gradient(135deg, #0c4a6e, #0891b2);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #0891b2, #0c4a6e);
        }
    </style>
</head>
<body class="font-sans antialiased">
    
    <!-- ========== NAVIGATION ========== -->
    <nav class="bg-white/95 backdrop-blur-sm shadow-sm fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                    <span class="ml-2 text-xl font-bold text-gray-800">Poblacion Water</span>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-gray-700 hover:text-blue-600 transition">Home</a>
                    <a href="#services" class="text-gray-700 hover:text-blue-600 transition">Services</a>
                    <a href="#about" class="text-gray-700 hover:text-blue-600 transition">About</a>
                    <a href="#contact" class="text-gray-700 hover:text-blue-600 transition">Contact</a>
                    
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md text-sm font-medium">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 transition">Login</a>
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md text-sm font-medium">
                                Register
                            </a>
                        @endauth
                    @endif
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="text-gray-700 hover:text-blue-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu (hidden by default) -->
            <div id="mobile-menu" class="md:hidden hidden pb-4">
                <div class="flex flex-col space-y-3">
                    <a href="#home" class="text-gray-700 hover:text-blue-600 transition">Home</a>
                    <a href="#services" class="text-gray-700 hover:text-blue-600 transition">Services</a>
                    <a href="#about" class="text-gray-700 hover:text-blue-600 transition">About</a>
                    <a href="#contact" class="text-gray-700 hover:text-blue-600 transition">Contact</a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-center text-sm font-medium">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 transition">Login</a>
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-center text-sm font-medium">
                                Register
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- ========== HERO SECTION ========== -->
    <section id="home" class="hero-gradient min-h-screen flex items-center pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="text-white">
                    <div class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm mb-6">
                        <span class="inline-block w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                        Pure, Clean & Safe
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight">
                        Poblacion's Trusted
                        <br>
                        <span class="text-cyan-200">Water Refilling Station</span>
                    </h1>
                    <p class="mt-6 text-lg text-white/80 max-w-lg">
                        Providing clean, safe, and affordable drinking water to the residents of 
                        Poblacion, Bilar, Bohol since 2024.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="px-6 py-3 bg-white text-blue-700 rounded-lg font-medium hover:bg-gray-100 transition shadow-lg">
                                    Go to Dashboard
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="px-6 py-3 bg-white text-blue-700 rounded-lg font-medium hover:bg-gray-100 transition shadow-lg">
                                    Get Started
                                </a>
                                <a href="{{ route('login') }}" class="px-6 py-3 border-2 border-white/30 text-white rounded-lg font-medium hover:bg-white/10 transition">
                                    Sign In
                                </a>
                            @endauth
                        @endif
                    </div>
                    <div class="mt-8 flex items-center space-x-8 text-white/70 text-sm">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            100% Purified
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                            Door-to-Door Delivery
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Affordable Rates
                        </div>
                    </div>
                </div>
                <div class="flex justify-center lg:justify-end">
                    <div class="relative">
    <div class="w-64 h-64 md:w-80 md:h-80 bg-white/10 backdrop-blur-sm rounded-full flex items-center justify-center water-drop">
        <svg class="w-32 h-32 md:w-40 md:h-40 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
        </svg>
    </div>
</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== SERVICES SECTION ========== -->
    <section id="services" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium mb-4">
                    What We Offer
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Our Services</h2>
                <p class="mt-4 text-gray-500 max-w-2xl mx-auto">
                    Poblacion Water Refilling Station provides clean, safe drinking water with convenient services.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Service 1: Refill -->
                <div class="service-card bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Water Refill</h3>
                    <p class="mt-2 text-gray-500 text-sm">
                        Bring your standard 5-gallon container and get it refilled with purified, safe drinking water at our station.
                    </p>
                    <div class="mt-4 flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Walk-in available
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('register') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                            Get Refill →
                        </a>
                    </div>
                </div>

                <!-- Service 2: Delivery -->
                <div class="service-card bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <div class="w-16 h-16 bg-cyan-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM3 7h18M3 7a2 2 0 012-2h14a2 2 0 012 2M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Door-to-Door Delivery</h3>
                    <p class="mt-2 text-gray-500 text-sm">
                        We deliver purified water straight to your doorstep. Schedule your delivery and enjoy hassle-free refills.
                    </p>
                    <div class="mt-4 flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Free delivery within Poblacion
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('register') }}" class="inline-flex items-center text-cyan-600 hover:text-cyan-800 font-medium">
                            Order Delivery →
                        </a>
                    </div>
                </div>

                <!-- Service 3: Bulk Orders -->
                <div class="service-card bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Bulk Orders</h3>
                    <p class="mt-2 text-gray-500 text-sm">
                        For events, offices, and establishments. We can supply large quantities of purified water at special rates.
                    </p>
                    <div class="mt-4 flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Special rates for bulk
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('register') }}" class="inline-flex items-center text-green-600 hover:text-green-800 font-medium">
                            Inquire Now →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Additional Service Info -->
            <div class="mt-12 bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-center">
                    <div>
                        <p class="text-3xl font-bold text-blue-600">₱20</p>
                        <p class="text-sm text-gray-500">Standard Refill</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-cyan-600">8 AM to 5 PM</p>
                        <p class="text-sm text-gray-500">Service Hours</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-green-600">100%</p>
                        <p class="text-sm text-gray-500">Purified Water</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-purple-600">Minimum of 30 mins</p>
                        <p class="text-sm text-gray-500">Delivery Time</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== ABOUT SECTION ========== -->
    <section id="about" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium mb-4">
                        About Us
                    </span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800">
                        Clean Water, Happy <span class="text-blue-600">Community</span>
                    </h2>
                    <p class="mt-4 text-gray-500 leading-relaxed">
                        Poblacion Water Refilling Station is a locally-owned business dedicated to providing 
                        the highest quality drinking water to the residents of Poblacion, Bilar, Bohol.
                    </p>
                    <p class="mt-4 text-gray-500 leading-relaxed">
                        We use state-of-the-art filtration and purification systems to ensure every drop of 
                        water we provide is safe, clean, and refreshing.
                    </p>
                    <div class="mt-6 grid grid-cols-2 gap-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">NSF-Certified Filtration</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Regular Quality Testing</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Eco-Friendly Practices</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Locally Owned</span>
                        </div>
                    </div>
                </div>
                <div class="bg-blue-50 rounded-2xl p-8 border border-blue-100">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white rounded-xl p-6 text-center shadow-sm">
                            <p class="text-3xl font-bold text-blue-600">200+</p>
                            <p class="text-sm text-gray-500">Happy Customers</p>
                        </div>
                        <div class="bg-white rounded-xl p-6 text-center shadow-sm">
                            <p class="text-3xl font-bold text-cyan-600">1,000+</p>
                            <p class="text-sm text-gray-500">Gallons Delivered</p>
                        </div>
                        <div class="bg-white rounded-xl p-6 text-center shadow-sm">
                            <p class="text-3xl font-bold text-green-600">4.9</p>
                            <p class="text-sm text-gray-500">Customer Rating</p>
                        </div>
                        <div class="bg-white rounded-xl p-6 text-center shadow-sm">
                            <p class="text-3xl font-bold text-purple-600">2024</p>
                            <p class="text-sm text-gray-500">Year Founded</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== CONTACT SECTION ========== -->
    <section id="contact" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium mb-4">
                    Get in Touch
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Contact Us</h2>
                <p class="mt-4 text-gray-500 max-w-2xl mx-auto">
                    Have questions or want to place an order? Reach out to us anytime.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-100">
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Visit Us</h3>
                    <p class="mt-2 text-gray-500 text-sm">Poblacion, Bilar, Bohol</p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-100">
                    <div class="w-14 h-14 bg-cyan-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Call Us</h3>
                    <p class="mt-2 text-gray-500 text-sm">0912-345-6789</p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-100">
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Email Us</h3>
                    <p class="mt-2 text-gray-500 text-sm">poblacionwater@email.com</p>
                </div>
            </div>

            <!-- Map Placeholder -->
            <div class="mt-8 bg-white rounded-2xl shadow-lg p-4 border border-gray-100">
                <div class="bg-gray-200 rounded-xl h-48 flex items-center justify-center text-gray-400">
                    <p class="text-sm">📍 Map of Poblacion, Bilar, Bohol (Google Maps Integration)</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== FOOTER ========== -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                        <span class="ml-2 text-xl font-bold">Poblacion Water</span>
                    </div>
                    <p class="text-gray-400 text-sm">Pure, clean, and safe drinking water for the community of Poblacion, Bilar, Bohol.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#home" class="hover:text-white transition">Home</a></li>
                        <li><a href="#services" class="hover:text-white transition">Services</a></li>
                        <li><a href="#about" class="hover:text-white transition">About</a></li>
                        <li><a href="#contact" class="hover:text-white transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Services</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li>Water Refill</li>
                        <li>Door-to-Door Delivery</li>
                        <li>Bulk Orders</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Contact Info</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li>📍 Poblacion, Bilar, Bohol</li>
                        <li>📞 0912-345-6789</li>
                        <li>📧 poblacionwater@email.com</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-6 text-center text-gray-500 text-sm">
                &copy; 2024 Poblacion Water Refilling Station. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Mobile Menu Toggle Script -->
    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>

</body>
</html>