CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `senha` VARCHAR(255) NOT NULL,
  `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `anuncios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL,
  `titulo` VARCHAR(120) NOT NULL,
  `descricao` TEXT,
  `foto` VARCHAR(255) DEFAULT NULL,
  `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed data
-- Senha para 'testuser' é 'password123'
INSERT INTO `usuarios` (`nome`, `email`, `senha`) VALUES
('Usuário Teste', 'testuser@example.com', '$2y$10$3.G.O2.lwc.N0M2nK3YhS.wXqK8U7i4j.L6E9R0g5aH3jK7lM8pC.');

-- Anúncio de exemplo para o Usuário Teste (ID 1)
INSERT INTO `anuncios` (`usuario_id`, `titulo`, `descricao`, `foto`) VALUES
(1, 'Cachorrinho Adorável para Adoção', 'Este é o Bob, um vira-lata muito amigável e brincalhão de 2 anos. Ele adora crianças e outros animais. Vacinado e vermifugado. Procuro um lar amoroso para ele.', NULL),
(1, 'Gatinha Carinhosa - Luna', 'Luna é uma gatinha de 1 ano, muito dócil e carinhosa. Adora um colo e um bom sachê. Castrada e vacinada. Perfeita para apartamento.', 'uploads/sample_cat.jpg');
