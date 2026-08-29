INSERT INTO mv_settings (setting_key, setting_value, setting_type, updated_at) VALUES
('site_name', 'Monoverse', 'string', NOW()),
('site_tagline', 'IRC community websites by SimosNap', 'string', NOW()),
('site_mode', 'community', 'string', NOW()),
('theme', 'default', 'string', NOW()),
('chat_enabled', '1', 'boolean', NOW()),
('chat_default_channel', '#monoverse', 'string', NOW()),
('oauth_provider', 'simosnap', 'string', NOW())
ON DUPLICATE KEY UPDATE
setting_value = VALUES(setting_value),
setting_type = VALUES(setting_type),
updated_at = NOW();

INSERT INTO mv_pages (title, slug, content, status, created_at) VALUES
('Home', 'home', 'Benvenuto in Monoverse.', 'published', NOW()),
('Chat', 'chat', 'Entra nella chat IRC della community.', 'published', NOW()),
('Regolamento', 'regolamento', 'Inserisci qui il regolamento della community.', 'published', NOW()),
('Privacy', 'privacy', 'Inserisci qui la privacy policy.', 'published', NOW()),
('Contatti', 'contatti', 'Inserisci qui i contatti della community.', 'published', NOW())
ON DUPLICATE KEY UPDATE
title = VALUES(title),
content = VALUES(content),
status = VALUES(status),
updated_at = NOW();

INSERT INTO mv_modules (module_key, name, description, version, enabled, allowed_modes, config_json, installed_at, updated_at) VALUES
('core_pages', 'Pagine', 'Gestione delle pagine base del sito.', '0.1.0', 1, '["community","webradio","opensource","custom"]', '{}', NOW(), NOW()),
('core_chat', 'Chat IRC', 'Webchat IRC sempre presente in Monoverse.', '0.1.0', 1, '["community","webradio","opensource","custom"]', '{}', NOW(), NOW()),
('core_oauth', 'OAuth SimosNap', 'Login centralizzato tramite SimosNap.', '0.1.0', 1, '["community","webradio","opensource","custom"]', '{}', NOW(), NOW())
ON DUPLICATE KEY UPDATE
name = VALUES(name),
description = VALUES(description),
version = VALUES(version),
enabled = VALUES(enabled),
allowed_modes = VALUES(allowed_modes),
config_json = VALUES(config_json),
updated_at = NOW();
