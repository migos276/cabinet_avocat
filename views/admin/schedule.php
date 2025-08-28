<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning - Administration</title>
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

        .section-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: #1f2937;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #1f2937;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }

        .slots-list {
            list-style: none;
        }

        .slot-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .slot-item:last-child {
            border-bottom: none;
        }

        .slot-info {
            flex: 1;
        }

        .slot-info h4 {
            font-size: 1rem;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .slot-info p {
            font-size: 0.85rem;
            color: #6b7280;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-available {
            background: #d1fae5;
            color: #065f46;
        }

        .status-booked {
            background: #fef3c7;
            color: #92400e;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .logout-btn {
            position: fixed;
            bottom: 2rem;
            left: 2rem;
            background: #ef4444;
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 50px;
            height: 50px;
        }

        .logout-btn:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: -250px;
                width: 250px;
                height: 100vh;
                z-index: 1000;
                transition: left 0.3s ease;
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><?php echo defined('SITE_NAME') ? SITE_NAME : 'Cabinet Excellence'; ?></h2>
                <p>Administration</p>
            </div>
            <ul class="sidebar-nav">
                <li><a href="/admin/dashboard">
                    <i class="fas fa-chart-line"></i>
                    Tableau de bord
                </a></li>
                <li><a href="/admin/content">
                    <i class="fas fa-edit"></i>
                    Contenu du site
                </a></li>
                <li><a href="/admin/contacts">
                    <i class="fas fa-envelope"></i>
                    Messages
                    <?php if ($stats['new_contacts'] > 0): ?>
                        <span class="status-badge status-new" style="margin-left: 0.5rem;"><?php echo $stats['new_contacts']; ?></span>
                    <?php endif; ?>
                </a></li>
                <li><a href="/admin/schedule" class="active">
                    <i class="fas fa-calendar-alt"></i>
                    Planning
                    <?php if ($stats['appointments'] > 0): ?>
                        <span class="status-badge status-pending" style="margin-left: 0.5rem;"><?php echo $stats['appointments']; ?></span>
                    <?php endif; ?>
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

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1>Planning</h1>
                <div class="breadcrumb">Administration / Planning</div>
            </div>

            <!-- Flash Message -->
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['flash_message']['success'] ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($_SESSION['flash_message']['message']); ?>
                </div>
                <?php unset($_SESSION['flash_message']); ?>
            <?php endif; ?>

            <!-- Add Daily Availability Form -->
            <div class="section-card">
                <h2 class="section-title">
                    <i class="fas fa-plus-circle"></i>
                    Ajouter disponibilité quotidienne
                </h2>
                <form action="/admin/schedule" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                    <input type="hidden" name="action" value="add_daily_slots">
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="all_day" name="all_day" checked>
                            Disponible toute la journée (09:00 - 18:00)
                        </label>
                    </div>
                    <div id="availability_times" style="display: none;">
                        <div class="form-group">
                            <label for="start_time">Heure de début</label>
                            <input type="time" id="start_time" name="start_time" class="form-control" value="09:00" required>
                        </div>
                        <div class="form-group">
                            <label for="end_time">Heure de fin</label>
                            <input type="time" id="end_time" name="end_time" class="form-control" value="18:00" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="break_start">Début de la pause (optionnel)</label>
                        <input type="time" id="break_start" name="break_start" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="break_end">Fin de la pause (optionnel)</label>
                        <input type="time" id="break_end" name="break_end" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Générer les slots
                    </button>
                </form>
            </div>

            <!-- Slots List -->
            <div class="section-card">
                <h2 class="section-title">
                    <i class="fas fa-calendar-alt"></i>
                    Créneaux disponibles
                </h2>
                <?php if (empty($slots)): ?>
                    <p style="color: #6b7280; text-align: center; padding: 2rem;">Aucun créneau disponible pour le moment.</p>
                <?php else: ?>
                    <ul class="slots-list">
                        <?php foreach ($slots as $slot): ?>
                            <li class="slot-item">
                                <div class="slot-info">
                                    <h4><?php echo date('d/m/Y H:i', strtotime($slot['start_time'])); ?> - <?php echo date('H:i', strtotime($slot['end_time'])); ?></h4>
                                    <p>
                                        <strong>Statut :</strong>
                                        <span class="status-badge <?php echo ($slot['is_booked'] ?? false) ? 'status-booked' : 'status-available'; ?>">
                                            <?php echo ($slot['is_booked'] ?? false) ? 'Réservé' : 'Disponible'; ?>
                                        </span>
                                    </p>
                                    <?php if ($slot['appointment_count'] > 0): ?>
                                        <p><strong>Rendez-vous :</strong> <?php echo $slot['appointment_count']; ?> associé(s)</p>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <?php if (isset($slot['is_booked']) && !$slot['is_booked']): ?>
                                        <form action="/admin/schedule" method="POST" style="display: inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                            <input type="hidden" name="action" value="delete_slot">
                                            <input type="hidden" name="slot_id" value="<?php echo $slot['id']; ?>">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer ce créneau ?');">
                                                <i class="fas fa-trash"></i>
                                                Supprimer
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Logout Button -->
    <button class="logout-btn" onclick="logout()" title="Se déconnecter">
        <i class="fas fa-sign-out-alt"></i>
    </button>

    <script>
        function logout() {
            if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
                window.location.href = '/admin/logout';
            }
        }

        // Gestion de la checkbox "toute la journée"
        document.addEventListener('DOMContentLoaded', () => {
            const allDayCheckbox = document.getElementById('all_day');
            const availabilityTimes = document.getElementById('availability_times');
            const startTime = document.getElementById('start_time');
            const endTime = document.getElementById('end_time');
            const dateInput = document.getElementById('date');

            // Empêcher les dates passées
            const today = new Date().toISOString().split('T')[0];
            dateInput.setAttribute('min', today);

            function toggleAvailability() {
                if (allDayCheckbox.checked) {
                    availabilityTimes.style.display = 'none';
                    startTime.required = false;
                    endTime.required = false;
                } else {
                    availabilityTimes.style.display = 'block';
                    startTime.required = true;
                    endTime.required = true;
                }
            }

            allDayCheckbox.addEventListener('change', toggleAvailability);
            toggleAvailability(); // Initialisation
        });
    </script>
</body>
</html>