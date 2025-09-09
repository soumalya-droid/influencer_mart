<?php require_once __DIR__ . '/partials/header.php'; ?>

    <main>
        <!-- Hero Section -->
        <section class="hero-section text-white py-24 md:py-32">
            <div class="container mx-auto px-6 text-center">
                <div class="max-w-3xl mx-auto">
                    <h1 class="text-4xl md:text-6xl font-extrabold mb-4 leading-tight hero-headline">Find Top Influencers. Launch Campaigns Effortlessly.</h1>
                    <p class="text-lg md:text-xl mb-8 text-gray-200 hero-subtext">Connect brands with the right influencers, manage bids, and collaborate seamlessly.</p>
                    <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-4 hero-buttons">
                        <a href="#campaigns" class="btn btn-light shadow-xl text-lg">Find Campaigns</a>
                        <a href="#" class="btn btn-outline-light shadow-xl text-lg">Join as Influencer</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-6">
                <div class="grid md:grid-cols-3 gap-12 text-center">
                    <div class="feature-card p-8 bg-gray-50 rounded-xl" data-aos="fade-up" data-aos-delay="100">
                        <i class="fas fa-bullseye fa-3x mb-4 text-purple-600 feature-icon"></i>
                        <h4 class="text-xl font-bold mb-3">Targeted Campaigns</h4>
                        <p class="text-gray-500">Reach the right audience with precise influencer matches for your brand campaigns.</p>
                    </div>
                    <div class="feature-card p-8 bg-gray-50 rounded-xl" data-aos="fade-up" data-aos-delay="200">
                        <i class="fas fa-handshake fa-3x mb-4 text-purple-600 feature-icon"></i>
                        <h4 class="text-xl font-bold mb-3">Seamless Collaboration</h4>
                        <p class="text-gray-500">Communicate, approve, and manage influencer bids all in one platform.</p>
                    </div>
                    <div class="feature-card p-8 bg-gray-50 rounded-xl" data-aos="fade-up" data-aos-delay="300">
                        <i class="fas fa-chart-line fa-3x mb-4 text-purple-600 feature-icon"></i>
                        <h4 class="text-xl font-bold mb-3">Track Performance</h4>
                        <p class="text-gray-500">Monitor campaign progress, payments, and reviews with detailed analytics.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Campaigns Section -->
        <section id="campaigns" class="py-20 bg-gray-50">
            <div class="container mx-auto px-6">
                <h2 class="section-heading text-center mb-6">Browse Campaigns</h2>
                <div class="max-w-2xl mx-auto mb-12">
                     <input id="campaignSearch" type="text" placeholder="Search campaigns by title..." class="w-full px-5 py-3 text-lg border-2 border-gray-300 rounded-full focus:outline-none focus:border-purple-500 transition-colors">
                </div>
                <div id="campaigns-grid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Campaign cards will be dynamically inserted here by JavaScript -->
                    <div id="loading-spinner" class="col-span-full flex justify-center items-center py-12">
                        <div class="loader"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Top Influencers Section -->
        <section id="influencers" class="py-20 bg-white">
            <div class="container mx-auto px-6">
                <h2 class="section-heading text-center mb-12">Our Influencers</h2>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Influencer Card 1 -->
                    <div class="influencer-card bg-gray-50 rounded-lg text-center overflow-hidden p-6" data-aos="fade-up">
                        <img src="https://placehold.co/120x120/a3bffa/ffffff?text=User" alt="Alex Doe" class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-white shadow-md">
                        <h5 class="text-lg font-bold mb-1">Alex Doe</h5>
                        <p class="text-gray-500 mb-2 truncate">alex.doe@example.com</p>
                        <p class="text-sm text-gray-400 mb-4">Joined: Aug 2024</p>
                        <a href="#" class="btn btn-outline-primary text-sm">View Profile</a>
                    </div>
                    <!-- Influencer Card 2 -->
                    <div class="influencer-card bg-gray-50 rounded-lg text-center overflow-hidden p-6" data-aos="fade-up" data-aos-delay="100">
                        <img src="https://placehold.co/120x120/d1a3ff/ffffff?text=User" alt="Jessica Smith" class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-white shadow-md">
                        <h5 class="text-lg font-bold mb-1">Jessica Smith</h5>
                        <p class="text-gray-500 mb-2 truncate">jess.smith@example.com</p>
                        <p class="text-sm text-gray-400 mb-4">Joined: Jul 2024</p>
                        <a href="#" class="btn btn-outline-primary text-sm">View Profile</a>
                    </div>
                    <!-- Influencer Card 3 -->
                    <div class="influencer-card bg-gray-50 rounded-lg text-center overflow-hidden p-6" data-aos="fade-up" data-aos-delay="200">
                         <img src="https://placehold.co/120x120/a3e7ff/ffffff?text=User" alt="Mike Johnson" class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-white shadow-md">
                        <h5 class="text-lg font-bold mb-1">Mike Johnson</h5>
                        <p class="text-gray-500 mb-2 truncate">mike.j@example.com</p>
                        <p class="text-sm text-gray-400 mb-4">Joined: Jun 2024</p>
                        <a href="#" class="btn btn-outline-primary text-sm">View Profile</a>
                    </div>
                    <!-- Influencer Card 4 -->
                    <div class="influencer-card bg-gray-50 rounded-lg text-center overflow-hidden p-6" data-aos="fade-up" data-aos-delay="300">
                        <img src="https://placehold.co/120x120/ffdaa3/ffffff?text=User" alt="Sarah Chen" class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-white shadow-md">
                        <h5 class="text-lg font-bold mb-1">Sarah Chen</h5>
                        <p class="text-gray-500 mb-2 truncate">sarah.chen@example.com</p>
                        <p class="text-sm text-gray-400 mb-4">Joined: May 2024</p>
                        <a href="#" class="btn btn-outline-primary text-sm">View Profile</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section id="testimonials" class="py-20 bg-gray-50">
            <div class="container mx-auto px-6">
                <h2 class="section-heading text-center mb-12">What Our Users Say</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="testimonial-card bg-white rounded-lg shadow-sm p-8" data-aos="fade-right">
                        <p class="text-gray-600 mb-6">"This platform revolutionized our marketing strategy. Finding the perfect influencers was never this easy!"</p>
                        <h6 class="font-bold text-purple-700">- Marketing Director, Shopify Brand</h6>
                    </div>
                    <div class="testimonial-card bg-white rounded-lg shadow-sm p-8" data-aos="fade-up">
                        <p class="text-gray-600 mb-6">"As an influencer, the collaboration process is incredibly smooth. Clear communication and timely payments."</p>
                        <h6 class="font-bold text-purple-700">- Jane Doe, Lifestyle Influencer</h6>
                    </div>
                    <div class="testimonial-card bg-white rounded-lg shadow-sm p-8" data-aos="fade-left">
                        <p class="text-gray-600 mb-6">"The analytics are a game-changer. We can finally track our ROI from influencer campaigns accurately."</p>
                        <h6 class="font-bold text-purple-700">- CEO, Tech Startup</h6>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action Section -->
        <section class="cta-section text-white text-center py-20">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4" data-aos="zoom-in">Ready to Launch Your Campaign?</h2>
                <p class="text-lg md:text-xl text-purple-200 mb-8 max-w-2xl mx-auto" data-aos="zoom-in" data-aos-delay="100">Sign up today and connect with top influencers in your niche.</p>
                <a href="#" class="btn btn-light shadow-2xl text-lg" data-aos="zoom-in" data-aos-delay="200">Sign Up Now</a>
            </div>
        </section>
    </main>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
