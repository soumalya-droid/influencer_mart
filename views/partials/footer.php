</main>
    <!-- Footer -->
    <footer class="bg-gray-800 text-gray-300">
        <div class="container mx-auto px-6 py-16">
            <div class="grid md:grid-cols-4 gap-12">
                <!-- About -->
                <div class="col-span-1">
                    <h3 class="text-2xl font-bold text-white mb-4"><span class="text-purple-400">In</span>fluence</h3>
                    <p class="text-gray-400">The premier platform for connecting innovative brands with creative influencers.</p>
                    <div class="flex space-x-4 mt-6">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white">About Us</a></li>
                        <li><a href="#campaigns" class="hover:text-white">Campaigns</a></li>
                        <li><a href="#influencers" class="hover:text-white">Influencers</a></li>
                        <li><a href="#" class="hover:text-white">Contact</a></li>
                    </ul>
                </div>
                <!-- Support -->
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Support</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white">FAQ</a></li>
                        <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white">Terms of Service</a></li>
                    </ul>
                </div>
                <!-- Newsletter -->
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Newsletter</h4>
                    <p class="mb-4 text-gray-400">Subscribe for the latest updates.</p>
                    <form>
                        <div class="flex">
                            <input type="email" placeholder="Your Email" class="w-full px-4 py-2 rounded-l-md text-gray-800 focus:outline-none">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-r-md">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="mt-12 border-t border-gray-700 pt-8 text-center text-gray-500">
                <p>&copy; 2024 Influence. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- AOS Script -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>

    <!-- Firebase SDKs -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getAuth, signInAnonymously, signInWithCustomToken } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";
        import { getFirestore, collection, addDoc, getDocs, onSnapshot } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";

        // Global variables for Firebase
        const appId = typeof __app_id !== 'undefined' ? __app_id : 'default-app-id';
        const firebaseConfig = typeof __firebase_config !== 'undefined' ? JSON.parse(__firebase_config) : {};
        const initialAuthToken = typeof __initial_auth_token !== 'undefined' ? __initial_auth_token : null;

        // Mock data to pre-populate the database if it's empty
        const initialCampaigns = [
            { title: "Summer Sportswear Launch", company_name: "ActiveLife Co.", budget: 5000.00 },
            { title: "Eco-Friendly Kitchenware", company_name: "GreenHome Goods", budget: 2500.00 },
            { title: "New Mobile Tech Gadget", company_name: "FutureTech Inc.", budget: 10000.00 },
            { title: "Artisan Coffee Subscription", company_name: "Morning Brews", budget: 3000.00 },
            { title: "Sustainable Skincare Line", company_name: "Pure Glow", budget: 7500.00 },
            { title: "Pet Nutrition Campaign", company_name: "Happy Paws", budget: 4000.00 },
        ];

        // --- Firebase Initialization and Auth ---
        let db;
        let auth;
        let userId;

        const app = initializeApp(firebaseConfig);
        db = getFirestore(app);
        auth = getAuth(app);

        let campaignsCollectionPath = `/artifacts/${appId}/public/data/campaigns`;

        /**
         * Signs in the user using the custom auth token if available,
         * otherwise signs in anonymously.
         */
        const signIn = async () => {
            try {
                if (initialAuthToken) {
                    await signInWithCustomToken(auth, initialAuthToken);
                } else {
                    await signInAnonymously(auth);
                }
                userId = auth.currentUser.uid;
                console.log("Signed in with user ID:", userId);
                await initializeFirestore();
            } catch (error) {
                console.error("Error during authentication:", error);
            }
        };

        /**
         * Populates the database with initial campaign data if the collection is empty.
         */
        const initializeFirestore = async (). => {
            try {
                const campaignsSnapshot = await getDocs(collection(db, campaignsCollectionPath));
                if (campaignsSnapshot.empty) {
                    console.log("Populating database with initial data...");
                    for (const campaign of initialCampaigns) {
                        await addDoc(collection(db, campaignsCollectionPath), campaign);
                    }
                }
                attachRealtimeListener();
            } catch (error) {
                console.error("Error initializing Firestore:", error);
            }
        };

        /**
         * Attaches a real-time listener to the campaigns collection.
         */
        const attachRealtimeListener = () => {
            const campaignsGrid = document.getElementById('campaigns-grid');
            const loadingSpinner = document.getElementById('loading-spinner');

            onSnapshot(collection(db, campaignsCollectionPath), (querySnapshot) => {
                const campaigns = [];
                querySnapshot.forEach((doc) => {
                    campaigns.push({ id: doc.id, ...doc.data() });
                });
                renderCampaigns(campaigns);
            }, (error) => {
                console.error("Error fetching campaigns:", error);
                loadingSpinner.innerHTML = `<p class="text-red-500 font-semibold text-center">Failed to load campaigns.</p>`;
            });
        };

        /**
         * Renders the campaign cards based on the provided data.
         * @param {Array<Object>} campaigns - The list of campaign objects.
         */
        const renderCampaigns = (campaigns) => {
            const campaignsGrid = document.getElementById('campaigns-grid');
            const loadingSpinner = document.getElementById('loading-spinner');

            campaignsGrid.innerHTML = '';
            if (campaigns.length === 0) {
                 campaignsGrid.innerHTML = `<p class="col-span-full text-center text-gray-500 py-12">No campaigns found.</p>`;
            } else {
                campaigns.forEach(campaign => {
                    const campaignCardHTML = `
                        <div class="campaign-card-wrapper">
                            <a href="campaigns/show?id=${campaign.id}" class="campaign-card bg-white rounded-lg shadow-sm p-6 flex flex-col justify-between h-full block">
                                <div>
                                    <h3 class="text-xl font-bold mb-3 campaign-title">${campaign.title}</h3>
                                    <p class="text-gray-500 mb-4">By: <span class="font-semibold text-gray-700">${campaign.company_name}</span></p>
                                    <p class="text-gray-500">Budget: <span class="font-semibold text-green-600">$${campaign.budget.toFixed(2)}</span></p>
                                </div>
                                <span class="btn btn-primary mt-6 self-start">View Campaign</span>
                            </a>
                        </div>
                    `;
                    const div = document.createElement('div');
                    div.innerHTML = campaignCardHTML;
                    campaignsGrid.appendChild(div.firstChild);
                });
            }
             loadingSpinner.style.display = 'none';
        };

        // --- Event Listeners and AOS Init ---
        document.addEventListener('DOMContentLoaded', () => {
            AOS.init({
                duration: 800,
                once: true,
            });

            // Live search for campaigns
            const searchInput = document.getElementById('campaignSearch');
            searchInput.addEventListener('keyup', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                const campaignCards = document.querySelectorAll('.campaign-card-wrapper');

                campaignsGrid.innerHTML = '';
                let foundCampaigns = 0;

                campaigns.forEach(campaign => {
                    if (campaign.title.toLowerCase().includes(searchTerm)) {
                         const campaignCardHTML = `
                            <div class="campaign-card-wrapper">
                                <a href="campaigns/show?id=${campaign.id}" class="campaign-card bg-white rounded-lg shadow-sm p-6 flex flex-col justify-between h-full block">
                                    <div>
                                        <h3 class="text-xl font-bold mb-3 campaign-title">${campaign.title}</h3>
                                        <p class="text-gray-500 mb-4">By: <span class="font-semibold text-gray-700">${campaign.company_name}</span></p>
                                        <p class="text-gray-500">Budget: <span class="font-semibold text-green-600">$${campaign.budget.toFixed(2)}</span></p>
                                    </div>
                                    <span class="btn btn-primary mt-6 self-start">View Campaign</span>
                                </a>
                            </div>
                        `;
                        const div = document.createElement('div');
                        div.innerHTML = campaignCardHTML;
                        campaignsGrid.appendChild(div.firstChild);
                        foundCampaigns++;
                    }
                });

                if (foundCampaigns === 0) {
                     campaignsGrid.innerHTML = `<p class="col-span-full text-center text-gray-500 py-12">No campaigns found matching "${searchTerm}".</p>`;
                }
            });

            signIn();
        });
    </script>
</body>
</html>
