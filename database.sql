-- Mise à jour de la table services pour ajouter le contenu détaillé
ALTER TABLE services ADD COLUMN detailed_content TEXT;

-- Création de la table pour les fichiers des contacts
CREATE TABLE IF NOT EXISTS contact_files (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    contact_id INTEGER NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INTEGER NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    uploaded_at DATETIME NOT NULL,
    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE
);

-- Index pour améliorer les performances
CREATE INDEX IF NOT EXISTS idx_contact_files_contact_id ON contact_files(contact_id);
CREATE INDEX IF NOT EXISTS idx_contacts_status ON contacts(status);
CREATE INDEX IF NOT EXISTS idx_contacts_created_at ON contacts(created_at);

-- Insertion de contenus par défaut pour les services (si ils n'existent pas déjà)
UPDATE services SET detailed_content = '
<h3>Notre approche</h3>
<p>Nous privilégions une approche personnalisée et sur-mesure pour chaque client. Notre méthode comprend :</p>
<ul>
    <li>Analyse approfondie de votre situation</li>
    <li>Conseil juridique adapté à vos besoins</li>
    <li>Accompagnement tout au long de la procédure</li>
    <li>Suivi post-dossier et conseils préventifs</li>
</ul>

<h3>Pourquoi nous choisir ?</h3>
<p>Fort de plus de 20 ans d''expérience, notre cabinet vous garantit :</p>
<ul>
    <li>Une expertise reconnue dans ce domaine</li>
    <li>Un accompagnement personnalisé</li>
    <li>Une disponibilité et une réactivité optimales</li>
    <li>Des tarifs transparents et compétitifs</li>
</ul>

<h3>Première consultation</h3>
<p>Nous vous proposons une première consultation gratuite pour évaluer votre situation et vous présenter les différentes options qui s''offrent à vous. Cette rencontre nous permet de mieux comprendre vos besoins et de vous proposer la stratégie la plus adaptée.</p>
' WHERE detailed_content IS NULL OR detailed_content = '';