<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-4xl mx-auto">
        <!-- Campaign Details -->
        <div class="bg-white shadow sm:rounded-lg p-8 mb-8">
            <div class="flex justify-between items-start">
                <h1 class="text-3xl font-bold text-gray-800"><?= htmlspecialchars($c['title']) ?></h1>
                <span class="capitalize inline-block bg-purple-100 text-purple-800 text-sm font-semibold px-3 py-1 rounded-full"><?= htmlspecialchars($c['status']) ?></span>
            </div>
            <p class="mt-2 text-gray-600">Posted by: <span class="font-semibold"><?= htmlspecialchars($c['company_name']) ?></span></p>
            <div class="mt-4 text-gray-500">
                <span class="mr-4"><strong>Budget:</strong> <span class="text-green-600 font-semibold">$<?= number_format($c['budget'], 2) ?></span></span>
                <span class="mr-4"><strong>Start:</strong> <?= date('M d, Y', strtotime($c['start_date'])) ?></span>
                <span><strong>End:</strong> <?= date('M d, Y', strtotime($c['end_date'])) ?></span>
            </div>
            <div class="mt-6 border-t pt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Description</h2>
                <p class="text-gray-600 leading-relaxed"><?= nl2br(htmlspecialchars($c['description'])) ?></p>
            </div>
            <div class="mt-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-1">Target Audience</h3>
                <p class="text-gray-600"><?= htmlspecialchars($c['target_audience']) ?></p>
            </div>
        </div>

        <?php $u = auth_user(); if ($u && $u['role'] === 'influencer'): ?>
            <!-- Bid Submission Form -->
            <div class="bg-white shadow sm:rounded-lg p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Submit Your Bid</h2>
                <form method="post" action="/bids/submit" class="space-y-6">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
                    <input type="hidden" name="campaign_id" value="<?= (int)$c['id'] ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="bid_price" class="block text-sm font-medium text-gray-700">Bid Price ($)</label>
                            <input type="number" name="bid_price" id="bid_price" step="0.01" required class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="expected_reach" class="block text-sm font-medium text-gray-700">Expected Reach</label>
                            <input type="number" name="expected_reach" id="expected_reach" class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        </div>
                    </div>
                    <div>
                        <label for="proposal" class="block text-sm font-medium text-gray-700">Proposal</label>
                        <textarea name="proposal" id="proposal" rows="4" required class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"></textarea>
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="btn btn-primary w-full md:w-auto">Submit Bid</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Bids Section -->
        <div class="bg-white shadow sm:rounded-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Submitted Bids</h2>
            <?php if (empty($bids)): ?>
                <p class="text-gray-500">No bids have been submitted for this campaign yet.</p>
            <?php else: ?>
                <ul class="space-y-6">
                    <?php foreach ($bids as $b): ?>
                        <li class="p-4 border rounded-md flex justify-between items-center">
                            <div>
                                <p class="font-semibold text-gray-800"><?= htmlspecialchars($b['name']) ?></p>
                                <p class="text-sm text-gray-600">Bid: <span class="font-bold text-green-600">$<?= number_format($b['bid_price'], 2) ?></span> | Status: <span class="font-semibold capitalize"><?= htmlspecialchars($b['status']) ?></span></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php if ($u && $u['role'] === 'company' && (int)$u['id'] === (int)$c['company_id'] && $b['status'] === 'pending'): ?>
                                    <form method="post" action="/bids/approve" class="inline">
                                        <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
                                        <input type="hidden" name="bid_id" value="<?= (int)$b['id'] ?>">
                                        <button type="submit" class="btn btn-primary text-xs">Approve</button>
                                    </form>
                                    <form method="post" action="/bids/reject" class="inline">
                                        <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
                                        <input type="hidden" name="bid_id" value="<?= (int)$b['id'] ?>">
                                        <button type="submit" class="btn btn-outline-primary text-xs bg-red-500 text-white border-red-500 hover:bg-red-600">Reject</button>
                                    </form>
                                <?php endif; ?>
                                <a href="/chat?with=<?= (int)($u['role'] === 'company' ? $b['influencer_id'] : $c['company_id']) ?>&campaign_id=<?= (int)$c['id'] ?>" class="btn btn-outline-primary text-xs">Chat</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
