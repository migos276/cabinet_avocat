<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contenu du site - Administration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        }

        .tab-button {
            flex: 1;
            padding: 1rem 2rem;
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
        }

        .section-title {
            font-size: 1.2rem;
            color: #1f2937;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
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
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .service-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 2px solid #f3f4f6;
        }

        .service-header {
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

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .team-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 2px solid #f3f4f6;
        }

        .team-image {
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
            }
            
            .services-grid,
            .team-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><?php echo SITE_NAME; ?></h2>
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
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <div class="tabs">
                <button class="tab-button active" onclick="openTab(event, 'general')">
                    <i class="fas fa-home"></i>
                    Contenu général
                </button>
                <button class="tab-button" onclick="openTab(event, 'services')">
                    <i class="fas fa-gavel"></i>
                    Services
                </button>
                <button class="tab-button" onclick="openTab(event, 'team')">
                    <i class="fas fa-users"></i>
                    Équipe
                </button>
            </div>

            <div id="general" class="tab-content active">
                <form method="POST">
                    <input type="hidden" name="action" value="update_content">
                    
                    <div class="form-grid">
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-star"></i>
                                Section Hero (Accueil)
                            </h3>
                            <div class="form-group">
                                <label class="form-label">Titre principal</label>
                                <input type="text" name="content[hero][title]" class="form-control form-control-lg" 
                                       value="<?php echo htmlspecialchars($content['hero']['title'] ?? 'Excellence Juridique à Votre Service'); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Sous-titre</label>
                                <textarea name="content[hero][subtitle]" class="form-control" rows="3"><?php echo htmlspecialchars($content['hero']['subtitle'] ?? 'Depuis plus de 20 ans, nous accompagnons nos clients avec expertise, intégrité et dévouement dans tous leurs défis juridiques les plus complexes.'); ?></textarea>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-info-circle"></i>
                                Section À propos
                            </h3>
                            <div class="form-group">
                                <label class="form-label">Titre</label>
                                <input type="text" name="content[about][title]" class="form-control" 
                                       value="<?php echo htmlspecialchars($content['about']['title'] ?? 'Votre Réussite, Notre Mission'); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Sous-titre</label>
                                <textarea name="content[about][subtitle]" class="form-control" rows="3"><?php echo htmlspecialchars($content['about']['subtitle'] ?? 'Fort d\'une expérience reconnue et d\'une approche personnalisée, notre cabinet vous offre un accompagnement juridique d\'excellence adapté à vos besoins spécifiques.'); ?></textarea>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-gavel"></i>
                                Section Services
                            </h3>
                            <div class="form-group">
                                <label class="form-label">Titre</label>
                                <input type="text" name="content[services][title]" class="form-control" 
                                       value="<?php echo htmlspecialchars($content['services']['title'] ?? 'Domaines d\'Expertise'); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Sous-titre</label>
                                <textarea name="content[services][subtitle]" class="form-control" rows="3"><?php echo htmlspecialchars($content['services']['subtitle'] ?? 'Une expertise reconnue dans des domaines juridiques essentiels pour répondre à tous vos besoins'); ?></textarea>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-users"></i>
                                Section Équipe
                            </h3>
                            <div class="form-group">
                                <label class="form-label">Titre</label>
                                <input type="text" name="content[team][title]" class="form-control" 
                                       value="<?php echo htmlspecialchars($content['team']['title'] ?? 'Des Experts à Vos Côtés'); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Sous-titre</label>
                                <textarea name="content[team][subtitle]" class="form-control" rows="3"><?php echo htmlspecialchars($content['team']['subtitle'] ?? 'Des avocats expérimentés et passionnés, reconnus pour leur expertise et leur engagement'); ?></textarea>
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

            <div id="services" class="tab-content">
                <div class="services-grid">
                    <?php foreach ($services as $service): ?>
                        <div class="service-card">
                            <form method="POST">
                                <input type="hidden" name="action" value="update_service">
                                <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                
                                <div class="service-header">
                                    <div class="service-icon" style="background: <?php echo htmlspecialchars($service['color']); ?>;">
                                        <i class="<?php echo htmlspecialchars($service['icon']); ?>"></i>
                                    </div>
                                    <div>
                                        <h4><?php echo htmlspecialchars($service['title']); ?></h4>
                                        <small style="color: #6b7280;">ID: <?php echo $service['id']; ?></small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Titre du service</label>
                                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($service['title']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Description courte</label>
                                    <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($service['description']); ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Contenu détaillé pour la page service</label>
                                    <div class="rich-editor">
                                        <div class="editor-toolbar">
                                            <button type="button" class="editor-btn" onclick="formatText('bold')" title="Gras">
                                                <i class="fas fa-bold"></i>
                                            </button>
                                            <button type="button" class="editor-btn" onclick="formatText('italic')" title="Italique">
                                                <i class="fas fa-italic"></i>
                                            </button>
                                            <button type="button" class="editor-btn" onclick="formatText('underline')" title="Souligné">
                                                <i class="fas fa-underline"></i>
                                            </button>
                                            <button type="button" class="editor-btn" onclick="insertList('ul')" title="Liste à p decoding="async"uces">
                                                <i class="fas fa-list-ul"></i>
                                            </button>
                                            <button type="button" class="editor-btn" onclick="insertList('ol')" title="Liste numérotée">
                                                <i class="fas fa-list-ol"></i>
                                            </button>
                                            <button type="button" class="editor-btn" onclick="insertHeading()" title="Titre">
                                                <i class="fas fa-heading"></i>
                                            </button>
                                        </div>
                                        <div class="editor-content" contenteditable="true" data-service-id="<?php echo $service['id']; ?>">
                                            <?php echo !empty($service['detailed_content']) ? $service['detailed_content'] : '
                                                <h3>Notre approche</h3>
                                                <p>Nous privilégions une approche personnalisée et sur-mesure pour chaque client. Notre méthode comprend :</p>
                                                <ul>
                                                    <li>Analyse approfondie de votre situation</li>
                                                    <li>Conseil juridique adapté à vos besoins</li>
                                                    <li>Accompagnement tout au long de la procédure</li>
                                                    <li>Suivi post-dossier et conseils préventifs</li>
                                                </ul>
                                                <h3>Pourquoi nous choisir ?</h3>
                                                <p>Fort de plus de 20 ans d\'expérience, notre cabinet vous garantit :</p>
                                                <ul>
                                                    <li>Une expertise reconnue dans ce domaine</li>
                                                    <li>Un accompagnement personnalisé</li>
                                                    <li>Une disponibilité et une réactivité optimales</li>
                                                    <li>Des tarifs transparents et compétitifs</li>
                                                </ul>
                                                <h3>Première consultation</h3>
                                                <p>Nous vous proposons une première consultation gratuite pour évaluer votre situation et vous présenter les différentes options qui s\'offrent à vous.</p>
                                            '; ?>
                                        </div>
                                    </div>
                                    <input type="hidden" name="detailed_content" id="detailed_content_<?php echo $service['id']; ?>" value="">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Icône (classe Font Awesome)</label>
                                    <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($service['icon']); ?>" placeholder="fas fa-gavel">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Couleur</label>
                                    <div class="color-picker-group">
                                        <input type="color" name="color" class="form-control" value="<?php echo htmlspecialchars($service['color']); ?>" onchange="updateColorPreview(this, <?php echo $service['id']; ?>)">
                                        <div class="color-preview" id="color_preview_<?php echo $service['id']; ?>" style="background: <?php echo htmlspecialchars($service['color']); ?>;">
                                            <i class="<?php echo htmlspecialchars($service['icon']); ?>"></i>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success" onclick="saveServiceContent(<?php echo $service['id']; ?>)">
                                    <i class="fas fa-save"></i>
                                    Sauvegarder
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="team" class="tab-content">
                <div class="form-section" style="margin-bottom: 2rem;">
                    <h3 class="section-title">
                        <i class="fas fa-user-plus"></i>
                        Ajouter un nouveau membre
                    </h3>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add_team">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Nom</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Poste</label>
                                <input type="text" name="position" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control textarea-lg" required></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Image du membre (JPG, PNG, GIF, max 5MB)</label>
                                <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/gif" required onchange="previewImage(this, 'new_team_preview')">
                                <img id="new_team_preview" class="image-preview" alt="Aperçu de l'image">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus"></i>
                            Ajouter le membre
                        </button>
                    </form>
                </div>

                <div class="team-grid">
                    <?php foreach ($team as $member): ?>
                        <div class="team-card">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="update_team">
                                <input type="hidden" name="team_id" value="<?php echo $member['id']; ?>">
                                
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                    <h4><?php echo htmlspecialchars($member['name']); ?></h4>
                                    <button type="submit" name="action" value="delete_team" class="btn btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer ce membre ?');">
                                        <i class="fas fa-trash"></i>
                                        Supprimer
                                    </button>
                                </div>

                                <img src="<?php echo htmlspecialchars($member['image_path']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" class="team-image">
                                <img id="preview_<?php echo $member['id']; ?>" class="image-preview" alt="Aperçu de la nouvelle image">

                                <div class="form-group">
                                    <label class="form-label">Nom</label>
                                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($member['name']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Poste</label>
                                    <input type="text" name="position" class="form-control" value="<?php echo htmlspecialchars($member['position']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control textarea-lg" required><?php echo htmlspecialchars($member['description']); ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Nouvelle image (optionnel, JPG, PNG, GIF, max 5MB)</label>
                                    <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/gif" onchange="previewImage(this, 'preview_<?php echo $member['id']; ?>')">
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

    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.remove("active");
            }
            tablinks = document.getElementsByClassName("tab-button");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("active");
            }
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }

        function updateColorPreview(input, serviceId) {
            const preview = document.getElementById('color_preview_' + serviceId);
            preview.style.background = input.value;
        }

        function formatText(command) {
            document.execCommand(command, false, null);
        }

        function insertList(type) {
            if (type === 'ul') {
                document.execCommand('insertUnorderedList', false, null);
            } else {
                document.execCommand('insertOrderedList', false, null);
            }
        }

        function insertHeading() {
            const selection = window.getSelection().toString();
            if (selection) {
                document.execCommand('formatBlock', false, 'h3');
            } else {
                document.execCommand('insertHTML', false, '<h3>Nouveau titre</h3><p></p>');
            }
        }

        function saveServiceContent(serviceId) {
            const editorContent = document.querySelector(`[data-service-id="${serviceId}"]`);
            const hiddenInput = document.getElementById(`detailed_content_${serviceId}`);
            hiddenInput.value = editorContent.innerHTML;
        }

        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.add('show');
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.remove('show');
            }
        }

        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const editorContent = this.querySelector('.editor-content');
                if (editorContent) {
                    const serviceId = editorContent.dataset.serviceId;
                    const hiddenInput = this.querySelector(`input[name="detailed_content"]`);
                    if (hiddenInput) {
                        hiddenInput.value = editorContent.innerHTML;
                    }
                }
            });
        });

        document.querySelectorAll('.editor-content').forEach(editor => {
            editor.addEventListener('paste', function(e) {
                e.preventDefault();
                const text = (e.originalEvent || e).clipboardData.getData('text/plain');
                document.execCommand('insertText', false, text);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const colorInputs = document.querySelectorAll('input[type="color"]');
            colorInputs.forEach(input => {
                const serviceId = input.name === 'color' ? input.closest('form').querySelector('input[name="service_id"]').value : null;
                if (serviceId) {
                    updateColorPreview(input, serviceId);
                }
            });
        });

        let autoSaveTimeout;
        document.querySelectorAll('.editor-content').forEach(editor => {
            editor.addEventListener('input', function() {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(() => {
                    const serviceId = this.dataset.serviceId;
                    localStorage.setItem(`service_draft_${serviceId}`, this.innerHTML);
                    console.log(`Brouillon sauvegardé pour le service ${serviceId}`);
                }, 2000);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.editor-content').forEach(editor => {
                const serviceId = editor.dataset.serviceId;
                const draft = localStorage.getItem(`service_draft_${serviceId}`);
                if (draft && confirm('Un brouillon a été trouvé pour ce service. Voulez-vous le restaurer ?')) {
                    editor.innerHTML = draft;
                }
            });
        });

        function clearDraft(serviceId) {
            localStorage.removeItem(`service_draft_${serviceId}`);
        }

        console.log('Interface d\'administration du contenu chargée');
    </script>
</body>
</html>