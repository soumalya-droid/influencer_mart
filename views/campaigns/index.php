<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Browse Campaigns</h1>
        <?php if (auth_user() && auth_user()['role'] === 'company'): ?>
            <a href="<?= base_url('campaigns/create') ?>" class="btn btn-primary">Create Campaign</a>
        <?php endif; ?>
    </div>

    <!-- Search Form -->
    <div class="mb-8">
        <form method="get" class="flex">
            <input name="q" placeholder="Search campaigns by title..."
                   value="<?= htmlspecialchars($q ?? '') ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-purple-500 focus:border-purple-500">
            <button type="submit" class="btn btn-primary rounded-l-none">Search</button>
        </form>
    </div>

    <!-- Campaigns Grid -->
    <?php if (empty($campaigns)): ?>
        <div class="text-center py-16">
            <p class="text-gray-500 text-lg">No campaigns found.</p>
        </div>
    <?php else: ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($campaigns as $c): ?>
                <div class="campaign-card bg-white rounded-lg shadow-sm p-6 flex flex-col justify-between h-full">
                    <div>
                        <h3 class="text-xl font-bold mb-3 campaign-title"><?= htmlspecialchars($c['title']) ?></h3>
                        <p class="text-gray-500 mb-4">By: <span class="font-semibold text-gray-700"><?= htmlspecialchars($c['company_name']) ?></span></p>
                        <p class="text-gray-500">Budget: <span class="font-semibold text-green-600">$<?= number_format($c['budget'], 2) ?></span></p>
                    </div>
                    <a href="<?= base_url('campaigns/show?id=' . (int)$c['id']) ?>" class="btn btn-primary mt-6 self-start">View Campaign</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
