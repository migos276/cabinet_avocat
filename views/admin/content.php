<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contenu du site - Administration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            color: #1f2937;
        }

        .admin-layout {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            background: linear-gradient(135deg, #1f2937, #111827);
            color: white;
            padding: 2rem 0;
        }

        .sidebar-header {
            text-align: center;
            padding: 0 1rem 2rem;
            border-bottom: 1px solid #374151;
            margin-bottom: 2rem;
        }

        .sidebar-header h2 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-header p {
            font-size: 0.8rem;
            color: #9ca3af;
        }

        .sidebar-nav {
            list-style: none;
        }

        .sidebar-nav a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(59, 130, 246, 0.1);
            border-left-color: #3b82f6;
        }

        .sidebar-nav i {
            margin-right: 0.75rem;
            width: 20px;
        }

        .main-content {
            padding: 2rem;
            max-height: 100vh;
            overflow-y: auto;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 2rem;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .breadcrumb {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .tabs {
            display: flex;
            background: white;
            border-radius: 15px 15px 0 0;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 0;
            overflow-x: auto;
        }

        .tab-button {
            flex: 1;
            min-width: 150px;
            padding: 1rem 1.5rem;
            border: none;
            background: transparent;
            cursor: pointer;
            font-weight: 600;
            color: #6b7280;
            transition: all 0.3s ease;
            border-radius: 15px 15px 0 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            white-space: nowrap;
        }

        .tab-button.active {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .tab-button:not(.active):hover {
            background: #f3f4f6;
            color: #374151;
        }

        .tab-content {
            display: none;
            background: white;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            padding: 2rem;
        }

        .tab-content.active {
            display: block;
        }

        .form-grid {
            display: grid;
            gap: 1.5rem;
        }

        .form-section {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid #3b82f6;
            position: relative;
        }

        .section-title {
            font-size: 1.2rem;
            color: #1f2937;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .section-title-left {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-mini {
            padding: 0.4rem 0.6rem;
            font-size: 0.8rem;
            border-radius: 6px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group-inline {
            display: flex;
            gap: 1rem;
            align-items: end;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-control.error {
            border-color: #ef4444;
        }

        .form-control-lg {
            padding: 1rem 1.25rem;
            font-size: 1.1rem;
        }

        .textarea-lg {
            min-height: 200px;
            resize: vertical;
        }

        .color-picker-group {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .color-preview {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #e5e7eb;
            color: #374151;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .alert-success, .alert-error {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            border: 1px solid;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-color: #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-color: #fecaca;
        }

        .services-grid, .team-grid, .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .service-card, .team-card, .news-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 2px solid #f3f4f6;
            position: relative;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .service-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .team-image, .news-image {
            width: 100%;
            height: 200px;
            border-radius: 10px;
            object-fit: cover;
            margin-bottom: 1rem;
        }

        .image-preview {
            width: 100%;
            height: 200px;
            border-radius: 10px;
            object-fit: cover;
            margin-bottom: 1rem;
            display: none;
        }

        .image-preview.show {
            display: block;
        }

        .rich-editor {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }

        .editor-toolbar {
            background: #f8fafc;
            padding: 0.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .editor-btn {
            padding: 0.5rem;
            border: none;
            background: transparent;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
            font-size: 0.9rem;
        }

        .editor-btn:hover {
            background: #e5e7eb;
        }

        .editor-btn.active {
            background: #3b82f6;
            color: white;
        }

        .editor-content {
            min-height: 300px;
            padding: 1rem;
            outline: none;
            line-height: 1.6;
        }

        .editor-content.error {
            border: 2px solid #ef4444;
        }

        .editor-content:focus {
            background: #f9fafb;
        }

        .sortable-item {
            cursor: move;
        }

        .sortable-item:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .drag-handle {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #f3f4f6;
            border-radius: 8px;
            padding: 0.5rem;
            cursor: move;
            color: #6b7280;
            font-size: 1.2rem;
        }

        .content-manager {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #f0f9ff;
            border-radius: 15px;
            border: 1px solid #bae6fd;
        }

        .content-item {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #e5e7eb;
        }

        .content-path {
            font-family: monospace;
            background: #f3f4f6;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        .add-service-form, .add-team-form, .add-news-form {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6b7280;
        }

        .order-indicator {
            position: absolute;
            top: -10px;
            left: -10px;
            background: #3b82f6;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.8rem;
        }

        .required::after {
            content: '*';
            color: #ef4444;
            margin-left: 0.25rem;
        }

        .optional::after {
            content: '(optionnel)';
            color: #6b7280;
            font-size: 0.8rem;
            margin-left: 0.25rem;
        }

        @media (max-width: 768px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                display: none;
            }
            
            .main-content {
                padding: 1rem;
            }
            
            .tabs {
                flex-direction: column;
            }
            
            .tab-button {
                border-radius: 0;
                min-width: auto;
            }
            
            .services-grid, .team-grid, .news-grid {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><?php echo h(SITE_NAME); ?></h2>
                <p>Administration</p>
            </div>
            <ul class="sidebar-nav">
                <li><a href="/admin/dashboard">
                    <i class="fas fa-chart-line"></i>
                    Tableau de bord
                </a></li>
                <li><a href="/admin/content" class="active">
                    <i class="fas fa-edit"></i>
                    Contenu du site
                </a></li>
                <li><a href="/admin/contacts">
                    <i class="fas fa-envelope"></i>
                    Messages
                </a></li>
                <li><a href="/admin/settings">
                    <i class="fas fa-cog"></i>
                    Paramètres
                </a></li>
                <li><a href="/" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    Voir le site
                </a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <h1>Contenu du site</h1>
                <div class="breadcrumb">Administration / Contenu du site</div>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo h($success); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo h($error); ?>
                </div>
            <?php endif; ?>

            <div class="tabs">
                <button class="tab-button active" onclick="openTab(event, 'general')">
                    <i class="fas fa-home"></i>
                    Contenu général
                </button>
                <button class="tab-button" onclick="openTab(event, 'content-manager')">
                    <i class="fas fa-cogs"></i>
                    Gestionnaire
                </button>
                <button class="tab-button" onclick="openTab(event, 'services')">
                    <i class="fas fa-gavel"></i>
                    Services
                </button>
                <button class="tab-button" onclick="openTab(event, 'team')">
                    <i class="fas fa-users"></i>
                    Équipe
                </button>
                <button class="tab-button" onclick="openTab(event, 'news')">
                    <i class="fas fa-newspaper"></i>
                    Actualités
                </button>
            </div>

            <!-- Contenu général -->
            <div id="general" class="tab-content active">
                <form method="POST" id="general-content-form">
                    <input type="hidden" name="action" value="update_content">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="form-grid">
                        <div class="form-section">
                            <h3 class="section-title">
                                <span class="section-title-left">
                                    <i class="fas fa-star"></i>
                                    Section Hero (Accueil)
                                </span>
                            </h3>
                            <div class="form-group">
                                <label class="form-label required">Titre principal</label>
                                <input type="text" name="content[hero][title]" class="form-control form-control-lg" 
                                       value="<?php echo h($content['hero']['title'] ?? 'Excellence Juridique à Votre Service'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Sous-titre</label>
                                <textarea name="content[hero][subtitle]" class="form-control" rows="3" required><?php echo h($content['hero']['subtitle'] ?? 'Depuis plus de 20 ans, nous accompagnons nos clients avec expertise, intégrité et dévouement dans tous leurs défis juridiques les plus complexes.'); ?></textarea>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3 class="section-title">
                                <span class="section-title-left">
                                    <i class="fas fa-info-circle"></i>
                                    Section À propos
                                </span>
                            </h3>
                            <div class="form-group">
                                <label class="form-label required">Titre</label>
                                <input type="text" name="content[about][title]" class="form-control" 
                                       value="<?php echo h($content['about']['title'] ?? 'Votre Réussite, Notre Mission'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Sous-titre</label>
                                <textarea name="content[about][subtitle]" class="form-control" rows="3" required><?php echo h($content['about']['subtitle'] ?? 'Fort d\'une expérience reconnue et d\'une approche personnalisée, notre cabinet vous offre un accompagnement juridique d\'excellence adapté à vos besoins spécifiques.'); ?></textarea>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3 class="section-title">
                                <span class="section-title-left">
                                    <i class="fas fa-gavel"></i>
                                    Section Services
                                </span>
                            </h3>
                            <div class="form-group">
                                <label class="form-label required">Titre</label>
                                <input type="text" name="content[services][title]" class="form-control" 
                                       value="<?php echo h($content['services']['title'] ?? 'Domaines d\'Expertise'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Sous-titre</label>
                                <textarea name="content[services][subtitle]" class="form-control" rows="3" required><?php echo h($content['services']['subtitle'] ?? 'Une expertise reconnue dans des domaines juridiques essentiels pour répondre à tous vos besoins'); ?></textarea>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3 class="section-title">
                                <span class="section-title-left">
                                    <i class="fas fa-users"></i>
                                    Section Équipe
                                </span>
                            </h3>
                            <div class="form-group">
                                <label class="form-label required">Titre</label>
                                <input type="text" name="content[team][title]" class="form-control" 
                                       value="<?php echo h($content['team']['title'] ?? 'Des Experts à Vos Côtés'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Sous-titre</label>
                                <textarea name="content[team][subtitle]" class="form-control" rows="3" required><?php echo h($content['team']['subtitle'] ?? 'Des avocats expérimentés et passionnés, reconnus pour leur expertise et leur engagement'); ?></textarea>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3 class="section-title">
                                <span class="section-title-left">
                                    <i class="fas fa-newspaper"></i>
                                    Section Actualités
                                </span>
                            </h3>
                            <div class="form-group">
                                <label class="form-label required">Titre</label>
                                <input type="text" name="content[news][title]" class="form-control" 
                                       value="<?php echo h($content['news']['title'] ?? 'Nos Dernières Actualités'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Sous-titre</label>
                                <textarea name="content[news][subtitle]" class="form-control" rows="3" required><?php echo h($content['news']['subtitle'] ?? 'Restez informé des dernières nouvelles et mises à jour juridiques de notre cabinet.'); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div style="text-align: center; margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Sauvegarder le contenu général
                        </button>
                    </div>
                </form>
            </div>

            <!-- Gestionnaire de contenu avancé -->
            <div id="content-manager" class="tab-content">
                <div class="content-manager">
                    <h3 style="margin-bottom: 1rem;">
                        <i class="fas fa-cogs"></i>
                        Gestionnaire de contenu avancé
                    </h3>
                    <p style="color: #6b7280; margin-bottom: 1.5rem;">
                        Gérez tous les éléments de contenu de votre site. Vous pouvez ajouter de nouvelles sections, modifier ou supprimer du contenu existant.
                    </p>

                    <!-- Formulaire d'ajout de contenu -->
                    <div style="background: white; padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem;">
                        <h4 style="margin-bottom: 1rem;">
                            <i class="fas fa-plus"></i>
                            Ajouter du nouveau contenu
                        </h4>
                        <form method="POST" id="add-content-form">
                            <input type="hidden" name="action" value="add_content_section">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label required">Section</label>
                                    <select name="new_section" class="form-control" onchange="toggleCustomSection(this)" required>
                                        <option value="">Sélectionner une section</option>
                                        <option value="hero">Hero (Accueil)</option>
                                        <option value="about">À propos</option>
                                        <option value="services">Services</option>
                                        <option value="team">Équipe</option>
                                        <option value="news">Actualités</option>
                                        <option value="contact">Contact</option>
                                        <option value="footer">Footer</option>
                                        <option value="custom">Nouvelle section personnalisée</option>
                                    </select>
                                </div>
                                <div class="form-group" id="customSectionGroup" style="display: none;">
                                    <label class="form-label required">Nom de la section personnalisée</label>
                                    <input type="text" id="customSectionInput" class="form-control" placeholder="ex: testimonials, features">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label required">Clé</label>
                                    <input type="text" name="new_key" class="form-control" placeholder="ex: title, subtitle, description" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required">Valeur</label>
                                    <textarea name="new_value" class="form-control" rows="3" placeholder="Contenu à afficher" required></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus"></i>
                                Ajouter ce contenu
                            </button>
                        </form>
                    </div>

                    <!-- Liste du contenu existant -->
                    <div style="background: white; padding: 1.5rem; border-radius: 10px;">
                        <h4 style="margin-bottom: 1rem;">
                            <i class="fas fa-list"></i>
                            Contenu existant
                        </h4>
                        
                        <?php if (!empty($content)): ?>
                            <?php foreach ($content as $section => $keys): ?>
                                <div style="margin-bottom: 1.5rem;">
                                    <h5 style="color: #3b82f6; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas fa-folder"></i>
                                        <?php echo h(ucfirst($section)); ?>
                                    </h5>
                                    <?php foreach ($keys as $key => $value): ?>
                                        <div class="content-item">
                                            <div>
                                                <div class="content-path"><?php echo h($section); ?>.<?php echo h($key); ?></div>
                                                <div style="margin-top: 0.5rem; color: #6b7280; font-size: 0.9rem;">
                                                    <?php echo strlen($value) > 100 ? substr(h($value), 0, 100) . '...' : h($value); ?>
                                                </div>
                                            </div>
                                            <div style="display: flex; gap: 0.5rem;">
                                                <button type="button" class="btn btn-mini btn-outline" onclick="editContent('<?php echo h($section); ?>', '<?php echo h($key); ?>', '<?php echo h($value, ENT_QUOTES); ?>')">
                                                    <i class="fas fa-edit"></i>
                                                    Modifier
                                                </button>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer ce contenu ?');">
                                                    <input type="hidden" name="action" value="delete_content">
                                                    <input type="hidden" name="content_section" value="<?php echo h($section); ?>">
                                                    <input type="hidden" name="content_key" value="<?php echo h($key); ?>">
                                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                    <button type="submit" class="btn btn-mini btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color: #6b7280; text-align: center; padding: 2rem;">
                                Aucun contenu trouvé. Commencez par ajouter du nouveau contenu ci-dessus.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Services -->
            <div id="services" class="tab-content">
                <!-- Formulaire d'ajout de service -->
                <div class="add-service-form">
                    <h3 style="margin-bottom: 1rem;">
                        <i class="fas fa-plus"></i>
                        Ajouter un nouveau service
                    </h3>
                    <form method="POST" id="add-service-form">
                        <input type="hidden" name="action" value="add_service">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Titre du service</label>
                                <input type="text" name="title" class="form-control" required placeholder="ex: Droit des Affaires">
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Icône (Font Awesome)</label>
                                <input type="text" name="icon" class="form-control" value="fas fa-gavel" placeholder="fas fa-gavel" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Couleur</label>
                                <div class="color-picker-group">
                                    <input type="color" name="color" class="form-control" value="#3b82f6" onchange="updateAddColorPreview(this)" required>
                                    <div class="color-preview" id="add_color_preview" style="background: #3b82f6;">
                                        <i class="fas fa-gavel"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Description courte</label>
                                <textarea name="description" class="form-control" rows="3" required placeholder="Description qui apparaîtra sur la page d'accueil"></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label optional">Contenu détaillé (pour la page dédiée)</label>
                            <div class="rich-editor">
                                <div class="editor-toolbar">
                                    <button type="button" class="editor-btn" onclick="formatText('bold')" title="Gras">
                                        <i class="fas fa-bold"></i>
                                    </button>
                                    <button type="button" class="editor-btn" onclick="formatText('italic')" title="Italique">
                                        <i class="fas fa-italic"></i>
                                    </button>
                                    <button type="button" class="editor-btn" onclick="insertList('ul')" title="Liste à puces">
                                        <i class="fas fa-list-ul"></i>
                                    </button>
                                    <button type="button" class="editor-btn" onclick="insertHeading()" title="Titre">
                                        <i class="fas fa-heading"></i>
                                    </button>
                                </div>
                                <div class="editor-content" contenteditable="true" id="newServiceContent">
                                    <h3>Notre approche</h3>
                                    <p>Nous privilégions une approche personnalisée et sur-mesure pour chaque client.</p>
                                    <ul>
                                        <li>Analyse approfondie de votre situation</li>
                                        <li>Conseil juridique adapté à vos besoins</li>
                                        <li>Accompagnement tout au long de la procédure</li>
                                    </ul>
                                </div>
                            </div>
                            <input type="hidden" name="detailed_content" id="new_detailed_content">
                        </div>
                        
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus"></i>
                            Ajouter le service
                        </button>
                    </form>
                </div>

                <!-- Services existants -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3>Services existants</h3>
                    <div class="btn btn-outline" onclick="toggleReorderMode()">
                        <i class="fas fa-sort"></i>
                        Mode réorganisation
                    </div>
                </div>

                <div class="services-grid" id="servicesGrid">
                    <?php foreach ($services as $index => $service): ?>
                        <div class="service-card sortable-item" data-id="<?php echo h($service['id']); ?>">
                            <div class="order-indicator"><?php echo $index + 1; ?></div>
                            <div class="drag-handle" style="display: none;">
                                <i class="fas fa-grip-vertical"></i>
                            </div>
                            
                            <form method="POST" id="service-form-<?php echo h($service['id']); ?>">
                                <input type="hidden" name="action" value="update_service">
                                <input type="hidden" name="service_id" value="<?php echo h($service['id']); ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                
                                <div class="card-header">
                                    <div class="service-icon" style="background: <?php echo h($service['color']); ?>;">
                                        <i class="<?php echo h($service['icon']); ?>"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <h4><?php echo h($service['title']); ?></h4>
                                        <small style="color: #6b7280;">ID: <?php echo h($service['id']); ?></small>
                                    </div>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer ce service ?');">
                                        <input type="hidden" name="action" value="delete_service">
                                        <input type="hidden" name="service_id" value="<?php echo h($service['id']); ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <button type="submit" class="btn btn-mini btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <div class="form-group">
                                    <label class="form-label required">Titre du service</label>
                                    <input type="text" name="title" class="form-control" value="<?php echo h($service['title']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label required">Description courte</label>
                                    <textarea name="description" class="form-control" rows="3" required><?php echo h($service['description']); ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="form-label optional">Contenu détaillé</label>
                                    <div class="rich-editor">
                                        <div class="editor-toolbar">
                                            <button type="button" class="editor-btn" onclick="formatText('bold')" title="Gras">
                                                <i class="fas fa-bold"></i>
                                            </button>
                                            <button type="button" class="editor-btn" onclick="formatText('italic')" title="Italique">
                                                <i class="fas fa-italic"></i>
                                            </button>
                                            <button type="button" class="editor-btn" onclick="insertList('ul')" title="Liste à puces">
                                                <i class="fas fa-list-ul"></i>
                                            </button>
                                            <button type="button" class="editor-btn" onclick="insertHeading()" title="Titre">
                                                <i class="fas fa-heading"></i>
                                            </button>
                                        </div>
                                        <div class="editor-content" contenteditable="true" data-service-id="<?php echo h($service['id']); ?>">
                                            <?php echo !empty($service['detailed_content']) ? $service['detailed_content'] : '<p>Contenu détaillé à compléter...</p>'; ?>
                                        </div>
                                    </div>
                                    <input type="hidden" name="detailed_content" id="detailed_content_<?php echo h($service['id']); ?>">
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label required">Icône</label>
                                        <input type="text" name="icon" class="form-control" value="<?php echo h($service['icon']); ?>" placeholder="fas fa-gavel" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label required">Couleur</label>
                                        <div class="color-picker-group">
                                            <input type="color" name="color" class="form-control" value="<?php echo h($service['color']); ?>" onchange="updateColorPreview(this, <?php echo h($service['id']); ?>)" required>
                                            <div class="color-preview" id="color_preview_<?php echo h($service['id']); ?>" style="background: <?php echo h($service['color']); ?>;">
                                                <i class="<?php echo h($service['icon']); ?>"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i>
                                    Sauvegarder
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Équipe -->
            <div id="team" class="tab-content">
                <!-- Formulaire d'ajout de membre -->
                <div class="add-team-form">
                    <h3 style="margin-bottom: 1rem;">
                        <i class="fas fa-user-plus"></i>
                        Ajouter un nouveau membre
                    </h3>
                    <form method="POST" enctype="multipart/form-data" id="add-team-form">
                        <input type="hidden" name="action" value="add_team">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Nom</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Poste</label>
                                <input type="text" name="position" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Description</label>
                            <textarea name="description" class="form-control textarea-lg" required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Image du membre (JPG, PNG, max 5MB)</label>
                            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png" required onchange="previewImage(this, 'new_team_preview')">
                            <img id="new_team_preview" class="image-preview" alt="Aperçu de l'image">
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus"></i>
                            Ajouter le membre
                        </button>
                    </form>
                </div>

                <!-- Équipe existante -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3>Équipe existante</h3>
                    <div class="btn btn-outline" onclick="toggleTeamReorderMode()">
                        <i class="fas fa-sort"></i>
                        Mode réorganisation
                    </div>
                </div>

                <div class="team-grid" id="teamGrid">
                    <?php foreach ($team as $index => $member): ?>
                        <div class="team-card sortable-item" data-id="<?php echo h($member['id']); ?>">
                            <div class="order-indicator"><?php echo $index + 1; ?></div>
                            <div class="drag-handle" style="display: none;">
                                <i class="fas fa-grip-vertical"></i>
                            </div>
                            
                            <form method="POST" enctype="multipart/form-data" id="team-form-<?php echo h($member['id']); ?>">
                                <input type="hidden" name="action" value="update_team">
                                <input type="hidden" name="team_id" value="<?php echo h($member['id']); ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                    <h4><?php echo h($member['name']); ?></h4>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer ce membre ?');">
                                        <input type="hidden" name="action" value="delete_team">
                                        <input type="hidden" name="team_id" value="<?php echo h($member['id']); ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <button type="submit" class="btn btn-mini btn-danger">
                                            <i class="fas fa-trash"></i>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>

                                <img src="<?php echo h($member['image_path']); ?>" alt="<?php echo h($member['name']); ?>" class="team-image">
                                <img id="preview_<?php echo h($member['id']); ?>" class="image-preview" alt="Aperçu de la nouvelle image">

                                <div class="form-group">
                                    <label class="form-label required">Nom</label>
                                    <input type="text" name="name" class="form-control" value="<?php echo h($member['name']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label required">Poste</label>
                                    <input type="text" name="position" class="form-control" value="<?php echo h($member['position']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label required">Description</label>
                                    <textarea name="description" class="form-control textarea-lg" required><?php echo h($member['description']); ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="form-label optional">Nouvelle image (JPG, PNG, max 5MB)</label>
                                    <input type="file" name="image" class="form-control" accept="image/jpeg,image/png" onchange="previewImage(this, 'preview_<?php echo h($member['id']); ?>')">
                                </div>

                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i>
                                    Sauvegarder
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Actualités -->
            <div id="news" class="tab-content">
                <!-- Formulaire d'ajout d'actualité -->
                <div class="add-news-form">
                    <h3 style="margin-bottom: 1rem;">
                        <i class="fas fa-newspaper"></i>
                        Ajouter une nouvelle actualité
                    </h3>
                    <form method="POST" enctype="multipart/form-data" id="add-news-form">
                        <input type="hidden" name="action" value="add_news">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Titre</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Date de publication</label>
                                <input type="datetime-local" name="publish_date" class="form-control" required value="<?php echo date('Y-m-d\TH:i'); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Contenu</label>
                            <div class="rich-editor">
                                <div class="editor-toolbar">
                                    <button type="button" class="editor-btn" onclick="formatText('bold')" title="Gras">
                                        <i class="fas fa-bold"></i>
                                    </button>
                                    <button type="button" class="editor-btn" onclick="formatText('italic')" title="Italique">
                                        <i class="fas fa-italic"></i>
                                    </button>
                                    <button type="button" class="editor-btn" onclick="insertList('ul')" title="Liste à puces">
                                        <i class="fas fa-list-ul"></i>
                                    </button>
                                    <button type="button" class="editor-btn" onclick="insertHeading()" title="Titre">
                                        <i class="fas fa-heading"></i>
                                    </button>
                                </div>
                                <div class="editor-content" contenteditable="true" id="newNewsContent">
                                    <p>Contenu de l'actualité à compléter...</p>
                                </div>
                            </div>
                            <input type="hidden" name="content" id="new_news_content">
                        </div>
                        <div class="form-group">
                            <label class="form-label optional">Image (JPG, PNG, max 5MB)</label>
                            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png" onchange="previewImage(this, 'new_news_preview')">
                            <img id="new_news_preview" class="image-preview" alt="Aperçu de l'image">
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus"></i>
                            Ajouter l'actualité
                        </button>
                    </form>
                </div>

                <!-- Actualités existantes -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3>Actualités existantes</h3>
                    <div class="btn btn-outline" onclick="toggleNewsReorderMode()">
                        <i class="fas fa-sort"></i>
                        Mode réorganisation
                    </div>
                </div>

                <div class="news-grid" id="newsGrid">
                    <?php foreach ($news as $index => $article): ?>
                        <div class="news-card sortable-item" data-id="<?php echo h($article['id']); ?>">
                            <div class="order-indicator"><?php echo $index + 1; ?></div>
                            <div class="drag-handle" style="display: none;">
                                <i class="fas fa-grip-vertical"></i>
                            </div>
                            
                            <form method="POST" enctype="multipart/form-data" id="news-form-<?php echo h($article['id']); ?>">
                                <input type="hidden" name="action" value="update_news">
                                <input type="hidden" name="news_id" value="<?php echo h($article['id']); ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                    <h4><?php echo h($article['title']); ?></h4>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer cette actualité ?');">
                                        <input type="hidden" name="action" value="delete_news">
                                        <input type="hidden" name="news_id" value="<?php echo h($article['id']); ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <button type="submit" class="btn btn-mini btn-danger">
                                            <i class="fas fa-trash"></i>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>

                                <?php if (!empty($article['image_path'])): ?>
                                    <img src="<?php echo h($article['image_path']); ?>" alt="<?php echo h($article['title']); ?>" class="news-image">
                                <?php endif; ?>
                                <img id="preview_<?php echo h($article['id']); ?>" class="image-preview" alt="Aperçu de la nouvelle image">

                                <div class="form-group">
                                    <label class="form-label required">Titre</label>
                                    <input type="text" name="title" class="form-control" value="<?php echo h($article['title']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label required">Date de publication</label>
                                    <input type="datetime-local" name="publish_date" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($article['publish_date'])); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label required">Contenu</label>
                                    <div class="rich-editor">
                                        <div class="editor-toolbar">
                                            <button type="button" class="editor-btn" onclick="formatText('bold')" title="Gras">
                                                <i class="fas fa-bold"></i>
                                            </button>
                                            <button type="button" class="editor-btn" onclick="formatText('italic')" title="Italique">
                                                <i class="fas fa-italic"></i>
                                            </button>
                                            <button type="button" class="editor-btn" onclick="insertList('ul')" title="Liste à puces">
                                                <i class="fas fa-list-ul"></i>
                                            </button>
                                            <button type="button" class="editor-btn" onclick="insertHeading()" title="Titre">
                                                <i class="fas fa-heading"></i>
                                            </button>
                                        </div>
                                        <div class="editor-content" contenteditable="true" data-news-id="<?php echo h($article['id']); ?>">
                                            <?php echo !empty($article['content']) ? $article['content'] : '<p>Contenu à compléter...</p>'; ?>
                                        </div>
                                    </div>
                                    <input type="hidden" name="content" id="news_content_<?php echo h($article['id']); ?>">
                                </div>

                                <div class="form-group">
                                    <label class="form-label optional">Nouvelle image (JPG, PNG, max 5MB)</label>
                                    <input type="file" name="image" class="form-control" accept="image/jpeg,image/png" onchange="previewImage(this, 'preview_<?php echo h($article['id']); ?>')">
                                </div>

                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i>
                                    Sauvegarder
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal d'édition de contenu -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Modifier le contenu</h3>
                <button class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <form method="POST" id="edit-content-form">
                <input type="hidden" name="action" value="update_content">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" id="editSection" name="content_section">
                <input type="hidden" id="editKey" name="content_key">
                
                <div class="form-group">
                    <label class="form-label">Section</label>
                    <input type="text" id="editSectionDisplay" class="form-control" disabled>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Clé</label>
                    <input type="text" id="editKeyDisplay" class="form-control" disabled>
                </div>
                
                <div class="form-group">
                    <label class="form-label required">Valeur</label>
                    <textarea id="editValue" name="new_value" class="form-control textarea-lg" required></textarea>
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" class="btn btn-outline" onclick="closeEditModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Sauvegarder
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <script>
        // Variables globales
        let reorderMode = false;
        let teamReorderMode = false;
        let newsReorderMode = false;
        let servicesSortable = null;
        let teamSortable = null;
        let newsSortable = null;

        // Navigation par onglets
        function openTab(evt, tabName) {
            const tabcontent = document.getElementsByClassName("tab-content");
            for (let i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.remove("active");
            }
            const tablinks = document.getElementsByClassName("tab-button");
            for (let i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("active");
            }
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }

        // Gestionnaire de section personnalisée
        function toggleCustomSection(select) {
            const customGroup = document.getElementById('customSectionGroup');
            const customInput = document.getElementById('customSectionInput');
            
            if (select.value === 'custom') {
                customGroup.style.display = 'block';
                customInput.required = true;
                customInput.addEventListener('input', function() {
                    select.name = 'temp_section';
                    if (!document.querySelector('input[name="new_section"]')) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'new_section';
                        select.parentNode.appendChild(hiddenInput);
                    }
                    document.querySelector('input[name="new_section"]').value = this.value;
                });
            } else {
                customGroup.style.display = 'none';
                customInput.required = false;
                select.name = 'new_section';
                const hiddenInput = document.querySelector('input[name="new_section"]');
                if (hiddenInput && hiddenInput !== select) {
                    hiddenInput.remove();
                }
            }
        }

        // Modal d'édition
        function editContent(section, key, value) {
            document.getElementById('editSection').value = section;
            document.getElementById('editKey').value = key;
            document.getElementById('editSectionDisplay').value = section;
            document.getElementById('editKeyDisplay').value = key;
            document.getElementById('editValue').value = value;
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Gestion des couleurs
        function updateColorPreview(input, id) {
            const preview = document.getElementById('color_preview_' + id);
            preview.style.background = input.value;
            const iconInput = input.closest('form').querySelector('input[name="icon"]');
            if (iconInput) {
                preview.querySelector('i').className = iconInput.value;
            }
        }

        function updateAddColorPreview(input) {
            const preview = document.getElementById('add_color_preview');
            const iconInput = input.closest('form').querySelector('input[name="icon"]');
            preview.style.background = input.value;
            if (iconInput) {
                preview.querySelector('i').className = iconInput.value;
            }
        }

        // Éditeur riche
        function formatText(command) {
            document.execCommand(command, false, null);
            document.activeElement.focus();
        }

        function insertList(type) {
            if (type === 'ul') {
                document.execCommand('insertUnorderedList', false, null);
            } else {
                document.execCommand('insertOrderedList', false, null);
            }
            document.activeElement.focus();
        }

        function insertHeading() {
            const selection = window.getSelection().toString();
            if (selection) {
                document.execCommand('formatBlock', false, 'h3');
            } else {
                document.execCommand('insertHTML', false, '<h3>Nouveau titre</h3><p></p>');
            }
            document.activeElement.focus();
        }

        function saveServiceContent(serviceId) {
            const editorContent = document.querySelector(`[data-service-id="${serviceId}"]`);
            const hiddenInput = document.getElementById(`detailed_content_${serviceId}`);
            if (editorContent && hiddenInput) {
                hiddenInput.value = editorContent.innerHTML.trim();
            }
        }

        function saveNewServiceContent() {
            const editorContent = document.getElementById('newServiceContent');
            const hiddenInput = document.getElementById('new_detailed_content');
            if (editorContent && hiddenInput) {
                hiddenInput.value = editorContent.innerHTML.trim();
            }
        }

        function saveNewsContent(newsId) {
            const editorContent = document.querySelector(`[data-news-id="${newsId}"]`);
            const hiddenInput = document.getElementById(`news_content_${newsId}`);
            if (editorContent && hiddenInput) {
                hiddenInput.value = editorContent.innerHTML.trim();
            }
        }

        function saveNewNewsContent() {
            const editorContent = document.getElementById('newNewsContent');
            const hiddenInput = document.getElementById('new_news_content');
            if (editorContent && hiddenInput) {
                hiddenInput.value = editorContent.innerHTML.trim();
            }
        }

        // Prévisualisation d'images avec validation
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (!['image/jpeg', 'image/png'].includes(file.type)) {
                    alert('Erreur : Seuls les fichiers JPG et PNG sont acceptés.');
                    input.value = '';
                    preview.classList.remove('show');
                    return;
                }
                if (file.size > maxSize) {
                    alert('Erreur : Le fichier est trop volumineux. Taille maximale : 5MB.');
                    input.value = '';
                    preview.classList.remove('show');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.add('show');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.remove('show');
            }
        }

        // Mode réorganisation des services
        function toggleReorderMode() {
            reorderMode = !reorderMode;
            const grid = document.getElementById('servicesGrid');
            const dragHandles = document.querySelectorAll('#servicesGrid .drag-handle');
            const orderIndicators = document.querySelectorAll('#servicesGrid .order-indicator');
            
            if (reorderMode) {
                grid.style.opacity = '0.8';
                dragHandles.forEach(handle => handle.style.display = 'block');
                orderIndicators.forEach(indicator => indicator.style.display = 'flex');
                
                servicesSortable = new Sortable(grid, {
                    animation: 150,
                    handle: '.drag-handle',
                    ghostClass: 'sortable-ghost',
                    onEnd: function(evt) {
                        updateServicesOrder();
                    }
                });
                
                document.querySelector('[onclick="toggleReorderMode()"]').innerHTML = '<i class="fas fa-check"></i> Terminer la réorganisation';
            } else {
                grid.style.opacity = '1';
                dragHandles.forEach(handle => handle.style.display = 'none');
                
                if (servicesSortable) {
                    servicesSortable.destroy();
                    servicesSortable = null;
                }
                
                document.querySelector('[onclick="toggleReorderMode()"]').innerHTML = '<i class="fas fa-sort"></i> Mode réorganisation';
            }
        }

        // Mode réorganisation de l'équipe
        function toggleTeamReorderMode() {
            teamReorderMode = !teamReorderMode;
            const grid = document.getElementById('teamGrid');
            const dragHandles = document.querySelectorAll('#teamGrid .drag-handle');
            const orderIndicators = document.querySelectorAll('#teamGrid .order-indicator');
            
            if (teamReorderMode) {
                grid.style.opacity = '0.8';
                dragHandles.forEach(handle => handle.style.display = 'block');
                orderIndicators.forEach(indicator => indicator.style.display = 'flex');
                
                teamSortable = new Sortable(grid, {
                    animation: 150,
                    handle: '.drag-handle',
                    ghostClass: 'sortable-ghost',
                    onEnd: function(evt) {
                        updateTeamOrder();
                    }
                });
                
                document.querySelector('[onclick="toggleTeamReorderMode()"]').innerHTML = '<i class="fas fa-check"></i> Terminer la réorganisation';
            } else {
                grid.style.opacity = '1';
                dragHandles.forEach(handle => handle.style.display = 'none');
                
                if (teamSortable) {
                    teamSortable.destroy();
                    teamSortable = null;
                }
                
                document.querySelector('[onclick="toggleTeamReorderMode()"]').innerHTML = '<i class="fas fa-sort"></i> Mode réorganisation';
            }
        }

        // Mode réorganisation des actualités
        function toggleNewsReorderMode() {
            newsReorderMode = !newsReorderMode;
            const grid = document.getElementById('newsGrid');
            const dragHandles = document.querySelectorAll('#newsGrid .drag-handle');
            const orderIndicators = document.querySelectorAll('#newsGrid .order-indicator');
            
            if (newsReorderMode) {
                grid.style.opacity = '0.8';
                dragHandles.forEach(handle => handle.style.display = 'block');
                orderIndicators.forEach(indicator => indicator.style.display = 'flex');
                
                newsSortable = new Sortable(grid, {
                    animation: 150,
                    handle: '.drag-handle',
                    ghostClass: 'sortable-ghost',
                    onEnd: function(evt) {
                        updateNewsOrder();
                    }
                });
                
                document.querySelector('[onclick="toggleNewsReorderMode()"]').innerHTML = '<i class="fas fa-check"></i> Terminer la réorganisation';
            } else {
                grid.style.opacity = '1';
                dragHandles.forEach(handle => handle.style.display = 'none');
                
                if (newsSortable) {
                    newsSortable.destroy();
                    newsSortable = null;
                }
                
                document.querySelector('[onclick="toggleNewsReorderMode()"]').innerHTML = '<i class="fas fa-sort"></i> Mode réorganisation';
            }
        }

        // Mise à jour de l'ordre des services
        function updateServicesOrder() {
            const items = document.querySelectorAll('#servicesGrid .sortable-item');
            const orders = {};
            
            items.forEach((item, index) => {
                const id = item.dataset.id;
                orders[id] = index + 1;
                
                const indicator = item.querySelector('.order-indicator');
                if (indicator) {
                    indicator.textContent = index + 1;
                }
            });
            
            const formData = new FormData();
            formData.append('action', 'reorder_services');
            formData.append('orders', JSON.stringify(orders));
            formData.append('csrf_token', '<?php echo generateCSRFToken(); ?>');
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            }).then(response => response.json()).then(data => {
                if (!data.success) {
                    alert('Erreur: ' + data.message);
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de la mise à jour de l\'ordre');
            });
        }

        // Mise à jour de l'ordre de l'équipe
        function updateTeamOrder() {
            const items = document.querySelectorAll('#teamGrid .sortable-item');
            const orders = {};
            
            items.forEach((item, index) => {
                const id = item.dataset.id;
                orders[id] = index + 1;
                
                const indicator = item.querySelector('.order-indicator');
                if (indicator) {
                    indicator.textContent = index + 1;
                }
            });
            
            const formData = new FormData();
            formData.append('action', 'reorder_team');
            formData.append('orders', JSON.stringify(orders));
            formData.append('csrf_token', '<?php echo generateCSRFToken(); ?>');
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            }).then(response => response.json()).then(data => {
                if (!data.success) {
                    alert('Erreur: ' + data.message);
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de la mise à jour de l\'ordre');
            });
        }

        // Mise à jour de l'ordre des actualités
        function updateNewsOrder() {
            const items = document.querySelectorAll('#newsGrid .sortable-item');
            const orders = {};
            
            items.forEach((item, index) => {
                const id = item.dataset.id;
                orders[id] = index + 1;
                
                const indicator = item.querySelector('.order-indicator');
                if (indicator) {
                    indicator.textContent = index + 1;
                }
            });
            
            const formData = new FormData();
            formData.append('action', 'reorder_news');
            formData.append('orders', JSON.stringify(orders));
            formData.append('csrf_token', '<?php echo generateCSRFToken(); ?>');
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            }).then(response => response.json()).then(data => {
                if (!data.success) {
                    alert('Erreur: ' + data.message);
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de la mise à jour de l\'ordre');
            });
        }

        // Auto-sauvegarde des contenus éditeur
        let autoSaveTimeout;
        function setupAutoSave() {
            document.querySelectorAll('.editor-content').forEach(editor => {
                editor.addEventListener('input', function() {
                    clearTimeout(autoSaveTimeout);
                    const serviceId = this.dataset.serviceId;
                    const newsId = this.dataset.newsId;
                    if (serviceId || newsId) {
                        autoSaveTimeout = setTimeout(() => {
                            const key = serviceId ? `service_draft_${serviceId}` : `news_draft_${newsId}`;
                            localStorage.setItem(key, this.innerHTML);
                            console.log(`Brouillon sauvegardé pour ${serviceId ? 'service' : 'actualité'} ${serviceId || newsId}`);
                        }, 2000);
                    }
                });
            });
        }

        // Validation des formulaires
        function setupFormValidation() {
            const forms = document.querySelectorAll('#general-content-form, #add-content-form, #add-service-form, #add-team-form, #add-news-form, form[id^="service-form-"], form[id^="team-form-"], form[id^="news-form-"]');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Validate required fields
                    const requiredFields = form.querySelectorAll('input[required], textarea[required]');
                    let isValid = true;
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.classList.add('error');
                            field.nextElementSibling?.classList.add('error');
                        } else {
                            field.classList.remove('error');
                            field.nextElementSibling?.classList.remove('error');
                        }
                    });

                    // Handle rich editor content
                    const editorContent = form.querySelector('.editor-content');
                    if (editorContent) {
                        const serviceId = editorContent.dataset.serviceId;
                        const newsId = editorContent.dataset.newsId;
                        const hiddenInput = form.querySelector(`input[name="${serviceId ? 'detailed_content' : 'content'}"]`);
                        if (hiddenInput) {
                            const content = editorContent.innerHTML.trim();
                            // Check if content is empty or default placeholder
                            if (!content || content === '<p>Contenu de l\'actualité à compléter...</p>' || content === '<p>Contenu détaillé à compléter...</p>') {
                                isValid = false;
                                editorContent.classList.add('error');
                                alert('Le contenu de l\'éditeur ne peut pas être vide ou contenir le texte par défaut.');
                            } else {
                                hiddenInput.value = content;
                                editorContent.classList.remove('error');
                                if (serviceId) {
                                    localStorage.removeItem(`service_draft_${serviceId}`);
                                } else if (newsId) {
                                    localStorage.removeItem(`news_draft_${newsId}`);
                                }
                            }
                        }
                    }

                    if (!isValid) {
                        alert('Veuillez remplir tous les champs requis et vérifier le contenu de l\'éditeur.');
                        return;
                    }

                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.disabled) {
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sauvegarde...';
                        submitBtn.disabled = true;

                        // Log FormData for debugging
                        const formData = new FormData(form);
                        for (let [key, value] of formData.entries()) {
                            console.log(`${key}: ${value}`);
                        }

                        fetch(window.location.href, {
                            method: 'POST',
                            body: formData
                        }).then(response => response.json()).then(data => {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Erreur: ' + data.message);
                            }
                        }).catch(error => {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                            console.error('Erreur:', error);
                            alert('Erreur lors de la sauvegarde. Veuillez vérifier votre connexion et réessayer.');
                        });
                    }
                });
            });
        }

        // Restauration des brouillons au chargement
        document.addEventListener('DOMContentLoaded', function() {
            // Restore drafts
            document.querySelectorAll('.editor-content').forEach(editor => {
                const serviceId = editor.dataset.serviceId;
                const newsId = editor.dataset.newsId;
                const key = serviceId ? `service_draft_${serviceId}` : `news_draft_${newsId}`;
                if (key) {
                    const draft = localStorage.getItem(key);
                    if (draft && confirm(`Un brouillon a été trouvé pour ${serviceId ? 'ce service' : 'cette actualité'}. Voulez-vous le restaurer ?`)) {
                        editor.innerHTML = draft;
                    }
                }
            });

            // Initialize color previews
            document.querySelectorAll('input[type="color"]').forEach(input => {
                const serviceMatch = input.getAttribute('onchange');
                if (serviceMatch && serviceMatch.includes('updateColorPreview')) {
                    const serviceId = serviceMatch.match(/\d+/);
                    if (serviceId) {
                        updateColorPreview(input, serviceId[0]);
                    }
                }
            });

            // Setup form validation
            setupFormValidation();

            // Setup auto-save
            setupAutoSave();

            // Update icon previews dynamically
            document.addEventListener('input', function(e) {
                if (e.target.name === 'icon') {
                    const form = e.target.closest('form');
                    const colorPreview = form.querySelector('.color-preview i');
                    if (colorPreview) {
                        colorPreview.className = e.target.value;
                    }
                }
            });

            console.log('Interface d\'administration avancée chargée avec succès');
        });
        // Fermeture du modal avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
            }
        });

        // Fermeture du modal en cliquant à l'extérieur
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
</body>
</html>