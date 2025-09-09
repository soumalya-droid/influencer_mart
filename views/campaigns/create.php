<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Create a New Campaign</h1>

        <div class="bg-white p-8 shadow sm:rounded-lg">
            <form class="space-y-6" action="/campaigns/create" method="POST">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Campaign Title</label>
                    <div class="mt-1">
                        <input id="title" name="title" type="text" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <div class="mt-1">
                        <textarea id="description" name="description" rows="4" required
                                  class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"></textarea>
                    </div>
                </div>

                <div>
                    <label for="target_audience" class="block text-sm font-medium text-gray-700">Target Audience</label>
                    <div class="mt-1">
                        <input id="target_audience" name="target_audience" type="text" placeholder="e.g., 18-30, US, tech-savvy"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="budget" class="block text-sm font-medium text-gray-700">Budget ($)</label>
                        <div class="mt-1">
                            <input id="budget" name="budget" type="number" step="0.01" required
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        </div>
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <div class="mt-1">
                            <input id="start_date" name="start_date" type="date" required
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        </div>
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <div class="mt-1">
                            <input id="end_date" name="end_date" type="date" required
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Create Campaign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
