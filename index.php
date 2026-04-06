<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SmarboPlusEvent - Professional platform for managing social events, tracking contributions, handling invitations, RSVPs and attendance with full transparency.">
    <title>SmarboPlusEvent Event Management Platform</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /***************************************************************
         * CUSTOM PROPERTIES
         ***************************************************************/
        :root {
            --primary: #E6521F;
            --primary-dark: #c44118;
            --primary-light: rgba(230, 82, 31, 0.1);
            --primary-glow: rgba(230, 82, 31, 0.15);
            --dark: #0a0a14;
            --dark-light: #151522;
            --gray-900: #1a1a2e;
            --gray-800: #2d2d44;
            --gray-700: #444461;
            --gray-600: #666687;
            --gray-500: #8f8fb2;
            --gray-400: #b8b8d9;
            --gray-300: #e0e0f0;
            --gray-200: #f0f0fa;
            --gray-100: #f8f8ff;
            --white: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 8px 32px rgba(230, 82, 31, 0.15);
            --shadow-lg: 0 16px 48px rgba(230, 82, 31, 0.18);
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            --radius-md: 20px;
            --radius-lg: 32px;
            --radius-xl: 48px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark-light) 100%);
            color: var(--white);
            line-height: 1.6;
            font-size: 16px;
            font-weight: 400;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
        }

        /***************************************************************
         * UTILITY CLASSES
         ***************************************************************/
        .container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .container-narrow {
            max-width: 1080px;
        }

        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
        }

        .glass-heavy {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .text-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, #ff8a5c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .text-center { text-align: center; }
        .mb-1 { margin-bottom: 1rem; }
        .mb-2 { margin-bottom: 2rem; }
        .mb-3 { margin-bottom: 3rem; }
        .mb-4 { margin-bottom: 4rem; }
        .mt-4 { margin-top: 4rem; }

        /***************************************************************
         * TYPOGRAPHY
         ***************************************************************/
        h1, h2, h3, h4, h5, h6 {
            font-weight: 800;
            line-height: 1.1;
        }

        h1 { font-size: 5rem; margin-bottom: 1.5rem; }
        h2 { font-size: 3.5rem; margin-bottom: 2rem; }
        h3 { font-size: 2.2rem; margin-bottom: 1.2rem; }
        h4 { font-size: 1.5rem; margin-bottom: 1rem; }

        p {
            color: var(--gray-400);
            font-size: 1.1rem;
            line-height: 1.7;
        }

        .section-title {
            font-size: 3.8rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .section-subtitle {
            font-size: 1.3rem;
            color: var(--gray-400);
            text-align: center;
            max-width: 700px;
            margin: 0 auto 4rem;
        }

        /***************************************************************
         * BUTTONS
         ***************************************************************/
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 2.2rem;
            border-radius: 50px;
            font-size: 1.05rem;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            cursor: pointer;
            white-space: nowrap;
            gap: 10px;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
            z-index: -1;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
            box-shadow: 0 4px 20px rgba(230, 82, 31, 0.3);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-secondary:hover {
            background: var(--primary);
            color: white;
        }

        .btn-lg {
            padding: 1.3rem 3rem;
            font-size: 1.2rem;
        }

        /***************************************************************
         * HEADER / NAVIGATION
         ***************************************************************/
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            padding: 1.5rem 0;
            transition: var(--transition);
        }

        header.scrolled {
            background: rgba(10, 10, 20, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 1rem 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            z-index: 1001;
        }

        .logo-img {
            height: 50px;
            width: auto;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 900;
            color: white;
        }

        .logo-text span {
            color: var(--primary);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2.5rem;
        }

        .nav-links a {
            color: var(--gray-300);
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            transition: var(--transition);
            position: relative;
        }

        .nav-links a:hover {
            color: white;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: var(--transition);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.8rem;
            cursor: pointer;
            z-index: 1001;
        }

        /***************************************************************
         * HERO SECTION
         ***************************************************************/
        .hero {
            padding: 200px 0 100px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 80%;
            height: 100%;
            background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%);
            z-index: -1;
        }

        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(230, 82, 31, 0.15);
            color: var(--primary);
            padding: 0.6rem 1.2rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .hero-title {
            font-size: 4.5rem;
            line-height: 1.1;
            margin-bottom: 1.5rem;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: var(--gray-400);
            margin-bottom: 2.5rem;
        }

        .hero-actions {
            display: flex;
            gap: 1.2rem;
            flex-wrap: wrap;
        }

        /* Hero Carousel */
        .hero-carousel {
            position: relative;
            perspective: 1000px;
        }

        .carousel-container {
            position: relative;
            width: 100%;
            height: 500px;
            transform-style: preserve-3d;
            transform: rotateY(-10deg);
            transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--dark-light);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }

        .carousel-slide {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: var(--radius-lg);
            overflow: hidden;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-lg);
            background: var(--dark-light);
        }

        .carousel-slide.active {
            opacity: 1;
            transform: translateX(0) translateZ(0);
            z-index: 3;
        }

        .carousel-slide.prev {
            opacity: 0.5;
            transform: translateX(-50%) translateZ(-100px) scale(0.9);
            z-index: 2;
        }

        .carousel-slide.next {
            opacity: 0.5;
            transform: translateX(50%) translateZ(-100px) scale(0.9);
            z-index: 2;
        }

        .slide-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            padding: 20px;
            background: var(--dark-light);
        }

        .slide-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 2rem;
            background: linear-gradient(transparent, rgba(10, 10, 20, 0.95));
            color: white;
            z-index: 2;
        }

        .carousel-controls {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .carousel-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--gray-600);
            border: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .carousel-dot.active {
            background: var(--primary);
            transform: scale(1.2);
        }

        /***************************************************************
         * EVENT TYPES SECTION
         ***************************************************************/
        .event-types-section {
            padding: 6rem 0;
            position: relative;
        }

        .event-types-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            perspective: 1000px;
        }

        .event-type-card {
            padding: 3rem 2rem;
            border-radius: var(--radius-lg);
            text-align: center;
            transition: var(--transition);
            transform-style: preserve-3d;
            transform: rotateY(0deg);
            position: relative;
            overflow: hidden;
        }

        .event-type-card:nth-child(1) {
            background: linear-gradient(135deg, var(--primary) 0%, #ff8a5c 100%);
        }

        .event-type-card:nth-child(2) {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .event-type-card:nth-child(3) {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .event-type-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: rgba(255,255,255,0.3);
        }

        .event-type-icon {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            color: white;
        }

        /***************************************************************
         * 3D CAROUSEL FOR BENEFITS SECTION - BANNERS ONLY VERSION
         ***************************************************************/
        .benefits-section {
            padding: 6rem 0;
            background: rgba(0,0,0,0.2);
        }

        .carousel-3d-container {
            position: relative;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            perspective: 1200px;
            padding: 2rem 0 4rem;
        }

        .carousel-3d {
            position: relative;
            width: 100%;
            height: 500px;
            transform-style: preserve-3d;
            transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .carousel-card {
            position: absolute;
            width: 320px;
            height: 450px;
            left: 50%;
            top: 50%;
            margin-left: -160px;
            margin-top: -225px;
            border-radius: var(--radius-lg);
            overflow: hidden;
            cursor: pointer;
            transform-style: preserve-3d;
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            background: var(--dark-light);
        }

        .carousel-card .card-front,
        .carousel-card .card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: var(--radius-lg);
            overflow: hidden;
        }

        .carousel-card .card-front {
            background: var(--dark-light);
            transform: rotateY(0deg);
        }

        .carousel-card .card-back {
            background: var(--dark-light);
            transform: rotateY(180deg);
        }

        /* Front side - banner fills entire card */
        .carousel-card .card-image {
            width: 100%;
            height: 100%;
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            background-color: var(--dark-light);
        }

        /* Back side - same banner */
        .carousel-card .card-back-image {
            width: 100%;
            height: 100%;
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
            background-color: var(--dark-light);
        }

        /* Remove all text content styling */
        .carousel-card .card-content,
        .carousel-card .card-icon,
        .carousel-card .card-back-content,
        .carousel-card h3,
        .carousel-card p,
        .carousel-card .card-highlight {
            display: none !important;
        }

        /* Hover Effects */
        .carousel-card:hover {
            transform: translateZ(50px) scale(1.05);
            box-shadow: 0 35px 70px rgba(230, 82, 31, 0.2);
        }

        .carousel-card.flipped {
            transform: rotateY(180deg) translateZ(50px);
        }

        .carousel-card.flipped:hover {
            transform: rotateY(180deg) translateZ(60px) scale(1.05);
        }

        /* Carousel Controls */
        .carousel-controls-3d {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            margin-top: 3rem;
        }

        .carousel-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .carousel-btn:hover {
            background: var(--primary);
            transform: scale(1.1);
            box-shadow: 0 10px 20px rgba(230, 82, 31, 0.3);
        }

        .carousel-dots {
            display: flex;
            gap: 0.8rem;
        }

        .carousel-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--gray-600);
            border: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .carousel-dot.active {
            background: var(--primary);
            transform: scale(1.3);
        }

        /***************************************************************
         * ROLES SECTION
         ***************************************************************/
        .roles-section {
            padding: 6rem 0;
            background: linear-gradient(135deg, var(--gray-900) 0%, var(--dark) 100%);
        }

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 3rem;
        }

        .role-card {
            padding: 3rem;
            border-radius: var(--radius-lg);
            position: relative;
            overflow: hidden;
        }

        .role-card:nth-child(1) {
            background: linear-gradient(135deg, rgba(230, 82, 31, 0.1) 0%, rgba(230, 82, 31, 0.05) 100%);
            border: 1px solid rgba(230, 82, 31, 0.2);
        }

        .role-card:nth-child(2) {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(102, 126, 234, 0.05) 100%);
            border: 1px solid rgba(102, 126, 234, 0.2);
        }

        .role-badge {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            background: var(--primary);
            color: white;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .role-card:nth-child(2) .role-badge {
            background: #667eea;
        }

        /***************************************************************
         * FAQ SECTION
         ***************************************************************/
        .faq-section {
            padding: 6rem 0;
        }

        .faq-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .faq-item {
            margin-bottom: 1.5rem;
            border-radius: var(--radius-md);
            overflow: hidden;
        }

        .faq-question {
            width: 100%;
            padding: 1.5rem 2rem;
            text-align: left;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: var(--transition);
        }

        .faq-question:hover {
            background: rgba(255,255,255,0.05);
        }

        .faq-question.active {
            background: var(--primary-light);
            border-color: var(--primary);
        }

        .faq-answer {
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
            background: rgba(0,0,0,0.2);
            border-left: 1px solid var(--glass-border);
            border-right: 1px solid var(--glass-border);
            border-bottom: 1px solid var(--glass-border);
            border-radius: 0 0 var(--radius-md) var(--radius-md);
        }

        .faq-answer.active {
            padding: 2rem;
            max-height: 500px;
        }

        /***************************************************************
         * TESTIMONIALS SECTION
         ***************************************************************/
        .testimonials-section {
            padding: 6rem 0;
            background: linear-gradient(135deg, var(--dark) 0%, var(--gray-900) 100%);
            position: relative;
            overflow: hidden;
        }

        .testimonials-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
        }

        .testimonials-slider {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .testimonials-track {
            display: flex;
            gap: 2rem;
            transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 1rem 0;
        }

        .testimonial-card {
            flex: 0 0 calc(33.333% - 1.333rem);
            padding: 2.5rem;
            border-radius: var(--radius-lg);
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            min-height: 320px;
            display: flex;
            flex-direction: column;
        }

        .testimonial-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), #ff8a5c);
        }

        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        .testimonial-rating {
            display: flex;
            gap: 5px;
            margin-bottom: 1.5rem;
            color: #ffc107;
            font-size: 1.1rem;
        }

        .testimonial-text {
            font-size: 1.1rem;
            line-height: 1.7;
            color: var(--gray-300);
            margin-bottom: 2rem;
            flex-grow: 1;
            font-style: italic;
            position: relative;
            padding-left: 1.5rem;
        }

        .testimonial-text::before {
            content: '"';
            position: absolute;
            left: 0;
            top: -10px;
            font-size: 4rem;
            color: var(--primary);
            opacity: 0.3;
            font-family: serif;
            line-height: 1;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: auto;
        }

        .author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, #ff8a5c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.3rem;
            color: white;
            flex-shrink: 0;
        }

        .author-info h4 {
            font-size: 1.2rem;
            margin-bottom: 0.3rem;
            color: white;
        }

        .author-info p {
            font-size: 0.9rem;
            color: var(--gray-400);
            margin: 0;
        }

        .testimonial-controls {
            display: flex;
            justify-content: center;
            gap: 0.8rem;
            margin-top: 3rem;
        }

        .testimonial-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--gray-600);
            border: none;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }

        .testimonial-dot.active {
            background: var(--primary);
            transform: scale(1.3);
        }

        .testimonial-dot::after {
            content: '';
            position: absolute;
            top: -8px;
            left: -8px;
            right: -8px;
            bottom: -8px;
            border-radius: 50%;
            border: 2px solid transparent;
            transition: var(--transition);
        }

        .testimonial-dot.active::after {
            border-color: var(--primary);
            opacity: 0.3;
        }

        /* Testimonials Slider Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .testimonial-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .testimonial-card:nth-child(1) { animation-delay: 0.1s; }
        .testimonial-card:nth-child(2) { animation-delay: 0.2s; }
        .testimonial-card:nth-child(3) { animation-delay: 0.3s; }
        .testimonial-card:nth-child(4) { animation-delay: 0.4s; }

        /***************************************************************
         * FOOTER
         ***************************************************************/
        .footer-links {
            padding: 6rem 0 4rem;
            border-top: 1px solid var(--glass-border);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 3rem;
        }

        .footer-column h4 {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            color: white;
            font-weight: 600;
        }

        .footer-links-list {
            list-style: none;
        }

        .footer-links-list li {
            margin-bottom: 0.8rem;
        }

        .footer-links-list a {
            color: var(--gray-400);
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .footer-links-list a:hover {
            color: var(--primary);
            padding-left: 5px;
        }

        .footer-bottom {
            padding: 2rem 0;
            border-top: 1px solid var(--glass-border);
            text-align: center;
            color: var(--gray-500);
            font-size: 0.9rem;
        }

        /***************************************************************
         * RESPONSIVE DESIGN
         ***************************************************************/
        @media (max-width: 1200px) {
            h1 { font-size: 4rem; }
            .hero-title { font-size: 3.5rem; }
            .footer-grid { grid-template-columns: repeat(3, 1fr); }
            .testimonial-card {
                flex: 0 0 calc(50% - 1rem);
                min-height: 300px;
            }
            
            .testimonials-track {
                gap: 1.5rem;
            }
            
            /* Adjust 3D carousel for banners */
            .carousel-3d-container {
                perspective: 1100px;
            }
            .carousel-3d {
                height: 450px;
            }
            .carousel-card {
                width: 280px;
                height: 400px;
                margin-left: -140px;
            }
        }

        @media (max-width: 992px) {
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 2rem;
            }
            .hero-actions {
                justify-content: center;
            }
            .event-types-cards { grid-template-columns: repeat(2, 1fr); }
            .nav-links {
                position: fixed;
                top: 0;
                right: -100%;
                width: 300px;
                height: 100vh;
                background: rgba(10, 10, 20, 0.95);
                backdrop-filter: blur(20px);
                flex-direction: column;
                justify-content: center;
                gap: 2rem;
                padding: 2rem;
                transition: var(--transition);
                z-index: 1000;
            }
            .nav-links.active {
                right: 0;
            }
            .mobile-menu-btn { display: block; }
            
            .carousel-container {
                height: 400px;
            }

            /* Adjust 3D carousel */
            .carousel-3d-container {
                perspective: 1000px;
            }
            .carousel-3d {
                height: 400px;
            }
            .carousel-card {
                width: 240px;
                height: 350px;
                margin-left: -120px;
            }
        }

        @media (max-width: 768px) {
            h1 { font-size: 3rem; }
            h2 { font-size: 2.5rem; }
            .section-title { font-size: 2.8rem; }
            .event-types-cards {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            .footer-grid { grid-template-columns: repeat(2, 1fr); }
            
            /* 3D Carousel Responsive */
            .carousel-3d-container {
                perspective: 800px;
                padding: 0 0 3rem;
            }
            .carousel-3d {
                height: 350px;
            }
            .carousel-card {
                width: 220px;
                height: 320px;
                margin-left: -110px;
            }
            .carousel-controls-3d {
                margin-top: 1.5rem;
            }
            
            /* Testimonials Responsive */
            .testimonial-card {
                flex: 0 0 calc(100% - 1rem);
                min-height: 280px;
                padding: 2rem;
            }
            
            .testimonials-slider {
                padding: 0 10px;
            }
            
            .testimonials-track {
                gap: 1.5rem;
                padding: 0.5rem 0;
            }
            
            .testimonial-text {
                font-size: 1rem;
                padding-left: 1rem;
            }
            
            .testimonial-text::before {
                font-size: 3rem;
                top: -5px;
            }
            
            .author-avatar {
                width: 50px;
                height: 50px;
                font-size: 1.1rem;
            }
            
            .author-info h4 {
                font-size: 1.1rem;
            }
            
            /* Hero banner adjustments */
            .carousel-container {
                height: 350px;
            }
            
            .slide-image {
                padding: 15px;
            }
        }

        @media (max-width: 576px) {
            body {
                font-size: 15px;
            }
            .container { padding: 0 16px; }
            .hero-actions { flex-direction: column; }
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            .roles-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            .role-card {
                padding: 2rem;
            }
            
            .carousel-controls-3d {
                gap: 1rem;
            }
            
            .carousel-btn {
                width: 50px;
                height: 50px;
            }
            
            /* Testimonials small screens */
            .testimonials-section {
                padding: 4rem 0;
            }
            
            .testimonial-card {
                padding: 1.5rem;
                min-height: 260px;
            }
            
            .testimonial-rating {
                margin-bottom: 1rem;
                font-size: 1rem;
            }
            
            .testimonial-text {
                margin-bottom: 1.5rem;
            }
            
            .testimonial-author {
                gap: 0.8rem;
            }
            
            .testimonial-controls {
                margin-top: 2rem;
            }
            
            /* Hero carousel mobile */
            .hero {
                padding: 150px 0 80px;
            }
            
            .carousel-container {
                height: 300px;
            }
            
            /* 3D carousel mobile */
            .carousel-3d-container {
                perspective: 700px;
            }
            .carousel-3d {
                height: 320px;
            }
            .carousel-card {
                width: 190px;
                height: 270px;
                margin-left: -95px;
            }
        }

        @media (max-width: 480px) {
             /* 3D carousel mobile */
            .carousel-3d-container {
                perspective: 600px;
            }
            .carousel-3d {
                height: 280px;
            }
            .carousel-card {
                width: 160px;
                height: 230px;
                margin-left: -80px;
            }
        }

        @media (max-width: 414px) {
            h1 {
                font-size: 2.8rem;
            }
            .hero-title {
                font-size: 2.5rem;
            }
            .section-title {
                font-size: 2.2rem;
            }
        }

        @media (max-width: 400px) {
            /* For very small screens */
            .testimonial-card {
                min-height: 280px;
            }
            
            .testimonial-author {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }

        @media (max-width: 320px) {
            h1 {
                font-size: 2.5rem;
            }
            .hero-title {
                font-size: 2.2rem;
            }
            .section-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header id="main-header">
        <div class="container">
            <nav>
                <a href="/" class="logo">
                    <img src="./images/INVTS.png" alt="SmarboPlusEvent" class="logo-img">
                </a>

                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="nav-links" id="navLinks">
                    <a href="#benefits">Benefits</a>
                    <a href="#events">Events</a>
                    <a href="#roles">For Whom</a>
                    <a href="#faq">FAQ</a>
                    <a href="login.php" class="btn-secondary">Login</a>
                    <a href="register.php" class="btn-primary">Get Started</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section with Carousel -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div>
                    <div class="hero-badge">
                        <i class="fas fa-check-circle"></i>
                        Complete Transparency for Social Events
                    </div>
                    <h1 class="hero-title text-gradient">
                        Track Contributions &<br>Manage Guests With<br>Full Visibility
                    </h1>
                    <p class="hero-subtitle">
                        Simplify weddings, send-offs, kitchen parties, and social gatherings. See exactly who's coming, 
                        track contributions in real-time, and manage everything from one clear dashboard.
                    </p>
                    <div class="hero-actions">
                        <a href="register.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-calendar-plus"></i> Start Managing Events
                        </a>
                        <a href="#benefits" class="btn btn-secondary btn-lg">
                            <i class="fas fa-eye"></i> See Benefits
                        </a>
                    </div>
                </div>

                <div class="hero-carousel">
                    <div class="carousel-container" id="carouselContainer">
                        <div class="carousel-slide active">
                            <img src="./images/banner1.png" class="slide-image" alt="Event Overview">

                        </div>
                        <div class="carousel-slide">
                            <img src="./images/banner2.png" class="slide-image" alt="Dashboard">

                        </div>
                        <div class="carousel-slide">
                            <img src="./images/banner3.jpg" class="slide-image" alt="Reports">

                        </div>
                        <div class="carousel-slide">
                            <img src="./images/banner4.jpg" class="slide-image" alt="Reports">

                        </div>
                        <div class="carousel-slide">
                            <img src="./images/banner5.jpg" class="slide-image" alt="Reports">

                        </div>
                        <div class="carousel-slide">
                            <img src="./images/banner6.jpg" class="slide-image" alt="Reports">

                        </div>
                        <div class="carousel-slide">
                            <img src="./images/banner7.jpg" class="slide-image" alt="Reports">

                        </div>
                        <div class="carousel-slide">
                            <img src="./images/banner8.jpg" class="slide-image" alt="Reports">

                        </div>
                    </div>
                    <div class="carousel-controls">
                        <button class="carousel-dot active" data-slide="0"></button>
                        <button class="carousel-dot" data-slide="1"></button>
                        <button class="carousel-dot" data-slide="2"></button>
                         <button class="carousel-dot" data-slide="3"></button>
                          <button class="carousel-dot" data-slide="4"></button>
                           <button class="carousel-dot" data-slide="5"></button>
                            <button class="carousel-dot" data-slide="6"></button>
                             <button class="carousel-dot" data-slide="7"></button>
                              <button class="carousel-dot" data-slide="8"></button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Event Types Section -->
    <section id="events" class="event-types-section">
        <div class="container">
            <h2 class="section-title">Perfect for Your Social Events</h2>
            <p class="section-subtitle">Designed specifically for the events that matter most in our communities</p>
            
            <div class="event-types-cards">
                <div class="event-type-card glass">
                    <div class="event-type-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Weddings</h3>
                    <p>Track contributions, manage guest lists, and monitor attendance for the big day with complete transparency.</p>
                </div>
                <div class="event-type-card glass">
                    <div class="event-type-icon">
                        <i class="fas fa-plane-departure"></i>
                    </div>
                    <h3>Send-Offs</h3>
                    <p>Coordinate farewell events with contribution tracking and clear guest management.</p>
                </div>
                <div class="event-type-card glass">
                    <div class="event-type-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h3>Kitchen Parties</h3>
                    <p>Organize pre-wedding celebrations with full financial visibility and guest tracking.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section with 3D Carousel - BANNERS ONLY -->
    <section id="benefits" class="benefits-section">
        <div class="container">
            <h2 class="section-title">What Makes SmarboPlusEvent Different</h2>
            <p class="section-subtitle">Focused on what matters for your social events. simplicity and clarity.</p>
            
            <div class="carousel-3d-container">
                <div class="carousel-3d" id="benefitsCarousel">
                    <!-- Card 1 -->
                    <div class="carousel-card">
                        <div class="card-front">
                            <div class="card-image" style="background-image: url('./images/banner3.jpg');"></div>
                        </div>
                        <div class="card-back">
                            <div class="card-back-image" style="background-image: url('./images/banner3.jpg');"></div>
                        </div>
                    </div>
                    
                    <!-- Card 2 -->
                    <div class="carousel-card">
                        <div class="card-front">
                            <div class="card-image" style="background-image: url('./images/banner4.jpg');"></div>
                        </div>
                        <div class="card-back">
                            <div class="card-back-image" style="background-image: url('./images/banner4.jpg');"></div>
                        </div>
                    </div>
                    
                    <!-- Card 3 -->
                    <div class="carousel-card">
                        <div class="card-front">
                            <div class="card-image" style="background-image: url('./images/banner5.jpg');"></div>
                        </div>
                        <div class="card-back">
                            <div class="card-back-image" style="background-image: url('./images/banner5.jpg');"></div>
                        </div>
                    </div>
                    
                    <!-- Card 4 -->
                    <div class="carousel-card">
                        <div class="card-front">
                            <div class="card-image" style="background-image: url('./images/banner6.jpg');"></div>
                        </div>
                        <div class="card-back">
                            <div class="card-back-image" style="background-image: url('./images/banner6.jpg');"></div>
                        </div>
                    </div>
                    
                    <!-- Card 5 -->
                    <div class="carousel-card">
                        <div class="card-front">
                            <div class="card-image" style="background-image: url('./images/banner7.jpg');"></div>
                        </div>
                        <div class="card-back">
                            <div class="card-back-image" style="background-image: url('./images/banner7.jpg');"></div>
                        </div>
                    </div>
                    
                    <!-- Card 6 - Use banner1 or banner2 if you want variety -->
                    <div class="carousel-card">
                        <div class="card-front">
                            <div class="card-image" style="background-image: url('./images/banner8.jpg');"></div>
                        </div>
                        <div class="card-back">
                            <div class="card-back-image" style="background-image: url('./images/banner8.jpg');"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation Controls -->
                <div class="carousel-controls-3d">
                    <button class="carousel-btn prev-btn" id="prevBtn">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="carousel-dots" id="carouselDots">
                        <!-- Dots will be generated by JavaScript -->
                    </div>
                    <button class="carousel-btn next-btn" id="nextBtn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="faq-section">
        <div class="container container-narrow">
            <h2 class="section-title">Common Questions</h2>
            <p class="section-subtitle">Simple answers about how SmarboPlusEvent helps with your social events</p>
            
            <div class="faq-container">
                <div class="faq-item">
                    <button class="faq-question">
                        What kind of events does SmarboPlusEvent handle?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>SmarboPlusEvent is designed specifically for social events like weddings, send-offs, kitchen parties, and other community gatherings. It focuses on tracking contributions, managing guest lists, and monitoring attendance for these types of events.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        Can event owners see other people's events?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>No. Event owners only see their own event details. This keeps everything private and focused. Administrators have a different view where they can see all events to help coordinate everything smoothly.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        How does contribution tracking work?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>You can see promised amounts, track which payments have been received, and automatically calculate what's still outstanding. Everything updates in real time so you always know the current status.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        Is this complicated to use?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Not at all. We designed SmarboPlusEvent to be simple and straightforward. The dashboards show you exactly what you need to know without any complexity. If you can use a basic website, you can use SmarboPlusEvent.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

   
    
<!-- Testimonials Section in Kiswahili -->
<section id="testimonials" class="testimonials-section">
    <div class="container">
        <h2 class="section-title">Wanasema Nini Watumiaji Wetu</h2>
        <p class="section-subtitle">Jiunge na maelfu ya wapangaji hafla ambao wanamwamini SmarboPlusEvent</p>
        
        <div class="testimonials-slider">
            <div class="testimonials-track" id="testimonialsTrack">
                <!-- Testimonial 1 -->
                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        "SmarboPlusEvent ilibadilisha kabisa namna tunavyoshughulikia michango ya harusi yetu. Uwazi ulitupa utulivu wa roho na kufanya ufuatiliaji uwe rahisi zaidi!"
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">JM</div>
                        <div class="author-info">
                            <h4>John & Maria M.</h4>
                            <p>Wapangaji Harusi</p>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 2 -->
                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        "Kusimamia hafla yetu ya kumuaga mgeni ilikuwa rahisi kabisa na SmarboPlusEvent. Ufuatiliaji wa wakati halisi na upatikanaji wa simu ulifanya kila kitu kiwe rahisi!"
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">AK</div>
                        <div class="author-info">
                            <h4>Anna K.</h4>
                            <p>Mratibu wa Hafla</p>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 3 -->
                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        "Vipengele vya uwazi vimebadilisha mchezo kabisa! Hakuna mazungumzo yasiyo rahisi tena kuhusu michango. Kila kitu kinaonekana wazi na kueleweka."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">MS</div>
                        <div class="author-info">
                            <h4>Michael S.</h4>
                            <p>Kiongozi wa Jumuiya</p>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 4 -->
                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        "Kamili kwa karamu yetu ya jikoni! Rahisi kutumia, inafaa kwenye simu, na sasisho za wakati halisi ziliweka kila mtu akiwa na habari. Inapendekezwa sana!"
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">TD</div>
                        <div class="author-info">
                            <h4>Tatu D.</h4>
                            <p>Mwenyeji wa Hafla</p>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 5 - Additional Swahili testimonial -->
                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        "Kwa ajili ya harusi zetu za kitamaduni, SmarboPlusEvent ilituokoa wakati mwingi. Uwezo wa kufuatilia michango na wageni kwa wakati mmoja ulitufanya tuwe na udhibiti bora."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">JM</div>
                        <div class="author-info">
                            <h4>Juma & Mwanaisha</h4>
                            <p>Wapangaji Harusi za Kitamaduni</p>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 6 - Additional Swahili testimonial -->
                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        "Kama mratibu wa hafla nyingi, SmarboPlusEvent imenifanya niweze kushughulikia matukio mengi kwa urahisi. Dashibodi rahisi na ripoti zilizowazi zinasaidia sana."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">FK</div>
                        <div class="author-info">
                            <h4>Fatma K.</h4>
                            <p>Mratibu wa Hafla za Kijamii</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-controls">
                <button class="testimonial-dot active" data-slide="0"></button>
                <button class="testimonial-dot" data-slide="1"></button>
                <button class="testimonial-dot" data-slide="2"></button>
                <button class="testimonial-dot" data-slide="3"></button>
                <button class="testimonial-dot" data-slide="4"></button>
                <button class="testimonial-dot" data-slide="5"></button>
            </div>
        </div>
    </div>
</section>

    <!-- Footer Links -->
    <section class="footer-links">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h4>What We Help With</h4>
                    <ul class="footer-links-list">
                        <li><a href="#events">Weddings</a></li>
                        <li><a href="#events">Send-Offs</a></li>
                        <li><a href="#events">Kitchen Parties</a></li>
                        <li><a href="#events">Social Gatherings</a></li>
                        <li><a href="#benefits">Contribution Tracking</a></li>
                        <li><a href="#benefits">Guest Management</a></li>
                        <li><a href="#benefits">Attendance Tracking</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>For Different Users</h4>
                    <ul class="footer-links-list">
                        <li><a href="#roles">Event Owners</a></li>
                        <li><a href="#roles">Administrators</a></li>
                        <li><a href="register.php">Get Started Free</a></li>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="#faq">Common Questions</a></li>
                        <li><a href="#benefits">Key Benefits</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>About SmarboPlusEvent</h4>
                    <ul class="footer-links-list">
                        <li><a href="#">Our Story</a></li>
                        <li><a href="#">How We Help</a></li>
                        <li><a href="#">Community Focus</a></li>
                        <li><a href="#">Privacy Commitment</a></li>
                        <li><a href="#">Contact Support</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Resources</h4>
                    <ul class="footer-links-list">
                        <li><a href="#">Getting Started Guide</a></li>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Tips for Event Planners</a></li>
                        <li><a href="#">Community Stories</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Bottom -->
    <footer class="footer-bottom">
        <div class="container">
            <p>© 2026 SmarboPlusEvent. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const navLinks = document.getElementById('navLinks');
        
        mobileMenuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });

        // Header Scroll Effect
        window.addEventListener('scroll', () => {
            const header = document.getElementById('main-header');
            header.classList.toggle('scrolled', window.scrollY > 50);
        });

        // Hero Carousel Functionality
        const slides = document.querySelectorAll('.carousel-slide');
        const dots = document.querySelectorAll('.carousel-dot');
        let currentSlide = 0;

        function showSlide(n) {
            slides.forEach(slide => slide.classList.remove('active', 'prev', 'next'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            currentSlide = (n + slides.length) % slides.length;
            
            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
            
            let prevSlide = (currentSlide - 1 + slides.length) % slides.length;
            let nextSlide = (currentSlide + 1) % slides.length;
            
            slides[prevSlide].classList.add('prev');
            slides[nextSlide].classList.add('next');
        }

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                showSlide(index);
            });
        });

        // Auto rotate hero carousel
        setInterval(() => {
            showSlide(currentSlide + 1);
        }, 5000);

        // FAQ Accordion
        const faqQuestions = document.querySelectorAll('.faq-question');
        faqQuestions.forEach(question => {
            question.addEventListener('click', () => {
                const answer = question.nextElementSibling;
                const isActive = answer.classList.contains('active');
                
                // Close all answers
                document.querySelectorAll('.faq-answer').forEach(ans => {
                    ans.classList.remove('active');
                    ans.previousElementSibling.classList.remove('active');
                });
                
                // Open clicked answer if it was closed
                if (!isActive) {
                    answer.classList.add('active');
                    question.classList.add('active');
                }
            });
        });

        // 3D Carousel for Benefits Section
        function init3DCarousel() {
            const carousel = document.getElementById('benefitsCarousel');
            const cards = document.querySelectorAll('.carousel-card');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const dotsContainer = document.getElementById('carouselDots');
            
            let currentIndex = 0;
            const totalCards = cards.length;
            const angle = 360 / totalCards;
            
            // Create dots
            cards.forEach((_, index) => {
                const dot = document.createElement('button');
                dot.className = `carousel-dot ${index === 0 ? 'active' : ''}`;
                dot.dataset.index = index;
                dot.addEventListener('click', () => goToCard(index));
                dotsContainer.appendChild(dot);
            });
            
            const dots = document.querySelectorAll('.carousel-dot');
            
            // Position cards in a circle
            function getRadius() {
                if (window.innerWidth < 480) return 200;
                if (window.innerWidth < 768) return 280;
                if (window.innerWidth < 992) return 350;
                if (window.innerWidth < 1200) return 400;
                return 450;
            }

            function positionCards() {
                const radius = getRadius();
                cards.forEach((card, index) => {
                    const cardAngle = angle * index;
                    
                    // No need to calculate x, z as transform does it all
                    card.style.transform = `rotateY(${cardAngle}deg) translateZ(${radius}px)`;
                    card.style.opacity = '1';
                    card.style.zIndex = index === currentIndex ? '10' : '1';
                });
            }
            
            // Update carousel rotation
            function updateCarousel() {
                const radius = getRadius();
                const rotation = -angle * currentIndex;
                carousel.style.transform = `translateZ(-${radius}px) rotateY(${rotation}deg)`;
                
                // Update active dot
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentIndex);
                });
            }
            
            // Navigate to specific card
            function goToCard(index) {
                currentIndex = index;
                updateCarousel();
            }
            
            // Next card
            function nextCard() {
                currentIndex = (currentIndex + 1) % totalCards;
                updateCarousel();
            }
            
            // Previous card
            function prevCard() {
                currentIndex = (currentIndex - 1 + totalCards) % totalCards;
                updateCarousel();
            }
            
            // Flip card on click
            cards.forEach(card => {
                card.addEventListener('click', function() {
                    this.classList.toggle('flipped');
                });
            });
            
            // Event listeners
            prevBtn.addEventListener('click', prevCard);
            nextBtn.addEventListener('click', nextCard);
            
            // Auto rotate
            let autoRotateInterval = setInterval(nextCard, 5000);
            
            // Pause auto rotate on hover
            carousel.addEventListener('mouseenter', () => {
                clearInterval(autoRotateInterval);
            });
            
            carousel.addEventListener('mouseleave', () => {
                autoRotateInterval = setInterval(nextCard, 5000);
            });
            
            // Initialize
            positionCards();
            updateCarousel();
            
            // Reposition on resize
            window.addEventListener('resize', positionCards);
        }

        // Testimonials Slider
        function initTestimonialsSlider() {
            const track = document.getElementById('testimonialsTrack');
            const dots = document.querySelectorAll('.testimonial-dot');
            const cards = document.querySelectorAll('.testimonial-card');
            let currentSlide = 0;
            const totalSlides = cards.length;
            
            // Calculate how many cards to show based on screen width
            function getCardsPerView() {
                if (window.innerWidth < 768) return 1;
                if (window.innerWidth < 1200) return 2;
                return 3;
            }
            
            function updateSlider() {
                const cardsPerView = getCardsPerView();
                const slideWidth = cards[0].offsetWidth + parseFloat(getComputedStyle(track).gap);
                const translateX = -(currentSlide * slideWidth);
                
                track.style.transform = `translateX(${translateX}px)`;
                
                // Update active dot
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentSlide);
                });
                
                // Hide dots that aren't needed for current view
                const maxSlide = totalSlides - cardsPerView;
                dots.forEach((dot, index) => {
                    dot.style.display = index <= maxSlide ? 'block' : 'none';
                });
            }
            
            // Dot click events
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    currentSlide = index;
                    updateSlider();
                });
            });
            
            // Auto slide
            let autoSlideInterval = setInterval(() => {
                const cardsPerView = getCardsPerView();
                const maxSlide = totalSlides - cardsPerView;
                
                if (currentSlide >= maxSlide) {
                    currentSlide = 0;
                } else {
                    currentSlide++;
                }
                
                updateSlider();
            }, 5000);
            
            // Pause auto slide on hover
            const testimonialsSection = document.querySelector('.testimonials-slider');
            testimonialsSection.addEventListener('mouseenter', () => {
                clearInterval(autoSlideInterval);
            });
            
            testimonialsSection.addEventListener('mouseleave', () => {
                autoSlideInterval = setInterval(() => {
                    const cardsPerView = getCardsPerView();
                    const maxSlide = totalSlides - cardsPerView;
                    
                    if (currentSlide >= maxSlide) {
                        currentSlide = 0;
                    } else {
                        currentSlide++;
                    }
                    
                    updateSlider();
                }, 5000);
            });
            
            // Update on resize
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    updateSlider();
                }, 250);
            });
            
            // Initialize
            updateSlider();
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize hero carousel
            showSlide(0);
            
            // Initialize 3D carousel
            init3DCarousel();
            
            // Initialize testimonials slider
            initTestimonialsSlider();
        });
    </script>
</body>
</html>