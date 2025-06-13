-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 13, 2025 at 01:52 PM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u835980051_ava`
--

-- --------------------------------------------------------

--
-- Table structure for table `acesso_perfis`
--

CREATE TABLE `acesso_perfis` (
  `id` int(11) NOT NULL,
  `perfil_id` int(11) NOT NULL,
  `recurso` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `especialidade_id` int(11) DEFAULT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `avaliacoes_criterios`
--

CREATE TABLE `avaliacoes_criterios` (
  `id` int(11) NOT NULL,
  `pergunta_id` int(11) DEFAULT NULL,
  `descricao` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `data_exclusao` datetime NOT NULL,
  `data_criacao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `avaliacoes_geradas`
--

CREATE TABLE `avaliacoes_geradas` (
  `id` int(11) NOT NULL,
  `modelo_id` int(11) NOT NULL,
  `preceptor_id` int(11) NOT NULL,
  `residente_id` int(11) NOT NULL,
  `campo_estagio_id` int(11) NOT NULL,
  `inicio_avaliacao` datetime NOT NULL,
  `fim_avaliacao` datetime NOT NULL,
  `nivel_especialidade` enum('R1','R2','R3','R4','R5') NOT NULL,
  `ano_letivo` int(11) NOT NULL,
  `mes_referencia` int(11) NOT NULL,
  `observacoes_preceptor` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `data_criacao` timestamp NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `avaliacoes_perguntas`
--

CREATE TABLE `avaliacoes_perguntas` (
  `id` int(11) NOT NULL,
  `avaliacao_id` int(11) NOT NULL,
  `titulo` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `data_criacao` datetime NOT NULL DEFAULT current_timestamp(),
  `data_exclusao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `avaliacoes_respostas`
--

CREATE TABLE `avaliacoes_respostas` (
  `id` int(11) NOT NULL,
  `avaliacao_gerada_id` int(11) NOT NULL,
  `pergunta_id` int(11) NOT NULL,
  `criterio_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `turma_id` int(11) DEFAULT NULL,
  `resposta` text DEFAULT NULL,
  `nota_atribuida` decimal(5,2) DEFAULT NULL,
  `data_avaliacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campos_estagio`
--

CREATE TABLE `campos_estagio` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `data_criacao` timestamp NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `data_exclusao` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documentos_residente`
--

CREATE TABLE `documentos_residente` (
  `id` int(11) NOT NULL,
  `residente_id` int(11) DEFAULT NULL,
  `tipo_documento` enum('RG','CPF','Diploma','Vacina','Contrato','Outro') DEFAULT NULL,
  `nome_arquivo` varchar(255) DEFAULT NULL,
  `caminho_arquivo` varchar(255) DEFAULT NULL,
  `data_upload` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `editais`
--

CREATE TABLE `editais` (
  `id` int(11) NOT NULL,
  `numero` varchar(50) DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `descricao` text NOT NULL,
  `ano` int(11) DEFAULT NULL,
  `tipo` varchar(100) DEFAULT NULL,
  `data_abertura` date DEFAULT NULL,
  `data_fechamento` date DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `edital_arquivos`
--

CREATE TABLE `edital_arquivos` (
  `id` int(11) NOT NULL,
  `edital_id` int(11) DEFAULT NULL,
  `nome_arquivo` varchar(255) DEFAULT NULL,
  `caminho_arquivo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `edital_especialidades`
--

CREATE TABLE `edital_especialidades` (
  `edital_id` int(11) NOT NULL,
  `especialidade_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `especialidades`
--

CREATE TABLE `especialidades` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `duracao_anos` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `matriculas`
--

CREATE TABLE `matriculas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `turma_id` int(11) NOT NULL,
  `data_matricula` date DEFAULT curdate(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `data_exclusao` datetime DEFAULT NULL,
  `data_criacao` timestamp NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `edital_origem_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `perfis`
--

CREATE TABLE `perfis` (
  `id` int(11) NOT NULL,
  `nome` enum('admin','secretaria','residente','preceptor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `preceptores`
--

CREATE TABLE `preceptores` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `data_inicio` date DEFAULT NULL,
  `especialidade_id` int(11) DEFAULT NULL,
  `coordenador` enum('Sim','Não') DEFAULT 'Não',
  `data_cadastro` datetime DEFAULT current_timestamp(),
  `data_alteracao` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `preceptor_campoestagio`
--

CREATE TABLE `preceptor_campoestagio` (
  `id` int(11) NOT NULL,
  `preceptor_id` int(11) NOT NULL,
  `campo_estagio_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `data_criacao` timestamp NULL DEFAULT current_timestamp(),
  `data_alteracao` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `data_exclusao` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `residentes`
--

CREATE TABLE `residentes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nome_completo` varchar(255) NOT NULL,
  `nome_pai` varchar(255) DEFAULT NULL,
  `nome_mae` varchar(255) DEFAULT NULL,
  `estado_civil` varchar(50) DEFAULT NULL,
  `nome_conjuge` varchar(255) DEFAULT NULL,
  `nacionalidade` varchar(100) DEFAULT NULL,
  `naturalidade` varchar(100) DEFAULT NULL,
  `rg` varchar(20) DEFAULT NULL,
  `orgao_expedidor` varchar(50) DEFAULT NULL,
  `data_expedicao` date DEFAULT NULL,
  `crm` varchar(50) DEFAULT NULL,
  `pis_pasep` varchar(50) DEFAULT NULL,
  `titulo_eleitor` varchar(50) DEFAULT NULL,
  `zona` varchar(10) DEFAULT NULL,
  `secao` varchar(10) DEFAULT NULL,
  `cidade_eleitor` varchar(100) DEFAULT NULL,
  `reservista` varchar(100) DEFAULT NULL,
  `curso` varchar(150) DEFAULT NULL,
  `nome_faculdade` varchar(255) DEFAULT NULL,
  `sigla_faculdade` varchar(50) DEFAULT NULL,
  `cidade_faculdade` varchar(100) DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_termino` date DEFAULT NULL,
  `nome_banco` varchar(100) DEFAULT NULL,
  `numero_banco` varchar(10) DEFAULT NULL,
  `agencia` varchar(20) DEFAULT NULL,
  `conta` varchar(30) DEFAULT NULL,
  `sistema_abo` varchar(5) DEFAULT NULL,
  `fator_rh` varchar(5) DEFAULT NULL,
  `particularidades` text DEFAULT NULL,
  `auxilio_moradia` enum('SIM','NÃO') DEFAULT NULL,
  `outras_particularidades` text DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `altura` decimal(4,2) DEFAULT NULL,
  `situacao_status` varchar(50) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT current_timestamp(),
  `data_atualizacao` datetime DEFAULT NULL,
  `cor_etnica` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `secretarias`
--

CREATE TABLE `secretarias` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `turmas`
--

CREATE TABLE `turmas` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `especialidade_id` int(11) NOT NULL,
  `preceptor_id` int(11) DEFAULT NULL,
  `data_abertura` date NOT NULL,
  `data_fechamento` date NOT NULL,
  `descricao` text DEFAULT NULL,
  `carga_horaria` int(11) NOT NULL,
  `vagas` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `data_exclusao` datetime DEFAULT NULL,
  `data_criacao` timestamp NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `ativo` tinyint(1) DEFAULT 2,
  `criado_em` datetime DEFAULT current_timestamp(),
  `cancelado_em` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuarios_dados`
--

CREATE TABLE `usuarios_dados` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `imagem_perfil` varchar(255) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `sexo` enum('M','F','Outro') DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `endereco` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuario_perfis`
--

CREATE TABLE `usuario_perfis` (
  `usuario_id` int(11) NOT NULL,
  `perfil_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acesso_perfis`
--
ALTER TABLE `acesso_perfis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `perfil_id` (`perfil_id`);

--
-- Indexes for table `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `avaliacoes_criterios`
--
ALTER TABLE `avaliacoes_criterios`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `avaliacoes_geradas`
--
ALTER TABLE `avaliacoes_geradas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `modelo_id` (`modelo_id`),
  ADD KEY `residente_id` (`residente_id`),
  ADD KEY `campo_estagio_id` (`campo_estagio_id`),
  ADD KEY `fk_avaliacoes_preceptor` (`preceptor_id`);

--
-- Indexes for table `avaliacoes_perguntas`
--
ALTER TABLE `avaliacoes_perguntas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `avaliacoes_respostas`
--
ALTER TABLE `avaliacoes_respostas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `avaliacao_id` (`avaliacao_gerada_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `campos_estagio`
--
ALTER TABLE `campos_estagio`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documentos_residente`
--
ALTER TABLE `documentos_residente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `residente_id` (`residente_id`);

--
-- Indexes for table `editais`
--
ALTER TABLE `editais`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edital_arquivos`
--
ALTER TABLE `edital_arquivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `edital_id` (`edital_id`);

--
-- Indexes for table `edital_especialidades`
--
ALTER TABLE `edital_especialidades`
  ADD PRIMARY KEY (`edital_id`,`especialidade_id`),
  ADD KEY `especialidade_id` (`especialidade_id`);

--
-- Indexes for table `especialidades`
--
ALTER TABLE `especialidades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `matriculas`
--
ALTER TABLE `matriculas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_usuario_turma` (`usuario_id`,`turma_id`),
  ADD KEY `fk_matriculas_turma` (`turma_id`),
  ADD KEY `fk_edital_origem` (`edital_origem_id`);

--
-- Indexes for table `perfis`
--
ALTER TABLE `perfis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Indexes for table `preceptores`
--
ALTER TABLE `preceptores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_preceptor_usuario` (`usuario_id`),
  ADD KEY `fk_preceptor_especialidade` (`especialidade_id`);

--
-- Indexes for table `preceptor_campoestagio`
--
ALTER TABLE `preceptor_campoestagio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_preceptor_campo` (`preceptor_id`,`campo_estagio_id`),
  ADD KEY `campo_estagio_id` (`campo_estagio_id`);

--
-- Indexes for table `residentes`
--
ALTER TABLE `residentes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `secretarias`
--
ALTER TABLE `secretarias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `turmas`
--
ALTER TABLE `turmas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `especialidade_id` (`especialidade_id`),
  ADD KEY `preceptor_id` (`preceptor_id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `usuarios_dados`
--
ALTER TABLE `usuarios_dados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `usuario_perfis`
--
ALTER TABLE `usuario_perfis`
  ADD PRIMARY KEY (`usuario_id`,`perfil_id`),
  ADD KEY `perfil_id` (`perfil_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acesso_perfis`
--
ALTER TABLE `acesso_perfis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `avaliacoes_criterios`
--
ALTER TABLE `avaliacoes_criterios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `avaliacoes_geradas`
--
ALTER TABLE `avaliacoes_geradas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `avaliacoes_perguntas`
--
ALTER TABLE `avaliacoes_perguntas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `avaliacoes_respostas`
--
ALTER TABLE `avaliacoes_respostas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `campos_estagio`
--
ALTER TABLE `campos_estagio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documentos_residente`
--
ALTER TABLE `documentos_residente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `editais`
--
ALTER TABLE `editais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `edital_arquivos`
--
ALTER TABLE `edital_arquivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `especialidades`
--
ALTER TABLE `especialidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `matriculas`
--
ALTER TABLE `matriculas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `perfis`
--
ALTER TABLE `perfis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `preceptores`
--
ALTER TABLE `preceptores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `preceptor_campoestagio`
--
ALTER TABLE `preceptor_campoestagio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `residentes`
--
ALTER TABLE `residentes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `secretarias`
--
ALTER TABLE `secretarias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `turmas`
--
ALTER TABLE `turmas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usuarios_dados`
--
ALTER TABLE `usuarios_dados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `acesso_perfis`
--
ALTER TABLE `acesso_perfis`
  ADD CONSTRAINT `acesso_perfis_ibfk_1` FOREIGN KEY (`perfil_id`) REFERENCES `perfis` (`id`);

--
-- Constraints for table `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `avaliacoes_ibfk_1` FOREIGN KEY (`especialidade_id`) REFERENCES `especialidades` (`id`);

--
-- Constraints for table `avaliacoes_geradas`
--
ALTER TABLE `avaliacoes_geradas`
  ADD CONSTRAINT `avaliacoes_geradas_ibfk_1` FOREIGN KEY (`modelo_id`) REFERENCES `avaliacoes` (`id`),
  ADD CONSTRAINT `avaliacoes_geradas_ibfk_3` FOREIGN KEY (`residente_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `avaliacoes_geradas_ibfk_4` FOREIGN KEY (`campo_estagio_id`) REFERENCES `campos_estagio` (`id`),
  ADD CONSTRAINT `fk_avaliacoes_preceptor` FOREIGN KEY (`preceptor_id`) REFERENCES `usuarios` (`id`);

--
-- Constraints for table `avaliacoes_perguntas`
--
ALTER TABLE `avaliacoes_perguntas`
  ADD CONSTRAINT `avaliacoes_perguntas_ibfk_1` FOREIGN KEY (`avaliacao_id`) REFERENCES `avaliacoes` (`id`);

--
-- Constraints for table `avaliacoes_respostas`
--
ALTER TABLE `avaliacoes_respostas`
  ADD CONSTRAINT `avaliacoes_respostas_ibfk_2` FOREIGN KEY (`pergunta_id`) REFERENCES `avaliacoes_perguntas` (`id`),
  ADD CONSTRAINT `avaliacoes_respostas_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `avaliacoes_respostas_ibfk_4` FOREIGN KEY (`preceptor_id`) REFERENCES `usuarios` (`id`);

--
-- Constraints for table `documentos_residente`
--
ALTER TABLE `documentos_residente`
  ADD CONSTRAINT `documentos_residente_ibfk_1` FOREIGN KEY (`residente_id`) REFERENCES `residentes` (`id`);

--
-- Constraints for table `edital_arquivos`
--
ALTER TABLE `edital_arquivos`
  ADD CONSTRAINT `edital_arquivos_ibfk_1` FOREIGN KEY (`edital_id`) REFERENCES `editais` (`id`);

--
-- Constraints for table `edital_especialidades`
--
ALTER TABLE `edital_especialidades`
  ADD CONSTRAINT `edital_especialidades_ibfk_1` FOREIGN KEY (`edital_id`) REFERENCES `editais` (`id`),
  ADD CONSTRAINT `edital_especialidades_ibfk_2` FOREIGN KEY (`especialidade_id`) REFERENCES `especialidades` (`id`);

--
-- Constraints for table `matriculas`
--
ALTER TABLE `matriculas`
  ADD CONSTRAINT `fk_edital_origem` FOREIGN KEY (`edital_origem_id`) REFERENCES `editais` (`id`),
  ADD CONSTRAINT `fk_matriculas_turma` FOREIGN KEY (`turma_id`) REFERENCES `turmas` (`id`),
  ADD CONSTRAINT `fk_matriculas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Constraints for table `preceptores`
--
ALTER TABLE `preceptores`
  ADD CONSTRAINT `fk_preceptor_especialidade` FOREIGN KEY (`especialidade_id`) REFERENCES `especialidades` (`id`),
  ADD CONSTRAINT `fk_preceptor_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Constraints for table `preceptor_campoestagio`
--
ALTER TABLE `preceptor_campoestagio`
  ADD CONSTRAINT `preceptor_campoestagio_ibfk_1` FOREIGN KEY (`preceptor_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `preceptor_campoestagio_ibfk_2` FOREIGN KEY (`campo_estagio_id`) REFERENCES `campos_estagio` (`id`);

--
-- Constraints for table `residentes`
--
ALTER TABLE `residentes`
  ADD CONSTRAINT `residentes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Constraints for table `secretarias`
--
ALTER TABLE `secretarias`
  ADD CONSTRAINT `secretarias_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Constraints for table `turmas`
--
ALTER TABLE `turmas`
  ADD CONSTRAINT `turmas_ibfk_1` FOREIGN KEY (`especialidade_id`) REFERENCES `especialidades` (`id`),
  ADD CONSTRAINT `turmas_ibfk_2` FOREIGN KEY (`preceptor_id`) REFERENCES `usuarios` (`id`);

--
-- Constraints for table `usuarios_dados`
--
ALTER TABLE `usuarios_dados`
  ADD CONSTRAINT `usuarios_dados_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Constraints for table `usuario_perfis`
--
ALTER TABLE `usuario_perfis`
  ADD CONSTRAINT `usuario_perfis_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `usuario_perfis_ibfk_2` FOREIGN KEY (`perfil_id`) REFERENCES `perfis` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
