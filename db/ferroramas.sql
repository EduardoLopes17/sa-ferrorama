

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


CREATE TABLE `paradas` (
  `id` int(11) NOT NULL,
  `rota_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `ordem` int(11) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `paradas` (`id`, `rota_id`, `nome`, `ordem`, `criado_em`) VALUES
(23, 4, 'Boehmerwald', 1, '2026-11-17 11:34:16'),
(24, 4, 'Iririú', 2, '2026-11-17 11:34:16'),
(25, 4, 'Glória', 3, '2026-11-17 11:34:16'),
(26, 4, 'Itinga', 4, '2026-11-17 11:34:16'),
(27, 4, 'Guanabara', 5, '2026-11-17 11:34:16');


CREATE TABLE `rotas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `status` enum('Ativa','Inativa') NOT NULL DEFAULT 'Ativa',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `rotas` (`id`, `nome`, `descricao`, `status`, `criado_em`) VALUES
(4, 'CENTRO VILA NOVO', NULL, 'Ativa', '2026-11-05 10:28:51');



CREATE TABLE `sensores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `localizacao` varchar(150) NOT NULL,
  `tipo_sensor` varchar(100) NOT NULL,
  `endereco` varchar(200) NOT NULL,
  `status` enum('Ativo','Inativo') NOT NULL DEFAULT 'Ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `sensores` (`id`, `nome`, `localizacao`, `tipo_sensor`, `endereco`, `status`, `criado_em`) VALUES
(1, 'SENSOR 01', 'PLATAFORMA 2', 'Movimento', 'Escola', 'Inativo', '2026-11-05 11:34:50');



CREATE TABLE `trens` (
  `id` int(11) NOT NULL,
  `codigo` varchar(30) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `rota_origem` varchar(100) NOT NULL,
  `rota_destino` varchar(100) NOT NULL,
  `velocidade` decimal(6,2) NOT NULL DEFAULT 0.00,
  `status` enum('Em Operação','Parado','Manutenção','Inativo') NOT NULL DEFAULT 'Parado',
  `usuario_responsavel_id` int(11) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `rota_id` int(11) DEFAULT NULL,
  `proxima_parada_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `trens` (`id`, `codigo`, `modelo`, `rota_origem`, `rota_destino`, `velocidade`, `status`, `usuario_responsavel_id`, `criado_em`, `rota_id`, `proxima_parada_id`) VALUES
(7, 'TR 26-01', 'TREM BALA', '', '', 67.00, 'Manutenção', 4, '2026-11-05 10:28:33', 4, NULL),
(9, 'TR 26-02', 'TREM BALA', '', '', 67.00, 'Em Operação', 4, '2026-11-05 11:31:01', NULL, NULL);



CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('admin','usuario') NOT NULL DEFAULT 'usuario',
  `status` enum('ativo','inativo') NOT NULL DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `email`, `senha`, `perfil`, `status`, `criado_em`) VALUES
(4, 'Eduardo Lopessssss', '13889190952', 'edulopes@gmail.com', '$2y$10$HXMg7v/OvRIZARwK1R6ur.DEZ6b/1w3DsfuYRg1Dv5jKm5j6KaagK', 'admin', 'ativo', '2026-10-22 11:30:15');


ALTER TABLE `paradas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_paradas_rota_ordem` (`rota_id`,`ordem`);


ALTER TABLE `rotas`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `sensores`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `trens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `fk_trem_usuario` (`usuario_responsavel_id`),
  ADD KEY `fk_trens_rotas` (`rota_id`),
  ADD KEY `fk_trens_proxima_parada` (`proxima_parada_id`);


ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);


ALTER TABLE `paradas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;


ALTER TABLE `rotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;


ALTER TABLE `sensores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `trens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;


ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;


ALTER TABLE `paradas`
  ADD CONSTRAINT `fk_paradas_rotas` FOREIGN KEY (`rota_id`) REFERENCES `rotas` (`id`) ON DELETE CASCADE;


ALTER TABLE `trens`
  ADD CONSTRAINT `fk_trem_usuario` FOREIGN KEY (`usuario_responsavel_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_trens_proxima_parada` FOREIGN KEY (`proxima_parada_id`) REFERENCES `paradas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_trens_rotas` FOREIGN KEY (`rota_id`) REFERENCES `rotas` (`id`) ON DELETE SET NULL;
COMMIT;


