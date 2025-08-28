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

        .btn-outline {
            background: transparent;
            border: 2px solid #e5e7eb;
            color: #374151;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            border: 1px solid #bbf7d0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            border: 1px solid #fecaca;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
            display: none;
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
            display: none;
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
                <h2><?php echo htmlspecialchars(SITE_NAME); ?></h2>
                <p>Administration</p>
            </div>
            <ul class="sidebar-nav">
                <li><a href="/admin/dashboard"><i class="fas fa-chart-line"></i>Tableau de bord</a></li>
                <li><a href="/admin/content" class="active"><i class="fas fa-edit"></i>Contenu du site</a></li>
                <li><a href="/admin/contacts"><i class="fas fa-envelope"></i>Messages</a></li>
                <li><a href="/admin/settings"><i class="fas fa-cog"></i>Paramètres</a></li>
                <li><a href="/" target="_blank"><i class="fas fa-external-link-alt"></i>Voir le site</a></li>
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
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="tabs">
                <button class="tab-button active" onclick="openTab(event, 'general')"><i class="fas fa-home"></i>Contenu général</button>
                <button class="tab-button" onclick="openTab(event, 'content-manager')"><i class="fas fa-cogs"></i>Gestionnaire</button>
                <button class="tab-button" onclick="openTab(event, 'services')"><i class="fas fa-gavel"></i>Services</button>
                <button class="tab-button" onclick="openTab(event, 'team')"><i class="fas fa-users"></i>Équipe</button>
                <button class="tab-button" onclick="openTab(event, 'news')"><i class="fas fa-newspaper"></i>Actualités</button>
                <button class="tab-button" onclick="openTab(event, 'events')"><i class="fas fa-calendar"></i>Événements</button>
            </div>

            <!-- Contenu général -->
            <div id="general" class="tab-content active">
                <form method="POST" id="general-content-form">
                    <input type="hidden" name="action" value="update_content">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                    <div class="form-grid">
                        <?php foreach (['hero' => 'Accueil', 'about' => 'À propos', 'services' => 'Services', 'team' => 'Équipe', 'contact' => 'Contact'] as $section => $label): ?>
                            <div class="form-section">
                                <h3 class="section-title">
                                    <span class="section-title-left">
                                        <i class="fas fa-<?php echo $section === 'hero' ? 'star' : ($section === 'about' ? 'info-circle' : ($section === 'services' ? 'gavel' : ($section === 'team' ? 'users' : 'envelope'))); ?>"></i>
                                        Section <?php echo htmlspecialchars($label); ?>
                                    </span>
                                </h3>
                                <div class="form-group">
                                    <label class="form-label">Titre<span class="text-red-500">*</span></label>
                                    <input type="text" name="content[<?php echo htmlspecialchars($section); ?>][title]" class="form-control form-control-lg" value="<?php echo htmlspecialchars($content[$section]['title'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Sous-titre<span class="text-red-500">*</span></label>
                                    <textarea name="content[<?php echo htmlspecialchars($section); ?>][subtitle]" class="form-control textarea-lg" rows="3" required><?php echo htmlspecialchars($content[$section]['subtitle'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div style="text-align: center; margin-top: 2rem;">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>Sauvegarder le contenu général</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Gestionnaire de contenu avancé -->
            <div id="content-manager" class="tab-content">
                <div class="content-manager">
                    <h3 style="margin-bottom: 1rem;"><i class="fas fa-cogs"></i>Gestionnaire de contenu avancé</h3>
                    <p style="color: #6b7280; margin-bottom: 1.5rem;">
                        Gérez tous les éléments de contenu de votre site. Vous pouvez ajouter de nouvelles sections, modifier ou supprimer du contenu existant.
                    </p>

                    <div style="background: white; padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem;">
                        <h4 style="margin-bottom: 1rem;"><i class="fas fa-plus"></i>Ajouter du nouveau contenu</h4>
                        <form method="POST" id="add-content-form">
                            <input type="hidden" name="action" value="add_content_section">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Section<span class="text-red-500">*</span></label>
                                    <select name="new_section" class="form-control" onchange="toggleCustomSection(this)" required>
                                        <option value="">Sélectionner une section</option>
                                        <option value="hero">Hero (Accueil)</option>
                                        <option value="about">À propos</option>
                                        <option value="services">Services</option>
                                        <option value="team">Équipe</option>
                                        <option value="contact">Contact</option>
                                        <option value="footer">Footer</option>
                                        <option value="custom">Nouvelle section personnalisée</option>
                                    </select>
                                </div>
                                <div class="form-group" id="customSectionGroup" style="display: none;">
                                    <label class="form-label">Nom de la section personnalisée<span class="text-red-500">*</span></label>
                                    <input type="text" id="customSectionInput" class="form-control" placeholder="ex: testimonials, features">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Clé<span class="text-red-500">*</span></label>
                                    <input type="text" name="new_key" class="form-control" placeholder="ex: title, subtitle, description" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Valeur</label>
                                    <textarea name="new_value" class="form-control textarea-lg" rows="3" placeholder="Contenu à afficher"></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i>Ajouter ce contenu</button>
                        </form>
                    </div>

                    <div style="background: white; padding: 1.5rem; border-radius: 10px;">
                        <h4 style="margin-bottom: 1rem;"><i class="fas fa-list"></i>Contenu existant</h4>
                        <?php if (!empty($content)): ?>
                            <?php foreach ($content as $section => $keys): ?>
                                <div style="margin-bottom: 1.5rem;">
                                    <h5 style="color: #3b82f6; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas fa-folder"></i><?php echo ucfirst(htmlspecialchars($section)); ?>
                                    </h5>
                                    <?php foreach ($keys as $key => $value): ?>
                                        <div class="content-item">
                                            <div>
                                                <div class="content-path"><?php echo htmlspecialchars($section); ?>.<?php echo htmlspecialchars($key); ?></div>
                                                <div style="margin-top: 0.5rem; color: #6b7280; font-size: 0.9rem;">
                                                    <?php echo strlen($value) > 100 ? substr(htmlspecialchars($value), 0, 100) . '...' : htmlspecialchars($value); ?>
                                                </div>
                                            </div>
                                            <div style="display: flex; gap: 0.5rem;">
                                                <button type="button" class="btn btn-mini btn-outline" onclick="editContent('<?php echo htmlspecialchars($section); ?>', '<?php echo htmlspecialchars($key); ?>', '<?php echo htmlspecialchars($value, ENT_QUOTES); ?>')">
                                                    <i class="fas fa-edit"></i>Modifier
                                                </button>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer ce contenu ?');">
                                                    <input type="hidden" name="action" value="delete_content">
                                                    <input type="hidden" name="content_section" value="<?php echo htmlspecialchars($section); ?>">
                                                    <input type="hidden" name="content_key" value="<?php echo htmlspecialchars($key); ?>">
                                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                                    <button type="submit" class="btn btn-mini btn-danger"><i class="fas fa-trash"></i></button>
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
                <div class="add-service-form">
                    <h3 style="margin-bottom: 1rem;"><i class="fas fa-plus"></i>Ajouter un nouveau service</h3>
                    <form method="POST" id="add-service-form" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add_service">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Titre du service<span class="text-red-500">*</span></label>
                                <input type="text" name="title" class="form-control" required placeholder="ex: Droit des Affaires">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Icône (Font Awesome)<span class="text-red-500">*</span></label>
                                <input type="text" name="icon" class="form-control" value="fas fa-gavel" placeholder="fas fa-gavel" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Couleur<span class="text-red-500">*</span></label>
                                <div class="color-picker-group">
                                    <input type="color" name="color" class="form-control" value="#3b82f6" onchange="updateAddColorPreview(this)" required>
                                    <div class="color-preview" id="add_color_preview" style="background: #3b82f6;"></div>
                                </div>
                            </div>
                            <div class="form-group">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Description courte<span class="text-red-500">*</span></label>
                                <textarea name="description" class="form-control textarea-lg" rows="3" required placeholder="Description qui apparaîtra sur la page d'accueil"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Contenu détaillé<span class="text-gray-500 text-sm"> (optionnel)</span></label>
                            <div class="rich-editor">
                                <div class="editor-toolbar">
                                    <button type="button" class="editor-btn" onclick="formatText('bold')" title="Gras"><i class="fas fa-bold"></i></button>
                                    <button type="button" class="editor-btn" onclick="formatText('italic')" title="Italique"><i class="fas fa-italic"></i></button>
                                    <button type="button" class="editor-btn" onclick="insertList('ul')" title="Liste à puces"><i class="fas fa-list-ul"></i></button>
                                    <button type="button" class="editor-btn" onclick="insertHeading()" title="Titre"><i class="fas fa-heading"></i></button>
                                </div>
                                <div class="editor-content" contenteditable="true" id="newServiceContent"></div>
                            </div>
                            <input type="hidden" name="detailed_content" id="new_detailed_content">
                        </div>
                        <button type="submit" class="btn btn-success" onclick="saveNewEditorContent('service')"><i class="fas fa-plus"></i>Ajouter le service</button>
                    </form>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3>Services existants</h3>
                    <button class="btn btn-outline" onclick="toggleReorderMode('services')"><i class="fas fa-sort"></i>Mode réorganisation</button>
                </div>

                <div class="services-grid" id="servicesGrid">
                    <?php foreach ($services as $index => $service): ?>
                        <div class="service-card sortable-item" data-id="<?php echo htmlspecialchars($service['id']); ?>">
                            <div class="order-indicator"><?php echo $index + 1; ?></div>
                            <div class="drag-handle"><i class="fas fa-grip-vertical"></i></div>
                            <form method="POST" id="service-form-<?php echo htmlspecialchars($service['id']); ?>" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="update_service">
                                <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($service['id']); ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                <div class="card-header">
                                    <div class="service-icon" style="background: <?php echo htmlspecialchars($service['color']); ?>;">
                                        <i class="<?php echo htmlspecialchars($service['icon']); ?>"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <h4><?php echo htmlspecialchars($service['title']); ?></h4>
                                        <small style="color: #6b7280;">ID: <?php echo htmlspecialchars($service['id']); ?></small>
                                    </div>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer ce service ?');">
                                        <input type="hidden" name="action" value="delete_service">
                                        <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($service['id']); ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                        <button type="submit" class="btn btn-mini btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                                <?php if (!empty($service['image_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($service['image_path']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" class="team-image">
                                <?php endif; ?>
                                <img id="preview_<?php echo htmlspecialchars($service['id']); ?>" class="image-preview" alt="Aperçu de la nouvelle image">
                                <div class="form-group">
                                    <label class="form-label">Titre<span class="text-red-500">*</span></label>
                                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($service['title']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Description courte<span class="text-red-500">*</span></label>
                                    <textarea name="description" class="form-control textarea-lg" rows="3" required><?php echo htmlspecialchars($service['description']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Contenu détaillé<span class="text-gray-500 text-sm"> (optionnel)</span></label>
                                    <div class="rich-editor">
                                        <div class="editor-toolbar">
                                            <button type="button" class="editor-btn" onclick="formatText('bold')" title="Gras"><i class="fas fa-bold"></i></button>
                                            <button type="button" class="editor-btn" onclick="formatText('italic')" title="Italique"><i class="fas fa-italic"></i></button>
                                            <button type="button" class="editor-btn" onclick="insertList('ul')" title="Liste à puces"><i class="fas fa-list-ul"></i></button>
                                            <button type="button" class="editor-btn" onclick="insertHeading()" title="Titre"><i class="fas fa-heading"></i></button>
                                        </div>
                                        <div class="editor-content" contenteditable="true" data-service-id="<?php echo htmlspecialchars($service['id']); ?>">
                                            <?php echo !empty($service['detailed_content']) ? $service['detailed_content'] : '<p>Contenu détaillé à compléter...</p>'; ?>
                                        </div>
                                    </div>
                                    <input type="hidden" name="detailed_content" id="detailed_content_<?php echo htmlspecialchars($service['id']); ?>">
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Icône<span class="text-red-500">*</span></label>
                                        <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($service['icon']); ?>" required placeholder="fas fa-gavel">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Couleur<span class="text-red-500">*</span></label>
                                        <div class="color-picker-group">
                                            <input type="color" name="color" class="form-control" value="<?php echo htmlspecialchars($service['color']); ?>" onchange="updateColorPreview(this, <?php echo htmlspecialchars($service['id']); ?>)" required>
                                            <div class="color-preview" id="color_preview_<?php echo htmlspecialchars($service['id']); ?>" style="background: <?php echo htmlspecialchars($service['color']); ?>;"></div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success" onclick="saveEditorContent(<?php echo htmlspecialchars($service['id']); ?>, 'service')"><i class="fas fa-save"></i>Sauvegarder</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        <!-- Équipe -->
        <div id="team" class="tab-content">
                <div class="add-team-form">
                    <h3 style="margin-bottom: 1rem;"><i class="fas fa-user-plus"></i>Ajouter un nouveau membre</h3>
                    <form method="POST" id="add-team-form" enctype="multipart/form-data" autocomplete="off">
                        <input type="hidden" name="action" value="add_team">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Nom<span class="text-red-500">*</span></label>
                                <input type="text" name="name" class="form-control" required autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Poste<span class="text-red-500">*</span></label>
                                <input type="text" name="position" class="form-control" required autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Description<span class="text-red-500">*</span></label>
                            <textarea name="description" class="form-control textarea-lg" required autocomplete="off"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Image (JPG, PNG, max 5MB)<span class="text-gray-500 text-sm"> (optionnel)</span></label>
                            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png" onchange="previewImage(this, 'new_team_preview')">
                            <img id="new_team_preview" class="image-preview" alt="Aperçu de l'image">
                        </div>
                        <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i>Ajouter le membre</button>
                    </form>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3>Équipe existante</h3>
                    <button class="btn btn-outline" onclick="toggleReorderMode('team')"><i class="fas fa-sort"></i>Mode réorganisation</button>
                </div>

                <div class="team-grid" id="teamGrid">
                    <?php foreach ($team as $index => $member): ?>
                        <div class="team-card sortable-item" data-id="<?php echo htmlspecialchars($member['id']); ?>">
                            <div class="order-indicator"><?php echo $index + 1; ?></div>
                            <div class="drag-handle"><i class="fas fa-grip-vertical"></i></div>
                            <form method="POST" id="team-form-<?php echo htmlspecialchars($member['id']); ?>" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="update_team">
                                <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($member['id']); ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                    <h4><?php echo htmlspecialchars($member['name']); ?></h4>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer ce membre ?');">
                                        <input type="hidden" name="action" value="delete_team">
                                        <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($member['id']); ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                        <button type="submit" class="btn btn-mini btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                                <img src="<?php echo htmlspecialchars($member['image_path']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" class="team-image">
                                <img id="preview_<?php echo htmlspecialchars($member['id']); ?>" class="image-preview" alt="Aperçu de la nouvelle image">
                                <div class="form-group">
                                    <label class="form-label">Nom<span class="text-red-500">*</span></label>
                                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($member['name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Poste<span class="text-red-500">*</span></label>
                                    <input type="text" name="position" class="form-control" value="<?php echo htmlspecialchars($member['position']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Description<span class="text-red-500">*</span></label>
                                    <textarea name="description" class="form-control textarea-lg" required><?php echo htmlspecialchars($member['description']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Nouvelle image (JPG, PNG, max 5MB)<span class="text-gray-500 text-sm"> (optionnel)</span></label>
                                    <input type="file" name="image" class="form-control" accept="image/jpeg,image/png" onchange="previewImage(this, 'preview_<?php echo htmlspecialchars($member['id']); ?>')">
                                </div>
                                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>Sauvegarder</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Actualités -->
            <div id="news" class="tab-content">
                <div class="add-news-form">
                    <h3 style="margin-bottom: 1rem;"><i class="fas fa-newspaper"></i>Ajouter une actualité</h3>
                    <form method="POST" id="add-news-form" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add_news">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Titre<span class="text-red-500">*</span></label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date de publication<span class="text-red-500">*</span></label>
                                <input type="datetime-local" name="publish_date" class="form-control" required value="<?php echo date('Y-m-d\TH:i'); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Contenu<span class="text-red-500">*</span></label>
                            <div class="rich-editor">
                                <div class="editor-toolbar">
                                    <button type="button" class="editor-btn" onclick="formatText('bold')" title="Gras"><i class="fas fa-bold"></i></button>
                                    <button type="button" class="editor-btn" onclick="formatText('italic')" title="Italique"><i class="fas fa-italic"></i></button>
                                    <button type="button" class="editor-btn" onclick="insertList('ul')" title="Liste à puces"><i class="fas fa-list-ul"></i></button>
                                    <button type="button" class="editor-btn" onclick="insertHeading()" title="Titre"><i class="fas fa-heading"></i></button>
                                </div>
                                <div class="editor-content" contenteditable="true" id="newNewsContent"></div>
                            </div>
                            <input type="hidden" name="content" id="new_news_content">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Image (JPG, PNG, max 5MB)<span class="text-gray-500 text-sm"> (optionnel)</span></label>
                            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png" onchange="previewImage(this, 'new_news_preview')">
                            <img id="new_news_preview" class="image-preview" alt="Aperçu de l'image">
                        </div>
                        <button type="submit" class="btn btn-success" onclick="saveNewEditorContent('news')"><i class="fas fa-plus"></i>Ajouter l'actualité</button>
                    </form>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3>Actualités existantes</h3>
                    <button class="btn btn-outline" onclick="toggleReorderMode('news')"><i class="fas fa-sort"></i>Mode réorganisation</button>
                </div>

                <div class="news-grid" id="newsGrid">
                    <?php foreach ($news as $index => $article): ?>
                        <div class="news-card sortable-item" data-id="<?php echo htmlspecialchars($article['id']); ?>">
                            <div class="order-indicator"><?php echo $index + 1; ?></div>
                            <div class="drag-handle"><i class="fas fa-grip-vertical"></i></div>
                            <form method="POST" id="news-form-<?php echo htmlspecialchars($article['id']); ?>" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="update_news">
                                <input type="hidden" name="news_id" value="<?php echo htmlspecialchars($article['id']); ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                    <h4><?php echo htmlspecialchars($article['title']); ?></h4>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cette actualité ?');">
                                        <input type="hidden" name="action" value="delete_news">
                                        <input type="hidden" name="news_id" value="<?php echo htmlspecialchars($article['id']); ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                        <button type="submit" class="btn btn-mini btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                                <?php if (!empty($article['image_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($article['image_path']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="news-image">
                                <?php endif; ?>
                                <img id="preview_<?php echo htmlspecialchars($article['id']); ?>" class="image-preview" alt="Aperçu de la nouvelle image">
                                <div class="form-group">
                                    <label class="form-label">Titre<span class="text-red-500">*</span></label>
                                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($article['title']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Date de publication<span class="text-red-500">*</span></label>
                                    <input type="datetime-local" name="publish_date" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($article['publish_date'])); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Contenu<span class="text-red-500">*</span></label>
                                    <div class="rich-editor">
                                        <div class="editor-toolbar">
                                            <button type="button" class="editor-btn" onclick="formatText('bold')" title="Gras"><i class="fas fa-bold"></i></button>
                                            <button type="button" class="editor-btn" onclick="formatText('italic')" title="Italique"><i class="fas fa-italic"></i></button>
                                            <button type="button" class="editor-btn" onclick="insertList('ul')" title="Liste à puces"><i class="fas fa-list-ul"></i></button>
                                            <button type="button" class="editor-btn" onclick="insertHeading()" title="Titre"><i class="fas fa-heading"></i></button>
                                        </div>
                                        <div class="editor-content" contenteditable="true" data-news-id="<?php echo htmlspecialchars($article['id']); ?>">
                                            <?php echo !empty($article['content']) ? $article['content'] : '<p>Contenu à compléter...</p>'; ?>
                                        </div>
                                    </div>
                                    <input type="hidden" name="content" id="news_content_<?php echo htmlspecialchars($article['id']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Nouvelle image (JPG, PNG, max 5MB)<span class="text-gray-500 text-sm"> (optionnel)</span></label>
                                    <input type="file" name="image" class="form-control" accept="image/jpeg,image/png" onchange="previewImage(this, 'preview_<?php echo htmlspecialchars($article['id']); ?>')">
                                </div>
                                <button type="submit" class="btn btn-success" onclick="saveEditorContent(<?php echo htmlspecialchars($article['id']); ?>, 'news')"><i class="fas fa-save"></i>Sauvegarder</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Événements -->
            <div id="events" class="tab-content">
                <div class="add-news-form">
                    <h3 style="margin-bottom: 1rem;"><i class="fas fa-calendar"></i>Ajouter un événement</h3>
                    <form method="POST" id="add-event-form" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add_event">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Titre<span class="text-red-500">*</span></label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date de l'événement<span class="text-red-500">*</span></label>
                                <input type="datetime-local" name="event_date" class="form-control" required value="<?php echo date('Y-m-d\TH:i'); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Contenu<span class="text-red-500">*</span></label>
                            <div class="rich-editor">
                                <div class="editor-toolbar">
                                    <button type="button" class="editor-btn" onclick="formatText('bold')" title="Gras"><i class="fas fa-bold"></i></button>
                                    <button type="button" class="editor-btn" onclick="formatText('italic')" title="Italique"><i class="fas fa-italic"></i></button>
                                    <button type="button" class="editor-btn" onclick="insertList('ul')" title="Liste à puces"><i class="fas fa-list-ul"></i></button>
                                    <button type="button" class="editor-btn" onclick="insertHeading()" title="Titre"><i class="fas fa-heading"></i></button>
                                </div>
                                <div class="editor-content" contenteditable="true" id="newEventContent"></div>
                            </div>
                            <input type="hidden" name="content" id="new_event_content">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Image (JPG, PNG, max 5MB)<span class="text-gray-500 text-sm"> (optionnel)</span></label>
                            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png" onchange="previewImage(this, 'new_event_preview')">
                            <img id="new_event_preview" class="image-preview" alt="Aperçu de l'image">
                        </div>
                        <button type="submit" class="btn btn-success" onclick="saveNewEditorContent('event')"><i class="fas fa-plus"></i>Ajouter l'événement</button>
                    </form>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3>Événements existants</h3>
                    <button class="btn btn-outline" onclick="toggleReorderMode('events')"><i class="fas fa-sort"></i>Mode réorganisation</button>
                </div>

                <div class="news-grid" id="eventsGrid">
                    <?php foreach ($events as $index => $event): ?>
                        <div class="news-card sortable-item" data-id="<?php echo htmlspecialchars($event['id']); ?>">
                            <div class="order-indicator"><?php echo $index + 1; ?></div>
                            <div class="drag-handle"><i class="fas fa-grip-vertical"></i></div>
                            <form method="POST" id="event-form-<?php echo htmlspecialchars($event['id']); ?>" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="update_event">
                                <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event['id']); ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                    <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cet événement ?');">
                                        <input type="hidden" name="action" value="delete_event">
                                        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event['id']); ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                        <button type="submit" class="btn btn-mini btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                                <?php if (!empty($event['image_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($event['image_path']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="news-image">
                                <?php endif; ?>
                                <img id="preview_<?php echo htmlspecialchars($event['id']); ?>" class="image-preview" alt="Aperçu de la nouvelle image">
                                <div class="form-group">
                                    <label class="form-label">Titre<span class="text-red-500">*</span></label>
                                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($event['title']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Date de l'événement<span class="text-red-500">*</span></label>
                                    <input type="datetime-local" name="event_date" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($event['event_date'])); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Contenu<span class="text-red-500">*</span></label>
                                    <div class="rich-editor">
                                        <div class="editor-toolbar">
                                            <button type="button" class="editor-btn" onclick="formatText('bold')" title="Gras"><i class="fas fa-bold"></i></button>
                                            <button type="button" class="editor-btn" onclick="formatText('italic')" title="Italique"><i class="fas fa-italic"></i></button>
                                            <button type="button" class="editor-btn" onclick="insertList('ul')" title="Liste à puces"><i class="fas fa-list-ul"></i></button>
                                            <button type="button" class="editor-btn" onclick="insertHeading()" title="Titre"><i class="fas fa-heading"></i></button>
                                        </div>
                                        <div class="editor-content" contenteditable="true" data-event-id="<?php echo htmlspecialchars($event['id']); ?>">
                                            <?php echo !empty($event['content']) ? $event['content'] : '<p>Contenu à compléter...</p>'; ?>
                                        </div>
                                    </div>
                                    <input type="hidden" name="content" id="event_content_<?php echo htmlspecialchars($event['id']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Nouvelle image (JPG, PNG, max 5MB)<span class="text-gray-500 text-sm"> (optionnel)</span></label>
                                    <input type="file" name="image" class="form-control" accept="image/jpeg,image/png" onchange="previewImage(this, 'preview_<?php echo htmlspecialchars($event['id']); ?>')">
                                </div>
                                <button type="submit" class="btn btn-success" onclick="saveEditorContent(<?php echo htmlspecialchars($event['id']); ?>, 'event')"><i class="fas fa-save"></i>Sauvegarder</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Modal d'édition de contenu -->
            <div id="editModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Modifier le contenu</h3>
                        <button class="modal-close" onclick="closeEditModal()">&times;</button>
                    </div>
                    <form method="POST" id="editForm">
                        <input type="hidden" name="action" value="update_content">
                        <input type="hidden" id="editSection" name="content_section">
                        <input type="hidden" id="editKey" name="content_key">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                        <div class="form-group">
                            <label class="form-label">Section</label>
                            <input type="text" id="editSectionDisplay" class="form-control" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Clé</label>
                            <input type="text" id="editKeyDisplay" class="form-control" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Valeur<span class="text-red-500">*</span></label>
                            <textarea id="editValue" name="new_value" class="form-control textarea-lg" required></textarea>
                        </div>
                        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                            <button type="button" class="btn btn-outline" onclick="closeEditModal()">Annuler</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>Sauvegarder</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <script>
        let reorderModes = { services: false, team: false, news: false, events: false };
        let sortables = { services: null, team: null, news: null, events: null };

        function openTab(evt, tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            evt.currentTarget.classList.add('active');
        }

        function formatText(command) {
            document.execCommand(command, false, null);
            document.activeElement.focus();
        }

        function insertList(type) {
            document.execCommand(type === 'ul' ? 'insertUnorderedList' : 'insertOrderedList', false, null);
            document.activeElement.focus();
        }

        function insertHeading() {
            const selection = window.getSelection().toString();
            document.execCommand(selection ? 'formatBlock' : 'insertHTML', false, selection ? 'h3' : '<h3>Nouveau titre</h3><p></p>');
            document.activeElement.focus();
        }

        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const maxSize = 5 * 1024 * 1024;
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (!['image/jpeg', 'image/png'].includes(file.type)) {
                    alert('Seuls les fichiers JPG et PNG sont acceptés.');
                    input.value = '';
                    preview.classList.remove('show');
                    return;
                }
                if (file.size > maxSize) {
                    alert('Le fichier est trop volumineux (max 5MB).');
                    input.value = '';
                    preview.classList.remove('show');
                    return;
                }
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.classList.add('show');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.remove('show');
            }
        }

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

        function updateColorPreview(input, id) {
            const preview = document.getElementById(`color_preview_${id}`);
            if (preview) preview.style.background = input.value;
        }

        function updateAddColorPreview(input) {
            const preview = document.getElementById('add_color_preview');
            if (preview) preview.style.background = input.value;
        }

        function saveEditorContent(id, type) {
            const editor = document.querySelector(`[data-${type}-id="${id}"]`);
            const hiddenInput = document.getElementById(`${type === 'service' ? 'detailed_content' : (type === 'news' ? 'news_content' : 'event_content')}_${id}`);
            if (editor && hiddenInput) hiddenInput.value = editor.innerHTML.trim();
            if (id && type === 'service') {
                localStorage.removeItem(`service_draft_${id}`);
            } else if (id && type === 'news') {
                localStorage.removeItem(`news_draft_${id}`);
            } else if (id && type === 'event') {
                localStorage.removeItem(`event_draft_${id}`);
            }
        }

        function saveNewEditorContent(type) {
            let editor = document.getElementById(`new${type === 'service' ? 'Service' : 'News'}Content`);
            if (type === 'event') editor = document.getElementById('newEventContent');
            const hiddenInput = document.getElementById(`new_${type === 'service' ? 'detailed_content' : (type === 'news' ? 'news_content' : 'event_content')}`);
            if (editor && hiddenInput) hiddenInput.value = editor.innerHTML.trim();
        }

        function toggleReorderMode(type) {
            reorderModes[type] = !reorderModes[type];
            const grid = document.getElementById(`${type}Grid`);
            const dragHandles = grid.querySelectorAll('.drag-handle');
            const orderIndicators = grid.querySelectorAll('.order-indicator');
            const button = document.querySelector(`[onclick="toggleReorderMode('${type}')"]`);

            if (reorderModes[type]) {
                grid.style.opacity = '0.8';
                dragHandles.forEach(handle => handle.style.display = 'block');
                orderIndicators.forEach(indicator => indicator.style.display = 'flex');
                sortables[type] = new Sortable(grid, {
                    animation: 150,
                    handle: '.drag-handle',
                    ghostClass: 'sortable-ghost',
                    onEnd: () => updateOrder(type)
                });
                button.innerHTML = '<i class="fas fa-check"></i>Terminer la réorganisation';
            } else {
                grid.style.opacity = '1';
                dragHandles.forEach(handle => handle.style.display = 'none');
                orderIndicators.forEach(indicator => indicator.style.display = 'none');
                if (sortables[type]) {
                    sortables[type].destroy();
                    sortables[type] = null;
                }
                button.innerHTML = '<i class="fas fa-sort"></i>Mode réorganisation';
            }
        }

        function updateOrder(type) {
            const items = document.querySelectorAll(`#${type}Grid .sortable-item`);
            const orders = {};
            items.forEach((item, index) => {
                orders[item.dataset.id] = index + 1;
                item.querySelector('.order-indicator').textContent = index + 1;
            });
            
            // Créer un formulaire caché pour soumettre l'ordre
            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = `reorder_${type}`;
            form.appendChild(actionInput);
            
            const ordersInput = document.createElement('input');
            ordersInput.type = 'hidden';
            ordersInput.name = 'orders';
            ordersInput.value = JSON.stringify(orders);
            form.appendChild(ordersInput);
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = '<?php echo htmlspecialchars(generateCSRFToken()); ?>';
            form.appendChild(csrfInput);
            
            document.body.appendChild(form);
            form.submit();
        }

 function setupFormValidation() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', e => {
                // Correction : ne pas empêcher le submit pour les formulaires sans JS/AJAX
                if (form.id !== 'add-team-form' && form.id !== 'add-service-form' && form.id !== 'add-news-form' && form.id !== 'add-event-form') return;

                e.preventDefault();
                const requiredFields = form.querySelectorAll('input[required], textarea[required], select[required]');
                let isValid = true;
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('border-red-500');
                    } else {
                        field.classList.remove('border-red-500');
                    }
                });
                    const editor = form.querySelector('.editor-content');
                    if (editor) {
                        const hiddenInput = form.querySelector(`input[name="${editor.dataset.serviceId ? 'detailed_content' : (editor.dataset.newsId ? 'content' : '')}"]`) || form.querySelector('input[name="detailed_content"]') || form.querySelector('input[name="content"]');
                        if (hiddenInput) {
                            const content = editor.innerHTML.trim();
                            const isEditorRequired = hiddenInput.name !== 'detailed_content';
                            let finalContent = content;
                            if (content === '<p>Contenu à compléter...</p>' || content === '<p>Contenu détaillé à compléter...</p>') {
                                finalContent = '';
                            }
                            if (isEditorRequired && (!finalContent || content === '<p>Contenu à compléter...</p>' || content === '<p>Contenu détaillé à compléter...</p>')) {
                                isValid = false;
                                editor.closest('.rich-editor').classList.add('border-red-500');
                                alert('Le contenu de l\'éditeur ne peut pas être vide ou contenir le texte par défaut.');
                            } else {
                                hiddenInput.value = finalContent;
                                editor.closest('.rich-editor').classList.remove('border-red-500');
                            }
                        }
                    }
                    if (isValid) {
                    form.submit(); // Soumettre le formulaire normalement pour recharger la page
                } else {
                    alert('Veuillez remplir tous les champs requis.');
                }
            });
        });
    }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('input[name="icon"]').forEach(input => {
                input.addEventListener('input', e => {
                    const form = e.target.closest('form');
                    const preview = form.querySelector('.service-icon i');
                    if (preview) preview.className = e.target.value;
                });
            });
            document.querySelectorAll('input[name="color"]').forEach(input => {
                input.addEventListener('input', e => {
                    const form = e.target.closest('form');
                    const preview = form.querySelector('.color-preview');
                    const serviceIcon = form.querySelector('.service-icon');
                    if (preview) preview.style.background = e.target.value;
                    if (serviceIcon) serviceIcon.style.background = e.target.value;
                });
            });
            document.querySelectorAll('.editor-content').forEach(editor => {
                editor.addEventListener('input', function() {
                    const id = this.dataset.serviceId || this.dataset.newsId || this.dataset.eventId;
                    let type = this.dataset.serviceId ? 'service' : 'news';
                    if (this.dataset.eventId) type = 'event';
                    if (id) {
                        clearTimeout(window.autoSaveTimeout);
                        window.autoSaveTimeout = setTimeout(() => {
                            localStorage.setItem(`${type}_draft_${id}`, this.innerHTML);
                            console.log(`Brouillon sauvegardé pour ${type} ${id}`);
                        }, 2000);
                    }
                });
            });
            document.querySelectorAll('.editor-content').forEach(editor => {
                const id = editor.dataset.serviceId || editor.dataset.newsId || editor.dataset.eventId;
                let type = editor.dataset.serviceId ? 'service' : 'news';
                if (editor.dataset.eventId) type = 'event';
                if (id) {
                    const draft = localStorage.getItem(`${type}_draft_${id}`);
                    if (draft && confirm(`Un brouillon a été trouvé pour ce ${type}. Voulez-vous le restaurer ?`)) {
                        editor.innerHTML = draft;
                    }
                }
            });
            document.getElementById('editForm').addEventListener('submit', function(e) {
                // Ne pas empêcher le submit, laisser le formulaire se soumettre normalement
                // La page se rechargera automatiquement
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeEditModal();
            });
            document.getElementById('editModal').addEventListener('click', function(e) {
                if (e.target === this) closeEditModal();
            });
            setupFormValidation();
            console.log('Interface d\'administration avancée chargée avec succès');
        });
    </script>
</body>
</html>