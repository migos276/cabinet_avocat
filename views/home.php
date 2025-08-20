<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo SITE_NAME; ?> - Avocats Spécialisés</title>
    <meta name="description" content="Cabinet d'avocats d'excellence spécialisé en droit des affaires, droit de la famille et droit pénal. Expertise juridique reconnue depuis plus de 20 ans.">
    <meta name="keywords" content="avocat, cabinet juridique, droit des affaires, droit de la famille, droit pénal, conseil juridique, expertise">
    <meta name="author" content="<?php echo SITE_NAME; ?>">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    

    <style>
        /* Variables CSS pour la cohérence */
        :root {
            --primary-blue: #1e3a8a;
            --secondary-blue: #3b82f6;
            --light-blue: #dbeafe;
            --accent-gold: #f59e0b;
            --accent-green: #10b981;
            --accent-purple: #8b5cf6;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-800: #1f2937;
            --shadow-light: rgba(0, 0, 0, 0.1);
            --shadow-medium: rgba(0, 0, 0, 0.15);
            --gradient-bg: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            --success-color: #059669;
            --danger-color: #dc2626;
            --border-color: #d1d5db;
            --bg-light: #f9fafb;
            --primary-color: #2563eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
        }

        /* Navbar Styles */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            padding: 0.5rem 0;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .brand-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-icon {
            width: 3rem;
            height: 3rem;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-icon i {
            color: white;
            font-size: 1.25rem;
        }

        .brand-text h1 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .brand-text p {
            font-size: 0.7rem;
            color: var(--text-light);
            margin: -0.25rem 0 0 0;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            list-style: none;
        }

        .nav-link {
            text-decoration: none;
            color: #374151;
            font-weight: 500;
            padding: 0.4rem 0.8rem;
            border-radius: 0.4rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }

        .nav-link:hover, .nav-link.active {
            color: #2563eb;
            background-color: rgba(37, 99, 235, 0.1);
        }

        .nav-link i {
            margin-right: 0.5rem;
        }

 .btn {
    display: inline-flex;
    align-items: center;
    padding: 0.6rem;
    padding-left: 1.8rem; /* Espacement à gauche */
    padding-right: 1.8rem; /* Espacement à droite */
    border-radius: 0.6rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    font-size: 0.9rem;
}
        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            box-shadow: 0 3px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid currentColor;
            color: var(--primary-blue);
        }

        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }

        /* Mobile Menu */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.3rem;
            color: #374151;
            cursor: pointer;
            padding: 0.4rem;
            border-radius: 0.4rem;
            transition: all 0.3s ease;
        }

        .mobile-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 1rem;
        }

        .mobile-nav {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg,
                rgba(94, 124, 206, 0.9) 0%,
                rgba(178, 202, 241, 0.8) 100%),
                url('public/images/avocat2.jpg') no-repeat center center;
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            text-align: center;
            color: white;
            padding-top: 80px;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            opacity: 0;
            transform: translateY(50px);
            animation: fadeInUp 1s ease-out 0.5s forwards;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #ffffff, #e2e8f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero .lead {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .hero-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            align-items: center;
        }

        /* Sections générales */
        .section {
            padding: 80px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title .badge {
            display: inline-block;
            background: var(--light-blue);
            color: var(--primary-blue);
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: var(--primary-blue);
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .section-title .lead {
            font-size: 1.2rem;
            color: var(--text-light);
            max-width: 600px;
            margin: 0 auto;
        }

        /* About Section */
        .section_about {
            padding: 80px 0;
            background: var(--gradient-bg);
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .image-area {
            position: relative;
            height: 500px;
            border-radius: 20px;
            overflow: hidden;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 50%, #93c5fd 100%);
        }

        .image-area img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .decorative-element {
            position: absolute;
            border-radius: 50%;
            opacity: 0.7;
        }

        .decorative-element.blue {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            top: 10%;
            left: 10%;
            animation: float 6s ease-in-out infinite;
        }

        .decorative-element.yellow {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            top: 60%;
            right: 20%;
            animation: float 6s ease-in-out infinite 2s;
        }

        .decorative-element.purple {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            bottom: 20%;
            left: 60%;
            animation: float 6s ease-in-out infinite 4s;
        }

        .feature-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px var(--shadow-light);
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px var(--shadow-medium);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .feature-icon.blue {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        }

        .feature-icon.yellow {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .feature-title {
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
        }

        .feature-subtitle {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        /* Services Grid */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .service-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 0 5px 15px var(--shadow-light);
            transition: all 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px var(--shadow-medium);
        }

        .service-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .service-icon i {
            font-size: 2rem;
            color: white;
        }

        .service-card h4 {
            font-size: 1.5rem;
            color: var(--primary-blue);
            margin-bottom: 1rem;
        }

        .service-card p {
            color: var(--text-light);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        /* Contact Form */
        .contact-section {
            background: var(--gradient-bg);
        }

        .contact-form {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 60px var(--shadow-light);
            max-width: 800px;
            margin: 0 auto;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--secondary-blue);
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-control-lg {
            padding: 1rem 1.25rem;
            font-size: 1.1rem;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #1f2937, #111827);
            color: white;
            padding: 4rem 0 2rem;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h6 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .footer-section a {
            color: #d1d5db;
            text-decoration: none;
            transition: color 0.3s ease;
            display: block;
            margin-bottom: 0.5rem;
        }

        .footer-section a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid #374151;
            padding-top: 2rem;
            text-align: center;
            color: #9ca3af;
        }

        /* Scroll to Top */
        .scroll-to-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            background: var(--secondary-blue);
            color: white;
            border: none;
            border-radius: 50%;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
            opacity: 0;
            transform: scale(0);
            z-index: 999;
        }

        .scroll-to-top.show {
            opacity: 1;
            transform: scale(1);
        }

        .scroll-to-top:hover {
            background: var(--primary-blue);
            transform: scale(1.1);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        /* Alert Messages */
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Section Nos Valeurs */
        .values-section {
            padding: 80px 0;
            background: var(--white);
            position: relative;
            overflow: hidden;
        }

        .values-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background: linear-gradient(135deg, 
                rgba(30, 58, 138, 0.02) 0%, 
                rgba(59, 130, 246, 0.03) 50%, 
                rgba(245, 158, 11, 0.02) 100%);
            z-index: 1;
        }

        .values-section .container {
            position: relative;
            z-index: 2;
        }

        .badge {
            display: inline-block;
            background: var(--light-blue);
            color: var(--primary-blue);
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .value-card {
            background: var(--white);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 0 8px 25px var(--shadow-light);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(59, 130, 246, 0.08);
        }

        .value-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, var(--secondary-blue), var(--accent-gold));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }

        .value-card:hover::before {
            transform: scaleX(1);
        }

        .value-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 40px var(--shadow-medium);
        }

        .value-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: all 0.4s ease;
        }

        .value-icon i {
            font-size: 2.2rem;
            color: white;
            z-index: 2;
            position: relative;
            transition: transform 0.4s ease;
        }

        .value-card:hover .value-icon i {
            transform: scale(1.1) rotate(5deg);
        }

        .value-icon.blue {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        }

        .value-icon.gold {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .value-icon.green {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .value-card h4 {
            font-size: 1.4rem;
            color: var(--primary-blue);
            margin-bottom: 1rem;
            font-weight: 700;
            transition: color 0.3s ease;
        }

        .value-card:hover h4 {
            color: var(--secondary-blue);
        }

        .value-card p {
            color: var(--text-light);
            line-height: 1.7;
            margin-bottom: 1.5rem;
            opacity: 0.9;
            transition: all 0.3s ease;
        }

        .value-card:hover p {
            opacity: 1;
            color: var(--text-dark);
        }

        .values-commitment {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            margin-top: 3rem;
            position: relative;
            overflow: hidden;
        }

        .values-commitment::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.05) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        .commitment-content {
            position: relative;
            z-index: 2;
        }

        .commitment-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .stat-item {
            padding: 1rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary-blue);
            display: block;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-light);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .commitment-text {
            max-width: 600px;
            margin: 0 auto;
            font-size: 1.1rem;
            color: var(--text-dark);
            line-height: 1.6;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        /* Styles pour l'upload de fichiers */
        .file-upload-section {
            margin: 2rem 0;
            padding: 1.5rem;
            border: 2px dashed var(--border-color);
            border-radius: 0.5rem;
            background: var(--bg-light);
            transition: all 0.3s ease;
        }

        .file-upload-section.drag-over {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.05);
        }

        .file-upload-header {
            text-align: center;
            margin-bottom: 1rem;
        }

        .file-upload-icon {
            font-size: 2rem;
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }

        .file-input {
            display: none;
        }

        .file-upload-button {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .file-upload-button:hover {
            background: var(--secondary-blue);
        }

        .file-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .file-item {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 1rem;
            position: relative;
        }

        .file-item-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.5rem;
        }

        .file-icon {
            font-size: 1.5rem;
            color: var(--danger-color);
        }

        .file-remove {
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .file-remove:hover {
            background: var(--danger-color);
            color: white;
        }

        .file-info {
            font-size: 0.875rem;
        }

        .file-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
            word-break: break-word;
        }

        .file-size {
            color: var(--text-light);
        }

        .file-status {
            margin-top: 0.5rem;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            display: inline-block;
        }

        .file-status.success {
            background: rgba(5, 150, 105, 0.1);
            color: var(--success-color);
        }

        .file-status.error {
            background: rgba(220, 38, 38, 0.1);
            color: var(--danger-color);
        }

        .upload-info {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-light);
            text-align: center;
        }

        .file-preview-btn {
            background: var(--secondary-blue);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.8rem;
            margin-top: 0.5rem;
            transition: all 0.3s ease;
        }

        .file-preview-btn:hover {
            background: var(--primary-blue);
            transform: translateY(-1px);
        }

        #contactMessage {
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 0.5rem;
            display: none;
        }

        #contactMessage.success {
            background: rgba(5, 150, 105, 0.1);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }

        #contactMessage.error {
            background: rgba(220, 38, 38, 0.1);
            color: var(--danger-color);
            border: 1px solid var(--danger-color);
        }

        /* Styles pour modal de prévisualisation */
        .file-preview-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-backdrop {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .modal-content {
            background: white;
            border-radius: 15px;
            max-width: 90vw;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }
        
        .modal-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
        }
        
        .modal-header h3 {
            margin: 0;
            font-size: 1.2rem;
            color: #1f2937;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #6b7280;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .modal-close:hover {
            background: #ef4444;
            color: white;
        }
        
        .modal-body {
            padding: 1.5rem;
            text-align: center;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar-nav {
                display: none;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero .lead {
                font-size: 1.1rem;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .about-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .image-area {
                height: 300px;
            }

            .section-title h2 {
                font-size: 2rem;
            }

            .services-grid {
                grid-template-columns: 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .contact-form {
                padding: 2rem;
            }

            .section {
                padding: 60px 0;
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }

            .values-section {
                padding: 60px 0;
            }

            .values-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .value-card {
                padding: 2rem;
            }

            .values-commitment {
                padding: 2rem;
            }

            .commitment-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .stat-number {
                font-size: 2rem;
            }

            .modal-content {
                max-width: 95vw;
                max-height: 95vh;
                margin: 1rem;
            }
            
            .modal-body iframe,
            .modal-body img {
                width: 100%;
                height: auto !important;
                max-height: 60vh !important;
            }
        }

        @media (max-width: 480px) {
            .hero h1 {
                font-size: 2rem;
            }

            .container {
                padding: 0 0.5rem;
            }

            .service-card {
                padding: 1.5rem;
            }

            .value-card {
                padding: 1.5rem;
            }

            .value-icon {
                width: 70px;
                height: 70px;
            }

            .value-icon i {
                font-size: 1.8rem;
            }

            .commitment-stats {
                grid-template-columns: 1fr;
            }
        }

        /* Utility Classes */
        .text-center { text-align: center; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-8 { margin-bottom: 2rem; }
        .max-w-4xl { max-width: 56rem; }
        .mx-auto { margin-left: auto; margin-right: auto; }
    /* Team Section Horizontal Scroll */
.team-container {
    position: relative;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 3rem;
    display: flex;
    align-items: center;
}

.team-grid {
    display: flex;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: var(--secondary-blue) var(--gray-100);
    gap: 2rem;
    padding: 1rem 0;
}

.team-grid::-webkit-scrollbar {
    height: 8px;
}

.team-grid::-webkit-scrollbar-track {
    background: var(--gray-100);
    border-radius: 4px;
}

.team-grid::-webkit-scrollbar-thumb {
    background: var(--secondary-blue);
    border-radius: 4px;
}

.team-grid::-webkit-scrollbar-thumb:hover {
    background: var(--primary-blue);
}

.team-card {
    flex: 0 0 300px;
    background: white;
    border-radius: 20px;
    padding: 2.5rem;
    text-align: center;
    box-shadow: 0 5px 15px var(--shadow-light);
    transition: all 0.3s ease;
    scroll-snap-align: center;
    min-width: 300px;
}

.team-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px var(--shadow-medium);
}

.team-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
    border-radius: 15px;
    margin-bottom: 1.5rem;
}

.team-position {
    color: var(--secondary-blue);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.team-description {
    color: var(--text-light);
    line-height: 1.6;
}

.team-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: var(--white);
    border: 2px solid var(--border-color);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px var(--shadow-light);
    z-index: 10;
}

.team-nav-prev {
    left: 0;
}

.team-nav-next {
    right: 0;
}

.team-nav-btn:hover {
    background: var(--secondary-blue);
    color: var(--white);
    border-color: var(--secondary-blue);
    transform: translateY(-50%) scale(1.1);
}

.team-nav-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.team-nav-btn i {
    font-size: 1.2rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .team-container {
        padding: 0 2rem;
    }

    .team-grid {
        gap: 1.5rem;
    }

    .team-card {
        flex: 0 0 280px;
        min-width: 280px;
    }

    .team-nav-btn {
        width: 35px;
        height: 35px;
    }

    .team-nav-btn i {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .team-container {
        padding: 0 1rem;
    }

    .team-grid {
        gap: 1rem;
    }

    .team-card {
        flex: 0 0 260px;
        min-width: 260px;
        padding: 1.5rem;
    }

    .team-nav-btn {
        width: 30px;
        height: 30px;
    }
}
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <a href="/" class="navbar-brand">
                <div class="brand-container">
                    <div class="brand-icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div class="brand-text">
                        <h1>Cabinet Excellence</h1>
                        <p>Avocats Spécialisés</p>
                    </div>
                </div>
            </a>

            <ul class="navbar-nav">
                <li><a href="#home" class="nav-link active">
                    <i class="fas fa-home"></i>Accueil
                </a></li>
                <li><a href="#about" class="nav-link">
                    <i class="fas fa-info-circle"></i>À propos
                </a></li>
                <li><a href="#services" class="nav-link">
                    <i class="fas fa-gavel"></i>Nos services
                </a></li>
                <li><a href="#team" class="nav-link">
                    <i class="fas fa-users"></i>Équipe
                </a></li>
                <li><a href="#contact" class="btn btn-primary">
                    <i class="fas fa-calendar-alt"></i>Contact
                </a></li>
            </ul>

            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="mobile-menu" id="mobileMenu">
                <div class="mobile-nav">
                    <a href="#home" class="nav-link">Accueil</a>
                    <a href="#about" class="nav-link">À propos</a>
                    <a href="#services" class="nav-link">Nos services</a>
                    <a href="#team" class="nav-link">Équipe</a>
                    <a href="#contact" class="nav-link">Contact</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="container">
            <div class="hero-content">
                <h1><?php echo htmlspecialchars($content['hero']['title'] ?? 'Excellence Juridique à Votre Service'); ?></h1>
                <p class="lead">
                    <?php echo htmlspecialchars($content['hero']['subtitle'] ?? 'Depuis plus de 20 ans, nous accompagnons nos clients avec expertise, intégrité et dévouement dans tous leurs défis juridiques les plus complexes.'); ?>
                </p>
                <div class="hero-buttons">
                    <a href="#contact" class="btn btn-secondary btn-lg">
                        <i class="fas fa-calendar-alt"></i>
                                PRENDRE RENDEZ-VOUS
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section_about">
        <div class="container">
            <div class="about-grid">
                <div class="image-area">
                    <img src="public/images/avocat1.jpg" alt="Avocat en action">
                    <div class="decorative-element blue"></div>
                    <div class="decorative-element yellow"></div>
                    <div class="decorative-element purple"></div>
                </div>
                <div>
                    <span class="badge">À propos de nous</span>
                    <h2 class="mb-6"><?php echo htmlspecialchars($content['about']['title'] ?? 'Votre Réussite, Notre Mission'); ?></h2>
                    <p class="lead mb-6">
                        <?php echo htmlspecialchars($content['about']['subtitle'] ?? 'Fort d\'une expérience reconnue et d\'une approche personnalisée, notre cabinet vous offre un accompagnement juridique d\'excellence adapté à vos besoins spécifiques.'); ?>
                    </p>

                    <div class="feature-cards mb-8">
                        <div class="feature-card">
                            <div class="feature-icon blue">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <h4 class="feature-title">Confidentialité</h4>
                                <p class="feature-subtitle">Absolue</p>
                            </div>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon yellow">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <div>
                                <h4 class="feature-title">Accompagnement</h4>
                                <p class="feature-subtitle">Personnalisé</p>
                            </div>
                        </div>
                    </div>

                    <p class="mb-6" style="color: var(--text-light); line-height: 1.6;">
                        Que vous soyez un particulier ou une entreprise, nous mettons notre expertise à votre disposition
                        pour défendre vos intérêts et vous accompagner dans vos projets les plus complexes.
                    </p>

                    <a href="#services" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i>
                        En savoir plus
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Nos Valeurs -->
    <section class="values-section">
        <div class="container">
            <div class="section-title">
                <span class="badge">Nos valeurs</span>
                <h2>Les Principes qui Nous Guident</h2>
                <p class="lead">
                    Des valeurs fortes et authentiques qui définissent notre approche professionnelle et notre engagement envers nos clients
                </p>
            </div>

            <div class="values-grid">
                <div class="value-card fade-in-up" style="animation-delay: 0.1s;">
                    <div class="value-icon blue">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Intégrité</h4>
                    <p>
                        L'honnêteté et la transparence sont au cœur de toutes nos relations. Nous privilégions 
                        toujours la vérité et agissons avec une éthique irréprochable dans chaque dossier.
                    </p>
                </div>

                <div class="value-card fade-in-up" style="animation-delay: 0.2s;">
                    <div class="value-icon gold">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h4>Excellence</h4>
                    <p>
                        Nous visons l'excellence dans chaque mission, en actualisant constamment nos connaissances 
                        et en déployant tout notre savoir-faire pour obtenir les meilleurs résultats.
                    </p>
                </div>

                <div class="value-card fade-in-up" style="animation-delay: 0.3s;">
                    <div class="value-icon green">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h4>Engagement</h4>
                    <p>
                        Votre réussite est notre priorité. Nous nous engageons pleinement dans chaque dossier 
                        avec détermination et persévérance jusqu'à l'obtention du résultat souhaité.
                    </p>
                </div>   
            </div>

            <!-- Section engagement/statistiques -->
            <div class="values-commitment fade-in-up" style="animation-delay: 0.6s;">
                <div class="commitment-content">
                    <div class="commitment-stats">
                        <div class="stat-item">
                            <span class="stat-number">20+</span>
                            <span class="stat-label">Années d'expérience</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">98%</span>
                            <span class="stat-label">Clients satisfaits</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">1000+</span>
                            <span class="stat-label">Dossiers traités</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">24h</span>
                            <span class="stat-label">Délai de réponse</span>
                        </div>
                    </div>
                    
                    <div class="commitment-text">
                        <strong>Notre engagement :</strong> Chaque client mérite une approche personnalisée et des conseils 
                        juridiques de la plus haute qualité. Ces valeurs ne sont pas seulement des mots, elles guident 
                        chacune de nos décisions et actions au quotidien.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="section">
        <div class="container">
            <div class="section-title">
                <span class="badge mb-4">Nos spécialisations</span>
                <h2><?php echo htmlspecialchars($content['services']['title'] ?? 'Domaines d\'Expertise'); ?></h2>
                <p class="lead">
                    <?php echo htmlspecialchars($content['services']['subtitle'] ?? 'Une expertise reconnue dans des domaines juridiques essentiels pour répondre à tous vos besoins'); ?>
                </p>
            </div>

            <div class="services-grid">
                <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <div class="service-icon" style="background: <?php echo htmlspecialchars($service['color']); ?>;">
                        <i class="<?php echo htmlspecialchars($service['icon']); ?>"></i>
                    </div>
                    <h4><?php echo htmlspecialchars($service['title']); ?></h4>
                    <p><?php echo htmlspecialchars($service['description']); ?></p>
                    <a href="/service/<?php echo $service['id']; ?>" class="btn btn-outline">
                        <i class="fas fa-arrow-right"></i>
                        En savoir plus
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <!-- Team Section -->
    <section id="team" class="section" style="background: var(--gradient-bg);">
        <div class="container">
            <div class="section-title">
                <span class="badge mb-4">Notre équipe</span>
                <h2><?php echo htmlspecialchars($content['team']['title'] ?? 'Des Experts à Vos Côtés'); ?></h2>
                <p class="lead">
                    <?php echo htmlspecialchars($content['team']['subtitle'] ?? 'Des avocats expérimentés et passionnés, reconnus pour leur expertise et leur engagement'); ?>
                </p>
            </div>

            <div class="team-container">
                <button class="team-nav-btn team-nav-prev" aria-label="Précédent">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="team-grid">
                    <?php foreach ($team as $member): ?>
                    <div class="team-card">
                        <img src="<?php echo htmlspecialchars($member['image_path']); ?>"
                             alt="<?php echo htmlspecialchars($member['name']); ?>"
                             class="team-image">
                        <h4><?php echo htmlspecialchars($member['name']); ?></h4>
                        <p class="team-position"><?php echo htmlspecialchars($member['position']); ?></p>
                        <p class="team-description"><?php echo htmlspecialchars($member['description']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="team-nav-btn team-nav-next" aria-label="Suivant">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </section>
    <!-- Contact Section -->
    <section id="contact" class="section contact-section">
        <div class="container">
            <div class="max-w-4xl mx-auto">
                <div class="section-title">
                    <span class="badge mb-4">Nous contacter</span>
                    <h2>Parlons de Votre Situation</h2>
                    <p class="lead">
                        Bénéficiez d'un premier échange gratuit pour évaluer vos besoins juridiques
                    </p>
                </div>
                <div class="contact-form">
                    <div id="contactMessage"></div>
                    <form id="contactForm" method="POST" action="/contact" enctype="multipart/form-data">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name" class="form-label">Nom complet *</label>
                                <input type="text" class="form-control form-control-lg" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control form-control-lg" id="phone" name="phone">
                            </div>
                            <div class="form-group">
                                <label for="subject" class="form-label">Domaine juridique</label>
                                <select class="form-control form-control-lg" id="subject" name="subject">
                                    <option value="">Sélectionnez un domaine</option>
                                    <option value="droit-des-affaires">Droit des Affaires</option>
                                    <option value="droit-de-la-famille">Droit de la Famille</option>
                                    <option value="droit-penal">Droit Pénal</option>
                                    <option value="droit-immobilier">Droit Immobilier</option>
                                    <option value="droit-du-travail">Droit du Travail</option>
                                    <option value="droit-des-assurances">Droit des Assurances</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group mb-8">
                            <label for="message" class="form-label">Décrivez votre situation *</label>
                            <textarea class="form-control form-control-lg" id="message" name="message" rows="5" required
                                      placeholder="Expliquez-nous brièvement votre situation juridique..."></textarea>
                        </div>
                        
                        <!-- Section d'upload de fichiers -->
                        <div class="file-upload-section" id="fileUploadSection">
                            <div class="file-upload-header">
                                <div class="file-upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <h3>Joindre des documents</h3>
                                <p>Glissez-déposez vos fichiers ici ou cliquez pour sélectionner</p>
                                <input type="file" id="fileInput" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="file-input">
                                <button type="button" class="file-upload-button" onclick="document.getElementById('fileInput').click()">
                                    <i class="fas fa-plus"></i> Sélectionner des fichiers
                                </button>
                            </div>
                            <div class="upload-info">
                                <i class="fas fa-info-circle"></i>
                                Formats acceptés : PDF, DOC, DOCX, JPG, PNG (Max: 10MB par fichier)
                            </div>
                            <div class="file-preview" id="filePreview"></div>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg" style="padding: 1rem 2rem;">
                                <i class="fas fa-paper-plane"></i>
                                Demander une consultation
                            </button>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin-top: 1rem;">
                                <i class="fas fa-shield-alt"></i>
                                Vos données sont protégées et confidentielles
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Company Info -->
                <div class="footer-section">
                    <div class="brand-container mb-4">
                        <div class="brand-icon">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <div class="brand-text">
                            <h6 style="color: white; margin: 0;">Cabinet Excellence</h6>
                            <p style="color: #9ca3af; font-size: 0.9rem; margin: 0;">Avocats Spécialisés</p>
                        </div>
                    </div>
                    <p style="color: #d1d5db; line-height: 1.6; margin-bottom: 1rem;">
                        Excellence juridique et accompagnement personnalisé pour tous vos besoins légaux depuis plus de 20 ans.
                    </p>
                </div>

                <!-- Navigation -->
                <div class="footer-section">
                    <h6>Navigation</h6>
                    <a href="#home">Accueil</a>
                    <a href="#about">À propos</a>
                    <a href="#services">Expertises</a>
                    <a href="#team">Équipe</a>
                    <a href="#contact">Contact</a>
                </div>

                <!-- Expertise Areas -->
                <div class="footer-section">
                    <h6>Domaines d'expertise</h6>
                    <?php foreach (array_slice($services, 0, 6) as $service): ?>
                    <a href="#services"><?php echo htmlspecialchars($service['title']); ?></a>
                    <?php endforeach; ?>
                </div>

                <!-- Contact Info -->
                <div class="footer-section">
                    <h6>Contact</h6>
                    <div style="margin-bottom: 1rem;">
                        <p style="color: #d1d5db; margin: 0; display: flex; align-items: flex-start; gap: 0.5rem;">
                            <i class="fas fa-map-marker-alt" style="color: #60a5fa; margin-top: 0.2rem;"></i>
                            <span>123 Avenue des Champs-Élysées<br>75008 Paris, France</span>
                        </p>
                    </div>
                    <p style="color: #d1d5db; margin: 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-phone" style="color: #60a5fa;"></i>
                        <a href="tel:+33123456789" style="color: #d1d5db;">+33 1 23 45 67 89</a>
                    </p>
                    <p style="color: #d1d5db; margin: 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-envelope" style="color: #60a5fa;"></i>
                        <a href="mailto:<?php echo ADMIN_EMAIL; ?>" style="color: #d1d5db;"><?php echo ADMIN_EMAIL; ?></a>
                    </p>
                    <p style="color: #d1d5db; margin: 0.5rem 0; display: flex; align-items: flex-start; gap: 0.5rem;">
                        <i class="fas fa-clock" style="color: #60a5fa; margin-top: 0.2rem;"></i>
                        <span>Lun-Ven: 9h-18h<br>Sam: 9h-12h</span>
                    </p>
                </div>
            </div>

            <div class="footer-bottom">
                <p>© <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button class="scroll-to-top" id="scrollToTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        function initializeTeamScroll() {
    const teamGrid = document.querySelector('.team-grid');
    const prevBtn = document.querySelector('.team-nav-prev');
    const nextBtn = document.querySelector('.team-nav-next');

    if (!teamGrid || !prevBtn || !nextBtn) return;

    function updateButtonState() {
        const scrollLeft = teamGrid.scrollLeft;
        const maxScroll = teamGrid.scrollWidth - teamGrid.clientWidth;

        prevBtn.disabled = scrollLeft <= 0;
        nextBtn.disabled = scrollLeft >= maxScroll - 1;
    }

    prevBtn.addEventListener('click', () => {
        teamGrid.scrollBy({
            left: -320,
            behavior: 'smooth'
        });
    });

    nextBtn.addEventListener('click', () => {
        teamGrid.scrollBy({
            left: 320,
            behavior: 'smooth'
        });
    });

    teamGrid.addEventListener('scroll', updateButtonState);
    window.addEventListener('resize', updateButtonState);
    updateButtonState();

    // Gestion du défilement tactile
    let isDragging = false;
    let startX, scrollLeft;

    teamGrid.addEventListener('touchstart', (e) => {
        isDragging = true;
        startX = e.touches[0].pageX - teamGrid.offsetLeft;
        scrollLeft = teamGrid.scrollLeft;
    });

    teamGrid.addEventListener('touchend', () => {
        isDragging = false;
    });

    teamGrid.addEventListener('touchmove', (e) => {
        if (!isDragging) return;
        e.preventDefault();
        const x = e.touches[0].pageX - teamGrid.offsetLeft;
        const walk = (x - startX) * 2;
        teamGrid.scrollLeft = scrollLeft - walk;
    });
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', initializeTeamScroll);
        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            const toggle = document.querySelector('.mobile-menu-toggle i');
            
            if (mobileMenu.style.display === 'block') {
                mobileMenu.style.display = 'none';
                toggle.className = 'fas fa-bars';
            } else {
                mobileMenu.style.display = 'block';
                toggle.className = 'fas fa-times';
            }
        }

        // Close mobile menu when clicking on links
        document.querySelectorAll('.mobile-nav a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('mobileMenu').style.display = 'none';
                document.querySelector('.mobile-menu-toggle i').className = 'fas fa-bars';
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
            }
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offsetTop = target.offsetTop - 80;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Active navigation highlighting
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-link:not(.btn)');

            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 100;
                const sectionHeight = section.clientHeight;
                if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });

        // Scroll to top button
        const scrollToTopBtn = document.getElementById('scrollToTop');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        });

        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileToggle = document.querySelector('.mobile-menu-toggle');
            
            if (!mobileMenu.contains(e.target) && !mobileToggle.contains(e.target)) {
                mobileMenu.style.display = 'none';
                document.querySelector('.mobile-menu-toggle i').className = 'fas fa-bars';
            }
        });

        // Animation au scroll pour les cartes
        function animateOnScroll() {
            const cards = document.querySelectorAll('.value-card, .values-commitment');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in-up');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            cards.forEach(card => {
                observer.observe(card);
            });
        }

        // Animation des compteurs
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');
            
            counters.forEach(counter => {
                const target = counter.textContent;
                const isPercent = target.includes('%');
                const isPlus = target.includes('+');
                const isTime = target.includes('h');
                
                let endValue;
                if (isPercent) {
                    endValue = parseInt(target);
                } else if (isTime) {
                    endValue = parseInt(target);
                } else if (isPlus) {
                    endValue = parseInt(target);
                } else {
                    endValue = parseInt(target);
                }

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            animateCounter(counter, endValue, isPercent, isPlus, isTime);
                            observer.unobserve(counter);
                        }
                    });
                }, { threshold: 0.5 });

                observer.observe(counter);
            });
        }

        function animateCounter(element, endValue, isPercent, isPlus, isTime) {
            let startValue = 0;
            const duration = 2000;
            const increment = endValue / (duration / 16);

            function updateCounter() {
                if (startValue < endValue) {
                    startValue += increment;
                    let displayValue = Math.floor(startValue);
                    
                    if (isPercent) {
                        element.textContent = displayValue + '%';
                    } else if (isPlus) {
                        element.textContent = displayValue + '+';
                    } else if (isTime) {
                        element.textContent = displayValue + 'h';
                    } else {
                        if (endValue >= 1000) {
                            element.textContent = Math.floor(startValue) + '+';
                        } else {
                            element.textContent = displayValue;
                        }
                    }
                    
                    requestAnimationFrame(updateCounter);
                } else {
                    if (isPercent) {
                        element.textContent = endValue + '%';
                    } else if (isPlus) {
                        element.textContent = endValue + '+';
                    } else if (isTime) {
                        element.textContent = endValue + 'h';
                    } else if (endValue >= 1000) {
                        element.textContent = endValue + '+';
                    } else {
                        element.textContent = endValue;
                    }
                }
            }
            
            updateCounter();
        }

        // Gestionnaire de fichiers - CLASSE UNIQUE POUR ÉVITER LES DOUBLONS
        class FileUploadManager {
            constructor() {
                this.files = [];
                this.maxFileSize = 10 * 1024 * 1024; // 10MB
                this.maxFiles = 5;
                this.allowedTypes = [
                    'application/pdf', 
                    'application/msword', 
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
                    'image/jpeg', 
                    'image/jpg', 
                    'image/png'
                ];
                this.init();
            }

            init() {
                const fileInput = document.getElementById('fileInput');
                const uploadSection = document.getElementById('fileUploadSection');
                
                if (!fileInput || !uploadSection) return;
                
                // Events pour le drag & drop
                uploadSection.addEventListener('dragover', this.handleDragOver.bind(this));
                uploadSection.addEventListener('dragleave', this.handleDragLeave.bind(this));
                uploadSection.addEventListener('drop', this.handleDrop.bind(this));
                
                // Event pour la sélection de fichiers
                fileInput.addEventListener('change', this.handleFileSelect.bind(this));
                
                // IMPORTANT : UN SEUL gestionnaire pour le formulaire
                const form = document.getElementById('contactForm');
                if (form) {
                    form.addEventListener('submit', this.handleFormSubmit.bind(this));
                }
            }

            handleDragOver(e) {
                e.preventDefault();
                document.getElementById('fileUploadSection').classList.add('drag-over');
            }

            handleDragLeave(e) {
                e.preventDefault();
                if (!e.currentTarget.contains(e.relatedTarget)) {
                    document.getElementById('fileUploadSection').classList.remove('drag-over');
                }
            }

            handleDrop(e) {
                e.preventDefault();
                document.getElementById('fileUploadSection').classList.remove('drag-over');
                const files = Array.from(e.dataTransfer.files);
                this.addFiles(files);
            }

            handleFileSelect(e) {
                const files = Array.from(e.target.files);
                this.addFiles(files);
            }

            addFiles(newFiles) {
                for (const file of newFiles) {
                    if (this.files.length >= this.maxFiles) {
                        this.showMessage(`Maximum ${this.maxFiles} fichiers autorisés`, 'error');
                        break;
                    }

                    if (this.validateFile(file)) {
                        const fileId = 'file_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                        const fileObj = {
                            id: fileId,
                            file: file,
                            name: file.name,
                            size: file.size,
                            type: file.type,
                            status: 'ready'
                        };
                        
                        this.files.push(fileObj);
                        this.renderFilePreview(fileObj);
                    }
                }
                this.updateFileInput();
            }

            validateFile(file) {
                if (!this.allowedTypes.includes(file.type)) {
                    this.showMessage(`Type de fichier non autorisé: ${file.name}`, 'error');
                    return false;
                }

                if (file.size > this.maxFileSize) {
                    this.showMessage(`Fichier trop volumineux: ${file.name} (Max: 10MB)`, 'error');
                    return false;
                }

                if (this.files.some(f => f.name === file.name && f.size === file.size)) {
                    this.showMessage(`Fichier déjà ajouté: ${file.name}`, 'error');
                    return false;
                }

                return true;
            }

            renderFilePreview(fileObj) {
                const preview = document.getElementById('filePreview');
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                fileItem.id = fileObj.id;

                const iconClass = this.getFileIcon(fileObj.type);
                const formattedSize = this.formatFileSize(fileObj.size);

                fileItem.innerHTML = `
                    <div class="file-item-header">
                        <i class="${iconClass} file-icon"></i>
                        <button type="button" class="file-remove" onclick="window.fileUploader.removeFile('${fileObj.id}')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="file-info">
                        <div class="file-name">${this.escapeHtml(fileObj.name)}</div>
                        <div class="file-size">${formattedSize}</div>
                        <div class="file-status success">Prêt à envoyer</div>
                    </div>
                    ${this.canPreview(fileObj.type) ? `
                        <div class="file-preview-container">
                            <button type="button" class="file-preview-btn" onclick="window.fileUploader.previewFile('${fileObj.id}')">
                                <i class="fas fa-eye"></i> Aperçu
                            </button>
                        </div>
                    ` : ''}
                `;

                preview.appendChild(fileItem);
            }

            removeFile(fileId) {
                this.files = this.files.filter(f => f.id !== fileId);
                const fileElement = document.getElementById(fileId);
                if (fileElement) {
                    fileElement.remove();
                }
                this.updateFileInput();
            }

            updateFileInput() {
                const fileInput = document.getElementById('fileInput');
                const dataTransfer = new DataTransfer();
                
                this.files.forEach(fileObj => {
                    dataTransfer.items.add(fileObj.file);
                });
                
                fileInput.files = dataTransfer.files;
            }

            canPreview(type) {
                return type === 'application/pdf' || type.startsWith('image/');
            }

            previewFile(fileId) {
                const fileObj = this.files.find(f => f.id === fileId);
                if (!fileObj) return;

                if (fileObj.type === 'application/pdf') {
                    this.previewPDF(fileObj);
                } else if (fileObj.type.startsWith('image/')) {
                    this.previewImage(fileObj);
                }
            }

            previewPDF(fileObj) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const modal = document.createElement('div');
                    modal.className = 'file-preview-modal';
                    modal.innerHTML = `
                        <div class="modal-backdrop" onclick="this.parentElement.remove()">
                            <div class="modal-content" onclick="event.stopPropagation()">
                                <div class="modal-header">
                                    <h3>${this.escapeHtml(fileObj.name)}</h3>
                                    <button class="modal-close" onclick="this.closest('.file-preview-modal').remove()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <iframe src="${e.target.result}" style="width: 100%; height: 600px; border: none;"></iframe>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);
                };
                reader.readAsDataURL(fileObj.file);
            }

            previewImage(fileObj) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const modal = document.createElement('div');
                    modal.className = 'file-preview-modal';
                    modal.innerHTML = `
                        <div class="modal-backdrop" onclick="this.parentElement.remove()">
                            <div class="modal-content" onclick="event.stopPropagation()">
                                <div class="modal-header">
                                    <h3>${this.escapeHtml(fileObj.name)}</h3>
                                    <button class="modal-close" onclick="this.closest('.file-preview-modal').remove()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <img src="${e.target.result}" style="max-width: 100%; max-height: 80vh; object-fit: contain;" alt="${this.escapeHtml(fileObj.name)}">
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);
                };
                reader.readAsDataURL(fileObj.file);
            }

            async handleFormSubmit(e) {
                e.preventDefault();
                e.stopImmediatePropagation(); // Empêche tout autre gestionnaire de se déclencher
                
                const form = e.target;
                const formData = new FormData(form);
                
                // Validation des champs requis
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                let errors = [];

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.style.borderColor = '#ef4444';
                        isValid = false;
                        errors.push(`${field.name} est requis`);
                    } else {
                        field.style.borderColor = '#e5e7eb';
                    }
                });

                // Validation email
                const email = document.getElementById('email');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email.value && !emailRegex.test(email.value)) {
                    email.style.borderColor = '#ef4444';
                    isValid = false;
                    errors.push('Format email invalide');
                }

                if (!isValid) {
                    this.showMessage('Erreurs: ' + errors.join(', '), 'error');
                    return;
                }

                const submitButton = form.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
                submitButton.disabled = true;

                try {
                    // Ajouter les fichiers au FormData
                    this.files.forEach((fileObj, index) => {
                        formData.append(`documents[${index}]`, fileObj.file);
                    });

                    const response = await fetch('/contact', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        this.showMessage(
                            data.message + 
                            (data.uploaded_files > 0 ? ` (${data.uploaded_files} fichier(s) joint(s))` : ''), 
                            'success'
                        );
                        form.reset();
                        this.files = [];
                        document.getElementById('filePreview').innerHTML = '';
                        
                        // Reset field borders
                        requiredFields.forEach(field => {
                            field.style.borderColor = '#e5e7eb';
                        });
                    } else {
                        if (data.errors) {
                            this.showMessage('Erreurs: ' + data.errors.join(', '), 'error');
                        } else {
                            this.showMessage(data.message, 'error');
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.showMessage('Erreur lors de l\'envoi du message', 'error');
                } finally {
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                }
            }

            getFileIcon(type) {
                if (type === 'application/pdf') return 'fas fa-file-pdf';
                if (type.includes('word') || type.includes('document')) return 'fas fa-file-word';
                if (type.includes('image')) return 'fas fa-file-image';
                return 'fas fa-file';
            }

            formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            escapeHtml(unsafe) {
                return unsafe
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            showMessage(text, type) {
                const messageElement = document.getElementById('contactMessage');
                if (!messageElement) return;
                
                messageElement.innerHTML = `
                    <div class="alert alert-${type === 'error' ? 'error' : 'success'}">
                        <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'check-circle'}"></i>
                        ${text}
                    </div>
                `;
                messageElement.style.display = 'block';
                
                // Auto-hide après 5 secondes
                setTimeout(() => {
                    messageElement.style.display = 'none';
                }, 5000);
                
                // Scroll vers le message
                messageElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        // Initialisation au chargement de la page - UNE SEULE FOIS
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser les animations
            animateOnScroll();
            animateCounters();
            
            // Ajouter l'effet parallaxe seulement sur desktop
            if (window.innerWidth > 768) {
                addParallaxEffect();
            }
            
            // Initialiser le gestionnaire de fichiers - STOCKÉ GLOBALEMENT
            window.fileUploader = new FileUploadManager();
            
            console.log('Site Cabinet Juridique Excellence chargé avec succès');
        });

        // Effet parallaxe léger pour les cartes
        function addParallaxEffect() {
            const cards = document.querySelectorAll('.value-card');
            
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const rate = scrolled * -0.5;
                
                cards.forEach((card, index) => {
                    const yPos = -(rate / (index + 1));
                    if (Math.abs(yPos) < 100) {
                        card.style.transform = `translateY(${yPos}px)`;
                    }
                });
            });
        }

        // Réinitialiser les effets au redimensionnement
        window.addEventListener('resize', function() {
            if (window.innerWidth <= 768) {
                // Supprimer les transformations parallaxe sur mobile
                document.querySelectorAll('.value-card').forEach(card => {
                    card.style.transform = '';
                });
            }
        });

        // Gérer la fermeture des modals avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.querySelector('.file-preview-modal');
                if (modal) {
                    modal.remove();
                }
            }
        });
    </script>
</body>
</html>