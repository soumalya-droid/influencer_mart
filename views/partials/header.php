<?php $cfg = require __DIR__ . '/../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($cfg['app_name']) ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- AOS (Animate on Scroll) Library -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <style>
        /* Custom Styles to match the theme */
        html {
            scroll-behavior: smooth;
        }
        body {
            font-family: 'Inter', sans-serif;
            color: #1a202c; /* --text-dark */
        }

        :root {
            --primary-color: #6b46c1;
            --secondary-color: #4299e1;
            --text-dark: #1a202c;
            --text-light: #f7fafc;
            --bg-light: #f8f9fa;
        }

        /* Hero Section Gradient and Pattern */
        .hero-section {
            background-color: #1a202c;
            position: relative;
            overflow: hidden;
            background-image: url('https://placehold.co/1920x1080/2d3748/ffffff?text=Background');
            background-size: cover;
            background-position: center;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(107, 70, 193, 0.85), rgba(66, 153, 225, 0.85));
            z-index: 1;
        }

        .hero-section .container {
            position: relative;
            z-index: 2;
        }

        /* Hero text animation */
        @keyframes slideUpFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .hero-headline {
            animation: slideUpFadeIn 0.8s ease-out forwards;
        }
        .hero-subtext {
            animation: slideUpFadeIn 0.8s ease-out 0.2s forwards;
            opacity: 0; /* Start hidden */
        }
        .hero-buttons {
            animation: slideUpFadeIn 0.8s ease-out 0.4s forwards;
            opacity: 0; /* Start hidden */
        }

        /* Feature Card Hover Effect */
        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .feature-card .feature-icon {
            transition: transform 0.3s ease;
        }
        .feature-card:hover .feature-icon {
            transform: scale(1.1);
        }

        /* Influencer Card Hover Effect */
        .influencer-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .influencer-card:hover {
            transform: translateY(-12px) scale(1.03);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        /* Campaign Card Hover Effect */
        .campaign-card {
             border-left: 4px solid var(--primary-color);
             transition: all 0.3s ease;
        }
        .campaign-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: var(--secondary-color);
        }

        /* Testimonial Card Styling */
        .testimonial-card {
            position: relative;
            overflow: hidden;
        }
        .testimonial-card::before {
            content: '“';
            position: absolute;
            top: -10px;
            left: 15px;
            font-size: 6rem;
            font-weight: 800;
            color: rgba(107, 70, 193, 0.1);
            z-index: 0;
            line-height: 1;
        }
        .testimonial-card p, .testimonial-card h6 {
            position: relative;
            z-index: 1;
        }

        /* Call to Action Section Styling */
        .cta-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        /* General Button Styles */
        .btn {
            border-radius: 9999px; /* pill shape */
            font-weight: 600;
            padding: 0.75rem 2rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn:hover {
            transform: scale(1.05);
        }
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        .btn-primary:hover {
            background-color: #553c9a; /* Darker purple */
        }
        .btn-light {
            background-color: white;
            color: var(--primary-color);
        }
        .btn-light:hover {
            background-color: #f0e6ff; /* Lighter purple tint */
        }
        .btn-outline-light {
            border: 2px solid white;
            color: white;
        }
        .btn-outline-light:hover {
            background-color: white;
            color: var(--primary-color);
        }
        .btn-outline-primary {
             border: 2px solid var(--primary-color);
             color: var(--primary-color);
             padding: 0.5rem 1.5rem;
        }
        .btn-outline-primary:hover {
             background-color: var(--primary-color);
             color: white;
        }

        /* Section Heading */
        .section-heading {
            font-size: 2.5rem;
            font-weight: 800;
        }

        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-white">

    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-lg shadow-md sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="<?= base_url() ?>" class="text-2xl font-bold text-gray-800">
                <span class="text-purple-700">In</span>fluence
            </a>
            <div class="hidden lg:flex items-center space-x-8">
                <a href="<?= base_url() ?>" class="text-gray-600 hover:text-purple-700 font-medium">Home</a>
                <a href="<?= base_url('#campaigns') ?>" class="text-gray-600 hover:text-purple-700 font-medium">Campaigns</a>
                <a href="<?= base_url('#influencers') ?>" class="text-gray-600 hover:text-purple-700 font-medium">Influencers</a>
                <a href="<?= base_url('#testimonials') ?>" class="text-gray-600 hover:text-purple-700 font-medium">Testimonials</a>
                <a href="#" class="text-gray-600 hover:text-purple-700 font-medium">Contact</a>
            </div>
            <div class="hidden lg:flex items-center space-x-4">
                <?php if (auth_user()): ?>
                    <a href="<?= base_url('dashboard') ?>" class="text-gray-600 hover:text-purple-700 font-medium">Dashboard</a>
                    <form action="<?= base_url('logout') ?>" method="post" class="inline">
                        <button type="submit" class="text-gray-600 hover:text-purple-700 font-medium">Logout</button>
                    </form>
                <?php else: ?>
                    <a href="<?= base_url('auth/login') ?>" class="text-gray-600 hover:text-purple-700 font-medium">Login</a>
                    <a href="<?= base_url('auth/register') ?>" class="btn btn-primary shadow-lg">Sign Up</a>
                <?php endif; ?>
            </div>
            <button class="lg:hidden flex items-center px-3 py-2 border rounded text-gray-500 border-gray-600 hover:text-gray-800 hover:border-gray-800">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>
    <main>
    <?php if ($e = flash('error')): ?>
      <div class="container mx-auto"><div class="alert error bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert"><?= htmlspecialchars($e) ?></div></div>
    <?php endif; ?>
    <?php if ($s = flash('success')): ?>
      <div class="container mx-auto"><div class="alert success bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert"><?= htmlspecialchars($s) ?></div></div>
    <?php endif; ?>
