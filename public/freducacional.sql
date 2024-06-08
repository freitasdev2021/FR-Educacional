-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 08/06/2024 às 18:24
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `freducacional`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `afastados`
--

CREATE TABLE `afastados` (
  `id` int(11) NOT NULL,
  `IDInativo` int(11) NOT NULL,
  `Justificativa` varchar(250) NOT NULL,
  `INISuspensao` date DEFAULT NULL,
  `TERSuspensao` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `alocacoes`
--

CREATE TABLE `alocacoes` (
  `IDEscola` int(11) NOT NULL,
  `IDProfissional` int(11) NOT NULL,
  `INITurno` time NOT NULL,
  `TERTurno` time NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `TPProfissional` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `alocacoes`
--

INSERT INTO `alocacoes` (`IDEscola`, `IDProfissional`, `INITurno`, `TERTurno`, `created_at`, `updated_at`, `TPProfissional`) VALUES
(1, 1, '12:39:00', '11:39:00', '2024-05-24', '2024-05-24', 'PROF'),
(4, 1, '04:39:00', '05:39:00', '2024-05-24', '2024-05-24', 'PROF'),
(3, 1, '04:39:00', '05:39:00', '2024-05-24', '2024-05-24', 'PEDA'),
(1, 2, '12:39:00', '11:39:00', '2024-05-24', '2024-05-24', 'PROF'),
(3, 2, '04:39:00', '02:39:00', '2024-05-24', '2024-05-24', 'PROF');

-- --------------------------------------------------------

--
-- Estrutura para tabela `alocacoes_disciplinas`
--

CREATE TABLE `alocacoes_disciplinas` (
  `IDEscola` int(11) NOT NULL,
  `IDDisciplina` int(11) NOT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `alocacoes_disciplinas`
--

INSERT INTO `alocacoes_disciplinas` (`IDEscola`, `IDDisciplina`, `updated_at`, `created_at`) VALUES
(1, 2, '2024-06-05', '2024-06-05'),
(1, 3, '2024-06-05', '2024-06-05'),
(1, 1, '2024-06-05', '2024-06-05'),
(4, 1, '2024-06-05', '2024-06-05');

-- --------------------------------------------------------

--
-- Estrutura para tabela `alteracoes_situacao`
--

CREATE TABLE `alteracoes_situacao` (
  `id` int(11) NOT NULL,
  `IDAluno` int(11) NOT NULL,
  `STAluno` int(11) NOT NULL,
  `Justificativa` varchar(250) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `alteracoes_situacao`
--

INSERT INTO `alteracoes_situacao` (`id`, `IDAluno`, `STAluno`, `Justificativa`, `created_at`, `updated_at`) VALUES
(1, 1, 0, 'Pulou o Muro pra correr atras de pipa', '2024-06-07', '2024-06-07'),
(2, 1, 0, 'Terminou os estudos', '2024-06-07', '2024-06-07'),
(3, 1, 0, 'Terminou os estudos', '2024-06-07', '2024-06-07'),
(4, 1, 4, 'egressado kkk', '2024-06-07', '2024-06-07'),
(5, 1, 2, 'Desistiu de tudo', '2024-06-07', '2024-06-07'),
(6, 1, 0, 'Voltou tudo', '2024-06-07', '2024-06-07');

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos`
--

CREATE TABLE `alunos` (
  `id` int(11) NOT NULL,
  `IDMatricula` int(11) NOT NULL,
  `STAluno` int(11) DEFAULT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `IDTurma` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `alunos`
--

INSERT INTO `alunos` (`id`, `IDMatricula`, `STAluno`, `created_at`, `updated_at`, `IDTurma`) VALUES
(1, 1, 0, '2024-06-05', '2024-06-07', 4),
(2, 2, 0, '2024-06-05', '2024-06-05', 4),
(3, 3, 0, '2024-06-06', '2024-06-06', 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `atividades`
--

CREATE TABLE `atividades` (
  `id` int(11) NOT NULL,
  `IDTurma` int(11) NOT NULL,
  `IDDisciplina` int(11) NOT NULL,
  `DTAvaliacao` date NOT NULL,
  `TPConteudo` varchar(50) NOT NULL,
  `DSAtividade` varchar(250) NOT NULL,
  `Pontuacao` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `atividades_atribuicoes`
--

CREATE TABLE `atividades_atribuicoes` (
  `id` int(11) NOT NULL,
  `IDAluno` int(11) NOT NULL,
  `DTEntrega` date NOT NULL,
  `Realizado` int(11) NOT NULL,
  `Feedback` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `atividades_atribuicoes_ead`
--

CREATE TABLE `atividades_atribuicoes_ead` (
  `id` int(11) NOT NULL,
  `IDAluno` int(11) NOT NULL,
  `DTEntrega` date NOT NULL,
  `Realizado` int(11) NOT NULL,
  `Feedback` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `atividades_ead`
--

CREATE TABLE `atividades_ead` (
  `id` int(11) NOT NULL,
  `IDTurma` int(11) NOT NULL,
  `IDDisciplina` int(11) NOT NULL,
  `DTAvaliacao` date NOT NULL,
  `TPConteudo` varchar(50) NOT NULL,
  `DSAtividade` varchar(250) NOT NULL,
  `Pontuacao` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `aulas`
--

CREATE TABLE `aulas` (
  `id` int(11) NOT NULL,
  `IDEscola` int(11) NOT NULL,
  `IDTurma` int(11) NOT NULL,
  `IDDisciplina` int(11) NOT NULL,
  `IDProfessor` int(11) NOT NULL,
  `INIAula` datetime NOT NULL,
  `TERAula` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `aulas_ead`
--

CREATE TABLE `aulas_ead` (
  `id` int(11) NOT NULL,
  `IDTurma` int(11) NOT NULL,
  `Descricao_da_Aula` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `auxiliares`
--

CREATE TABLE `auxiliares` (
  `id` int(11) NOT NULL,
  `IDEscola` int(11) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `Nascimento` date NOT NULL,
  `Admissao` date NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Celular` varchar(11) NOT NULL,
  `TerminoContrato` date DEFAULT NULL,
  `CEP` varchar(8) NOT NULL,
  `Rua` varchar(50) NOT NULL,
  `UF` varchar(2) NOT NULL,
  `Cidade` varchar(50) NOT NULL,
  `Bairro` varchar(500) DEFAULT NULL,
  `Numero` int(11) DEFAULT NULL,
  `Ativo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `calendario`
--

CREATE TABLE `calendario` (
  `id` int(11) NOT NULL,
  `IDOrg` int(11) NOT NULL,
  `INIAno` date NOT NULL,
  `TERAno` date NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `calendario`
--

INSERT INTO `calendario` (`id`, `IDOrg`, `INIAno`, `TERAno`, `created_at`, `updated_at`) VALUES
(1, 1, '2024-02-01', '2024-12-07', '2024-05-25', '2024-05-25');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cardapio`
--

CREATE TABLE `cardapio` (
  `id` int(11) NOT NULL,
  `IDEscola` int(11) NOT NULL,
  `Dia` date NOT NULL,
  `Turno` varchar(20) NOT NULL,
  `Descricao` text NOT NULL,
  `Foto` text NOT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cardapio`
--

INSERT INTO `cardapio` (`id`, `IDEscola`, `Dia`, `Turno`, `Descricao`, `Foto`, `updated_at`, `created_at`) VALUES
(1, 2, '2024-05-30', 'Manhã', 'Arroz com Feijão e Suco', '', '2024-05-29', '2024-05-29');

-- --------------------------------------------------------

--
-- Estrutura para tabela `comentarios_planual`
--

CREATE TABLE `comentarios_planual` (
  `id` int(11) NOT NULL,
  `IDPlanejamentoAnual` int(11) NOT NULL,
  `IDPedagogo` int(11) NOT NULL,
  `Feedback` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `comentarios_plsemanal`
--

CREATE TABLE `comentarios_plsemanal` (
  `id` int(11) NOT NULL,
  `IDPlanejamentoSemanal` int(11) NOT NULL,
  `IDPedagogo` int(11) NOT NULL,
  `Feedback` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `diretores`
--

CREATE TABLE `diretores` (
  `id` int(11) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `Nascimento` date NOT NULL,
  `Admissao` date NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Celular` varchar(11) NOT NULL,
  `TerminoContrato` date DEFAULT NULL,
  `CEP` varchar(8) NOT NULL,
  `Rua` varchar(50) NOT NULL,
  `UF` varchar(2) NOT NULL,
  `Cidade` varchar(50) NOT NULL,
  `Bairro` varchar(50) DEFAULT NULL,
  `Numero` int(11) DEFAULT NULL,
  `IDEscola` int(11) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `diretores`
--

INSERT INTO `diretores` (`id`, `Nome`, `Nascimento`, `Admissao`, `Email`, `Celular`, `TerminoContrato`, `CEP`, `Rua`, `UF`, `Cidade`, `Bairro`, `Numero`, `IDEscola`, `created_at`, `updated_at`) VALUES
(1, 'gallinheiro', '2024-05-10', '2024-05-25', 'gallicu@gmail.com', '31932342424', '2024-05-17', '35160108', 'Rua Hungria', 'MG', 'Ipatinga', 'Cariru', 234, 5, '2024-05-20', '2024-05-20'),
(2, '423534', '2024-05-25', '2024-05-18', 'dfsfs@gmail.com', '32424234242', '2024-05-16', '35160208', 'Avenida Vinte e Seis de Outubro', 'MG', 'Ipatinga', 'Bela Vista', 2442, 4, '2024-05-20', '2024-05-20'),
(3, 'PedagogoTest', '2024-05-18', '2024-05-23', 'sdfsdf@gmail.com', '42424242', '2024-05-16', '35160208', 'Avenida Vinte e Seis de Outubro', 'MG', 'Ipatinga', 'Bela Vista', 2014, 0, '2024-05-22', '2024-05-22'),
(4, 'sdfsdfsd', '2024-05-09', '2024-05-16', 'dsfsdfsd@gmai.com', '234242423', '2024-06-01', '35160208', 'Avenida Vinte e Seis de Outubro', 'MG', 'Ipatinga', 'Bela Vista', 2014, 0, '2024-05-22', '2024-05-22'),
(5, 'DIRETOR TESTE', '2024-06-27', '2024-06-07', 'diretor@gmail.com', '31983086235', '2024-07-04', '35160208', 'Avenida Vinte e Seis de Outubro', 'MG', 'Ipatinga', 'Bela Vista', 2014, 1, '2024-06-03', '2024-06-03');

-- --------------------------------------------------------

--
-- Estrutura para tabela `disciplinas`
--

CREATE TABLE `disciplinas` (
  `id` int(11) NOT NULL,
  `NMDisciplina` varchar(30) NOT NULL,
  `Obrigatoria` varchar(3) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `disciplinas`
--

INSERT INTO `disciplinas` (`id`, `NMDisciplina`, `Obrigatoria`, `created_at`, `updated_at`) VALUES
(1, 'Biologia', 'Sim', '2024-05-27', '2024-06-05'),
(2, 'Matemática', 'Sim', '2024-06-05', '2024-06-05'),
(3, 'Português', 'Sim', '2024-06-05', '2024-06-05');

-- --------------------------------------------------------

--
-- Estrutura para tabela `dissertativas_ead`
--

CREATE TABLE `dissertativas_ead` (
  `id` int(11) NOT NULL,
  `IDAtividade` int(11) NOT NULL,
  `Enunciado` varchar(50) NOT NULL,
  `Resposta` text DEFAULT NULL,
  `Feedback` varchar(250) DEFAULT NULL,
  `Resultado` int(11) NOT NULL,
  `Total` float NOT NULL,
  `Nota` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `escolas`
--

CREATE TABLE `escolas` (
  `id` int(11) NOT NULL,
  `IDOrg` int(11) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `CEP` varchar(8) NOT NULL,
  `Rua` varchar(50) NOT NULL,
  `Bairro` varchar(50) NOT NULL,
  `Cidade` varchar(50) NOT NULL,
  `Numero` int(11) NOT NULL,
  `UF` varchar(2) NOT NULL,
  `Telefone` varchar(11) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `QTVagas` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `escolas`
--

INSERT INTO `escolas` (`id`, `IDOrg`, `Nome`, `CEP`, `Rua`, `Bairro`, `Cidade`, `Numero`, `UF`, `Telefone`, `Email`, `QTVagas`, `created_at`, `updated_at`) VALUES
(1, 1, 'Dr Ovidio', '35160208', 'Avenida Vinte e Seis de Outubro', 'Bela Vista', 'Ipatinga', 40, 'MG', '31983086235', 'testeescola@gmail.com', 600, '2024-05-15 19:09:34', '2024-05-16 06:09:58'),
(2, 1, 'Escola Estadual Dom Helvecio', '35160251', 'Rua Campos Sales', 'Imbaúbas', 'Ipatinga', 500, 'MG', '3138233513', 'eedomhelvecio@gmail.com', 804, '2024-05-16 19:03:13', '2024-05-16 19:03:13'),
(3, 1, 'Escola Municipal Padre Cicero de Castro', '35160225', 'Av. Fernando de Noronha', 'Bom Retiro', 'Ipatinga', 490, 'MG', '3138298454', 'padrecicero@gmail.com', 1204, '2024-05-17 06:08:17', '2024-05-18 19:05:11'),
(4, 1, 'Polivalente', '35170188', 'Rua Maria Soares de Oliveira', 'Belvedere', 'Coronel Fabriciano', 134, 'MG', '31382314214', 'polivalente@gmail.com', 604, '2024-05-19 00:15:03', '2024-05-19 01:41:26'),
(5, 1, 'Raulino', '35160100', 'Rua Estados Unidos', 'Cariru', 'Ipatinga', 210, 'MG', '31983086235', 'eofreitasmane@gmail.com', 28, '2024-05-19 01:40:24', '2024-05-24 21:26:40'),
(6, 1, 'Escola Sesi', '35160208', 'Avenida Vinte e Seis de Outubro', 'Bela Vista', 'Ipatinga', 77, 'MG', '31983343434', 'sesi@gmail.com', 904, '2024-06-01 19:53:50', '2024-06-01 19:53:50');

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoque`
--

CREATE TABLE `estoque` (
  `id` int(11) NOT NULL,
  `IDEscola` int(11) NOT NULL,
  `Quantidade` float NOT NULL,
  `TPUnidade` varchar(2) NOT NULL,
  `Vencimento` date DEFAULT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `Item` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `estoque`
--

INSERT INTO `estoque` (`id`, `IDEscola`, `Quantidade`, `TPUnidade`, `Vencimento`, `created_at`, `updated_at`, `Item`) VALUES
(1, 2, 4, 'UN', '2024-07-23', '2024-05-29', '2024-05-29', 'Saco de Arroz');

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoque_movimentacao`
--

CREATE TABLE `estoque_movimentacao` (
  `id` int(11) NOT NULL,
  `IDEstoque` int(11) NOT NULL,
  `TPMovimentacao` int(11) NOT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL,
  `Quantidade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `estoque_movimentacao`
--

INSERT INTO `estoque_movimentacao` (`id`, `IDEstoque`, `TPMovimentacao`, `updated_at`, `created_at`, `Quantidade`) VALUES
(1, 1, 0, '2024-05-29', '2024-05-29', 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `DSEvento` varchar(250) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `eventos`
--

INSERT INTO `eventos` (`id`, `DSEvento`, `created_at`, `updated_at`) VALUES
(1, 'Reuniao de Pais', '2024-05-24', '2024-05-24');

-- --------------------------------------------------------

--
-- Estrutura para tabela `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `faltas_justificadas`
--

CREATE TABLE `faltas_justificadas` (
  `id` int(11) NOT NULL,
  `IDPessoa` int(11) NOT NULL,
  `Justificativa` varchar(250) NOT NULL,
  `DTFalta` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `faltas_justificadas_profissional`
--

CREATE TABLE `faltas_justificadas_profissional` (
  `id` int(11) NOT NULL,
  `IDPessoa` int(11) NOT NULL,
  `Justificativa` varchar(250) NOT NULL,
  `DTFalta` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `feedback_transferencias`
--

CREATE TABLE `feedback_transferencias` (
  `id` int(11) NOT NULL,
  `Feedback` varchar(250) NOT NULL,
  `IDTransferencia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ferias_alunos`
--

CREATE TABLE `ferias_alunos` (
  `id` int(11) NOT NULL,
  `DTInicio` date NOT NULL,
  `DTTermino` date NOT NULL,
  `IDEscola` int(11) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `ferias_alunos`
--

INSERT INTO `ferias_alunos` (`id`, `DTInicio`, `DTTermino`, `IDEscola`, `created_at`, `updated_at`) VALUES
(1, '2024-05-30', '2024-05-16', 4, '2024-05-24', '2024-05-25'),
(2, '2024-05-10', '2024-05-30', 5, '2024-05-25', '2024-05-25'),
(3, '0000-00-00', '0000-00-00', 2, '2024-05-25', '2024-05-25'),
(4, '2024-05-08', '2024-05-31', 1, '2024-05-25', '2024-05-25'),
(5, '2024-05-11', '2024-05-31', 1, '2024-05-25', '2024-05-25'),
(6, '2024-05-09', '2024-05-14', 1, '2024-05-25', '2024-05-25');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ferias_profissionais`
--

CREATE TABLE `ferias_profissionais` (
  `id` int(11) NOT NULL,
  `DTInicio` date NOT NULL,
  `DTTermino` date NOT NULL,
  `IDEscola` int(11) NOT NULL,
  `IDProfissional` int(11) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `ferias_profissionais`
--

INSERT INTO `ferias_profissionais` (`id`, `DTInicio`, `DTTermino`, `IDEscola`, `IDProfissional`, `created_at`, `updated_at`) VALUES
(1, '2024-05-15', '2024-05-25', 4, 2, '2024-05-26', '2024-05-26');

-- --------------------------------------------------------

--
-- Estrutura para tabela `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `justificativa_alteracoes`
--

CREATE TABLE `justificativa_alteracoes` (
  `id` int(11) NOT NULL,
  `IDAluno` int(11) NOT NULL,
  `Justificativa` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `matriculas`
--

CREATE TABLE `matriculas` (
  `id` int(11) NOT NULL,
  `AnexoRG` varchar(100) NOT NULL,
  `CResidencia` varchar(100) NOT NULL,
  `Historico` varchar(100) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `CPF` varchar(11) NOT NULL,
  `RG` varchar(9) NOT NULL,
  `CEP` varchar(8) NOT NULL,
  `Rua` varchar(50) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Celular` varchar(11) NOT NULL,
  `UF` varchar(2) NOT NULL,
  `Cidade` varchar(50) NOT NULL,
  `BolsaFamilia` int(11) NOT NULL,
  `Alergia` int(11) NOT NULL,
  `Transporte` int(11) NOT NULL,
  `NEE` int(11) NOT NULL,
  `AMedico` int(11) NOT NULL,
  `APsicologico` int(11) NOT NULL,
  `Aprovado` int(11) NOT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL,
  `Nascimento` date NOT NULL,
  `Foto` varchar(100) NOT NULL,
  `Bairro` varchar(10) NOT NULL,
  `Numero` int(11) NOT NULL,
  `CDPasta` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `matriculas`
--

INSERT INTO `matriculas` (`id`, `AnexoRG`, `CResidencia`, `Historico`, `Nome`, `CPF`, `RG`, `CEP`, `Rua`, `Email`, `Celular`, `UF`, `Cidade`, `BolsaFamilia`, `Alergia`, `Transporte`, `NEE`, `AMedico`, `APsicologico`, `Aprovado`, `updated_at`, `created_at`, `Nascimento`, `Foto`, `Bairro`, `Numero`, `CDPasta`) VALUES
(1, 'curriculo 2024 (2).pdf', 'curriculo.pdf', 'Proposta comercial FR tecnologia.pdf', 'Freitinha', '08901129671', '20896779', '35160208', 'Avenida Vinte e Seis de Outubro', 'maxhenrique308@gmail.com', '31983908623', 'MG', 'Ipatinga', 0, 0, 0, 0, 0, 0, 1, '2024-06-05', '2024-06-05', '2001-02-16', 'freitinha.png', 'Bela Vista', 2014, 62971084206),
(2, 'daeonline.pdf', 'Cupom baixado (2).pdf', 'curriculo.pdf', 'Freitão', '08901129671', '20896779', '35160208', 'Avenida Vinte e Seis de Outubro', 'maxhenriquee308@gmail.com', '45453453453', 'MG', 'Ipatinga', 1, 1, 1, 1, 1, 1, 1, '2024-06-06', '2024-06-05', '2001-02-16', 'maiordeipatingalinkedin.jpeg', 'Bela Vista', 0, 45561104601),
(3, '923f3ef6dbd04ae49677ac43bd779fce-6-17.pdf', 'testeclick.pdf', 'LISTA_EDUCAR_ALUNOS-1_compressed.pdf', 'fsdfsdfsd', '23424234234', '44234324', '35171026', 'Rua Tungstênio', 'sdfsd@gmail.com', '23322342342', 'MG', 'Coronel Fabriciano', 1, 1, 1, 1, 1, 1, 1, '2024-06-06', '2024-06-06', '2024-06-11', 'XT3B3773.jpg', 'Recanto Ve', 234, 62696647991);

-- --------------------------------------------------------

--
-- Estrutura para tabela `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `motoristas`
--

CREATE TABLE `motoristas` (
  `id` int(11) NOT NULL,
  `IDOrganizacao` int(11) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `Nascimento` date NOT NULL,
  `Admissao` date NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Celular` varchar(11) NOT NULL,
  `TerminoContrato` date DEFAULT NULL,
  `CEP` varchar(8) NOT NULL,
  `Rua` varchar(50) NOT NULL,
  `UF` varchar(2) NOT NULL,
  `Cidade` varchar(50) NOT NULL,
  `Bairro` varchar(500) DEFAULT NULL,
  `Numero` int(11) DEFAULT NULL,
  `Ativo` int(11) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `motoristas`
--

INSERT INTO `motoristas` (`id`, `IDOrganizacao`, `Nome`, `Nascimento`, `Admissao`, `Email`, `Celular`, `TerminoContrato`, `CEP`, `Rua`, `UF`, `Cidade`, `Bairro`, `Numero`, `Ativo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Eusébioo', '2024-05-07', '2024-05-24', 'Maxhenrique307@gmail.com', '31983242343', '2024-05-25', '35160208', 'Avenida Vinte e Seis de Outubro', 'MG', 'Ipatinga', 'Bela Vista', 2014, 0, '2024-05-31', '2024-05-31'),
(2, 1, 'Motorista Tste', '2024-06-13', '2024-06-19', 'aaada@gmail.com', '12131213213', '2024-06-19', '35160208', 'Avenida Vinte e Seis de Outubro', 'MG', 'Ipatinga', 'Bela Vista', 2014, 0, '2024-06-01', '2024-06-01');

-- --------------------------------------------------------

--
-- Estrutura para tabela `objetivas_ead`
--

CREATE TABLE `objetivas_ead` (
  `id` int(11) NOT NULL,
  `IDAtividade` int(11) NOT NULL,
  `Enunciado` varchar(50) NOT NULL,
  `Opcoes` text NOT NULL,
  `Correta` varchar(1) NOT NULL,
  `Resposta` varchar(1) DEFAULT NULL,
  `Feedback` varchar(250) DEFAULT NULL,
  `Total` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ocorrencias`
--

CREATE TABLE `ocorrencias` (
  `id` int(11) NOT NULL,
  `IDAluno` int(11) NOT NULL,
  `IDProfessor` int(11) NOT NULL,
  `DTOcorrencia` datetime NOT NULL,
  `DSOcorrido` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `organizacoes`
--

CREATE TABLE `organizacoes` (
  `id` int(11) NOT NULL,
  `Organizacao` varchar(100) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Endereco` varchar(250) NOT NULL,
  `UF` varchar(2) NOT NULL,
  `Cidade` varchar(30) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `organizacoes`
--

INSERT INTO `organizacoes` (`id`, `Organizacao`, `Email`, `Endereco`, `UF`, `Cidade`, `updated_at`, `created_at`) VALUES
(1, 'Oba Oba e o Maior de Ipatinga', 'maxhenrique308@gmail.co', '{\"Rua\":\"Avenida Vinte e Seis de Outubro\",\"Cidade\":\"Ipatinga\",\"Bairro\":\"Bela Vista\",\"UF\":\"MG\",\"Numero\":\"2014\",\"CEP\":\"35160-208\"}', 'MG', 'Ipatinga', '2024-05-13 04:11:14', '2024-05-12 07:56:27'),
(2, 'testando secretaria', 'teste@gmail.com', '{\"Rua\":\"Avenida Vinte e Seis de Outubro\",\"Cidade\":\"Ipatinga\",\"Bairro\":\"Bela Vista\",\"UF\":\"MG\",\"Numero\":\"2014\",\"CEP\":\"35160-208\"}', 'MG', 'Ipatinga', '2024-05-13 05:08:57', '2024-05-13 05:08:57');

-- --------------------------------------------------------

--
-- Estrutura para tabela `paradas`
--

CREATE TABLE `paradas` (
  `id` int(11) NOT NULL,
  `IDRota` int(11) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `Hora` time NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `paradas`
--

INSERT INTO `paradas` (`id`, `IDRota`, `Nome`, `Hora`, `created_at`, `updated_at`) VALUES
(1, 1, 'Test', '15:38:00', '2024-06-01', '2024-06-01'),
(2, 1, 'Testt', '19:08:00', '2024-06-01', '2024-06-01');

-- --------------------------------------------------------

--
-- Estrutura para tabela `paralizacoes`
--

CREATE TABLE `paralizacoes` (
  `id` int(11) NOT NULL,
  `DSMotivo` varchar(250) NOT NULL,
  `IDEscola` int(11) NOT NULL,
  `DTInicio` date NOT NULL,
  `DTTermino` date NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `paralizacoes`
--

INSERT INTO `paralizacoes` (`id`, `DSMotivo`, `IDEscola`, `DTInicio`, `DTTermino`, `created_at`, `updated_at`) VALUES
(1, 'Motivod e teste', 3, '2024-05-06', '2024-05-30', '2024-05-25', '2024-05-25');

-- --------------------------------------------------------

--
-- Estrutura para tabela `participacoeseventos`
--

CREATE TABLE `participacoeseventos` (
  `IDEscola` int(11) NOT NULL,
  `IDEvento` int(11) NOT NULL,
  `DTInicio` datetime NOT NULL,
  `DTTermino` datetime NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `participacoeseventos`
--

INSERT INTO `participacoeseventos` (`IDEscola`, `IDEvento`, `DTInicio`, `DTTermino`, `created_at`, `updated_at`) VALUES
(1, 1, '2024-05-13 04:38:00', '2024-06-01 04:38:00', '2024-05-24', '2024-05-24'),
(4, 1, '2024-05-06 04:38:00', '2024-06-01 04:38:00', '2024-05-24', '2024-05-24');

-- --------------------------------------------------------

--
-- Estrutura para tabela `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedagogos`
--

CREATE TABLE `pedagogos` (
  `id` int(11) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `Nascimento` date NOT NULL,
  `Admissao` date NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Celular` varchar(11) NOT NULL,
  `TerminoContrato` date DEFAULT NULL,
  `CEP` varchar(8) NOT NULL,
  `Rua` varchar(50) NOT NULL,
  `UF` varchar(2) NOT NULL,
  `Cidade` varchar(50) NOT NULL,
  `Bairro` varchar(500) DEFAULT NULL,
  `Numero` int(11) DEFAULT NULL,
  `Ativo` int(11) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedagogos`
--

INSERT INTO `pedagogos` (`id`, `Nome`, `Nascimento`, `Admissao`, `Email`, `Celular`, `TerminoContrato`, `CEP`, `Rua`, `UF`, `Cidade`, `Bairro`, `Numero`, `Ativo`, `created_at`, `updated_at`) VALUES
(1, 'Pedagogo', '2024-05-18', '2024-05-11', 'peda@gmail.com', '32442443423', '2024-05-18', '35160208', 'Avenida Vinte e Seis de Outubro', 'MG', 'Ipatinga', 'Bela Vista', 2014, 0, '2024-05-22', '2024-05-22');

-- --------------------------------------------------------

--
-- Estrutura para tabela `planejamentoanual`
--

CREATE TABLE `planejamentoanual` (
  `id` int(11) NOT NULL,
  `IDProfessor` int(11) NOT NULL,
  `IDDisciplina` int(11) NOT NULL,
  `IDTurma` int(11) NOT NULL,
  `PLConteudos` text NOT NULL,
  `Aprovado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `planejamentosemanal`
--

CREATE TABLE `planejamentosemanal` (
  `id` int(11) NOT NULL,
  `IDPlanejamentoAnual` int(11) NOT NULL,
  `PLConteudos` text NOT NULL,
  `INISemana` date NOT NULL,
  `TERSemana` date NOT NULL,
  `Aprovado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `presenca`
--

CREATE TABLE `presenca` (
  `id` int(11) NOT NULL,
  `IDAula` int(11) NOT NULL,
  `IDEscola` int(11) NOT NULL,
  `IDTurma` int(11) NOT NULL,
  `IDProfessor` int(11) NOT NULL,
  `IDAluno` int(11) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `professores`
--

CREATE TABLE `professores` (
  `id` int(11) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `Nascimento` date NOT NULL,
  `Admissao` date NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Celular` varchar(11) NOT NULL,
  `TerminoContrato` date DEFAULT NULL,
  `CEP` varchar(8) NOT NULL,
  `Rua` varchar(50) NOT NULL,
  `UF` varchar(2) NOT NULL,
  `Cidade` varchar(50) NOT NULL,
  `Bairro` varchar(500) DEFAULT NULL,
  `Numero` int(11) DEFAULT NULL,
  `Ativo` int(11) NOT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `professores`
--

INSERT INTO `professores` (`id`, `Nome`, `Nascimento`, `Admissao`, `Email`, `Celular`, `TerminoContrato`, `CEP`, `Rua`, `UF`, `Cidade`, `Bairro`, `Numero`, `Ativo`, `updated_at`, `created_at`) VALUES
(1, 'Professor', '2024-05-11', '2024-05-17', 'prof@gmail.com', '12313123312', '2024-05-09', '35160208', 'Avenida Vinte e Seis de Outubro', 'MG', 'Ipatinga', 'Bela Vista', 2014, 0, '2024-05-22', '2024-05-22'),
(2, 'Fessor', '2024-05-16', '2024-05-25', 'fsfsfsf@gmail.com', '13132321334', '2024-05-10', '35160308', 'Rua Mogno', 'MG', 'Ipatinga', 'Horto', 444, 0, '2024-05-24', '2024-05-24'),
(3, '', '0000-00-00', '0000-00-00', '', '', NULL, '', '', '', '', NULL, NULL, 0, '2024-05-27', '2024-05-27');

-- --------------------------------------------------------

--
-- Estrutura para tabela `renovacoes`
--

CREATE TABLE `renovacoes` (
  `id` int(11) NOT NULL,
  `IDAluno` int(11) NOT NULL,
  `Aprovado` int(11) NOT NULL,
  `ANO` year(4) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `Vencimento` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `renovacoes`
--

INSERT INTO `renovacoes` (`id`, `IDAluno`, `Aprovado`, `ANO`, `created_at`, `updated_at`, `Vencimento`) VALUES
(1, 1, 1, '2025', '2024-06-05', '2024-06-05', '2025-06-29'),
(2, 2, 1, '2024', '2024-06-05', '2024-06-06', '2024-06-22'),
(3, 3, 1, '2025', '2024-06-06', '2024-06-07', '2025-06-05');

-- --------------------------------------------------------

--
-- Estrutura para tabela `responsavel`
--

CREATE TABLE `responsavel` (
  `id` int(11) NOT NULL,
  `IDAluno` int(11) NOT NULL,
  `RGPaisAnexo` text NOT NULL,
  `RGPais` varchar(9) NOT NULL,
  `NMResponsavel` varchar(50) NOT NULL,
  `EmailResponsavel` varchar(50) NOT NULL,
  `CLResponsavel` varchar(11) NOT NULL,
  `CPFResponsavel` varchar(11) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `responsavel`
--

INSERT INTO `responsavel` (`id`, `IDAluno`, `RGPaisAnexo`, `RGPais`, `NMResponsavel`, `EmailResponsavel`, `CLResponsavel`, `CPFResponsavel`, `created_at`, `updated_at`) VALUES
(1, 1, '', '20823455', 'Zenilda Barros Nelvam', 'zenilda@gmail.com', '31987778018', '16885066672', '2024-06-05', '2024-06-05'),
(2, 2, 'C:\\xampp\\tmp\\phpE0A3.tmp', '20896779', 'Angelica', 'maxhenrique308@gmaiol.com', '43543534534', '08901129671', '2024-06-05', '2024-06-06'),
(3, 3, '', '24234234', 'dsfdsfsd', 'adasd@gmail.com', '42442342342', '23424234234', '2024-06-06', '2024-06-07');

-- --------------------------------------------------------

--
-- Estrutura para tabela `reunioes`
--

CREATE TABLE `reunioes` (
  `id` int(11) NOT NULL,
  `IDEscola` int(11) NOT NULL,
  `DTInicio` datetime NOT NULL,
  `DTTermino` datetime NOT NULL,
  `DSEvento` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `rodagem`
--

CREATE TABLE `rodagem` (
  `id` int(11) NOT NULL,
  `KMInicial` float NOT NULL,
  `KMFinal` float NOT NULL,
  `IDVeiculo` int(11) NOT NULL,
  `IDRota` int(11) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `rodagem`
--

INSERT INTO `rodagem` (`id`, `KMInicial`, `KMFinal`, `IDVeiculo`, `IDRota`, `created_at`, `updated_at`) VALUES
(1, 356, 480, 1, 1, '2024-06-01', '2024-06-01'),
(2, 800, 1200, 1, 0, '2024-06-01', '2024-06-01'),
(3, 67, 84, 1, 1, '2024-06-01', '2024-06-01');

-- --------------------------------------------------------

--
-- Estrutura para tabela `rotas`
--

CREATE TABLE `rotas` (
  `id` int(11) NOT NULL,
  `IDVeiculo` int(11) NOT NULL,
  `IDMotorista` int(11) NOT NULL,
  `Descricao` varchar(50) NOT NULL,
  `Distancia` float NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `Turno` varchar(5) NOT NULL,
  `Partida` varchar(50) NOT NULL,
  `Chegada` varchar(50) NOT NULL,
  `HoraPartida` time NOT NULL,
  `HoraChegada` time NOT NULL,
  `DiasJSON` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `rotas`
--

INSERT INTO `rotas` (`id`, `IDVeiculo`, `IDMotorista`, `Descricao`, `Distancia`, `created_at`, `updated_at`, `Turno`, `Partida`, `Chegada`, `HoraPartida`, `HoraChegada`, `DiasJSON`) VALUES
(1, 0, 1, 'sdfsdff', 567, '2024-06-01', '2024-06-01', 'Manhã', 'adssdf', 'sfsdfsf', '01:36:00', '04:36:00', '[\"Segunda\",\"Quarta\",\"Sabado\"]'),
(4, 0, 2, 'Descrição da Partida', 80, '2024-06-01', '2024-06-01', 'Manhã', 'Ipatinga', 'Timoteo', '15:35:00', '13:40:00', '[\"Segunda\",\"Quarta\",\"Quinta\"]');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sabados_letivos`
--

CREATE TABLE `sabados_letivos` (
  `id` int(11) NOT NULL,
  `IDEscola` int(11) NOT NULL,
  `Data` date NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `sabados_letivos`
--

INSERT INTO `sabados_letivos` (`id`, `IDEscola`, `Data`, `created_at`, `updated_at`) VALUES
(1, 2, '2024-08-03', '2024-05-25', '2024-05-25'),
(2, 3, '2024-09-14', '2024-05-25', '2024-05-25');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('bsQ1ZxjelXo2mSMPFZGLdLa0BHq9FLTKILupsydt', 37, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiTlBGaElpRElCUDZ0WlFkWlpDajlZUmpxdE9VbDBDU3VweFRrWHhYdCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM5OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvQWx1bm9zL1N1c3BlbnNvLzMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozNzt9', 1717863699),
('wtRbyE541357MDVS8GRK00YaiOZYzRUOHnPfimwR', 37, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiYUozVndvQm90ZUpVUVQyMGlYbVZydG9XQ2J5bmlMSTNmeDluNGtrSiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQ1OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvQWx1bm9zL1RyYW5zZmVyZW5jaWFzLzEiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozNzt9', 1717822844);

-- --------------------------------------------------------

--
-- Estrutura para tabela `suspensos`
--

CREATE TABLE `suspensos` (
  `id` int(11) NOT NULL,
  `IDInativo` int(11) NOT NULL,
  `Justificativa` varchar(250) NOT NULL,
  `INISuspensao` date DEFAULT NULL,
  `TERSuspensao` date DEFAULT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `terceirizadas`
--

CREATE TABLE `terceirizadas` (
  `id` int(11) NOT NULL,
  `IDOrg` int(11) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `CEP` varchar(8) NOT NULL,
  `Rua` varchar(50) NOT NULL,
  `Bairro` varchar(50) NOT NULL,
  `Cidade` varchar(50) NOT NULL,
  `Numero` int(11) NOT NULL,
  `UF` varchar(2) NOT NULL,
  `Telefone` varchar(11) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `CNPJ` varchar(14) NOT NULL,
  `Ramo` varchar(30) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `TerminoContrato` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `terceirizadas`
--

INSERT INTO `terceirizadas` (`id`, `IDOrg`, `Nome`, `CEP`, `Rua`, `Bairro`, `Cidade`, `Numero`, `UF`, `Telefone`, `Email`, `CNPJ`, `Ramo`, `created_at`, `updated_at`, `TerminoContrato`) VALUES
(1, 1, 'Testando empresa', '35160208', 'Avenida Vinte e Seis de Outubro', 'Bela Vista', 'Ipatinga', 2024, 'MG', '44334535345', 'ddfsfs@gmail.com', '32.324.2342/34', 'Transportes', '2024-05-31', '2024-05-31', '2024-05-31');

-- --------------------------------------------------------

--
-- Estrutura para tabela `transferencias`
--

CREATE TABLE `transferencias` (
  `id` int(11) NOT NULL,
  `IDAluno` int(11) NOT NULL,
  `Aprovado` int(11) NOT NULL DEFAULT 0,
  `IDEscolaDestino` int(11) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `Justificativa` varchar(250) NOT NULL,
  `IDEscolaOrigem` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `transferencias`
--

INSERT INTO `transferencias` (`id`, `IDAluno`, `Aprovado`, `IDEscolaDestino`, `created_at`, `updated_at`, `Justificativa`, `IDEscolaOrigem`) VALUES
(1, 2, 0, 1, '2024-06-08', '2024-06-08', 'Transferido pq cagou no chão kkkkkkkk', 2),
(2, 1, 0, 6, '2024-06-08', '2024-06-08', 'pq o sesi e a melhor escola', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `turmas`
--

CREATE TABLE `turmas` (
  `id` int(11) NOT NULL,
  `IDEscola` int(11) NOT NULL,
  `Serie` varchar(30) NOT NULL,
  `Nome` varchar(30) NOT NULL,
  `INITurma` time NOT NULL,
  `TERTurma` time NOT NULL,
  `Periodo` varchar(10) NOT NULL,
  `NotaPeriodo` float DEFAULT NULL,
  `MediaPeriodo` float DEFAULT NULL,
  `TotalAno` float DEFAULT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL,
  `QTRepetencia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `turmas`
--

INSERT INTO `turmas` (`id`, `IDEscola`, `Serie`, `Nome`, `INITurma`, `TERTurma`, `Periodo`, `NotaPeriodo`, `MediaPeriodo`, `TotalAno`, `updated_at`, `created_at`, `QTRepetencia`) VALUES
(1, 3, '9º Ano E.FUNDAMENTAL', 'Turma 92', '07:00:00', '11:25:00', 'Trimestral', 30, 15, 100, '2024-05-18', '2024-05-17', 0),
(2, 2, '1º Ano E.FUNDAMENTAL', 'Turma 100', '21:00:00', '00:00:00', 'Bimestral', 100, 15, 100, '2024-05-27', '2024-05-27', 0),
(3, 3, '1º Ano E.MÉDIO', 'Turma 1000', '00:02:00', '00:02:00', 'Trimestral', 25, 15, 100, '2024-05-27', '2024-05-27', 0),
(4, 1, '4º Ano E.FUNDAMENTAL', 'Turma dr ovidio', '20:11:00', '21:11:00', 'Bimestral', 25, 15, 100, '2024-06-03', '2024-06-03', 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `turnos`
--

CREATE TABLE `turnos` (
  `id` int(11) NOT NULL,
  `IDProfessor` int(11) NOT NULL,
  `IDDisciplina` int(11) NOT NULL,
  `IDTurma` int(11) NOT NULL,
  `INITur` time NOT NULL,
  `TERTur` time NOT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL,
  `DiaSemana` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `turnos`
--

INSERT INTO `turnos` (`id`, `IDProfessor`, `IDDisciplina`, `IDTurma`, `INITur`, `TERTur`, `updated_at`, `created_at`, `DiaSemana`) VALUES
(1, 2, 1, 3, '20:26:00', '22:26:00', '2024-05-27', '2024-05-27', 'Terça'),
(2, 2, 1, 1, '19:28:00', '19:31:00', '2024-05-27', '2024-05-27', 'Segunda'),
(5, 2, 1, 1, '16:11:00', '18:11:00', '2024-06-03', '2024-06-03', 'Terça'),
(6, 2, 1, 4, '20:12:00', '21:12:00', '2024-06-03', '2024-06-03', 'Terça'),
(7, 2, 1, 4, '20:12:00', '21:12:00', '2024-06-03', '2024-06-03', 'Terça');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `id_org` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `permissoes` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tipo` int(11) NOT NULL,
  `IDProfissional` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `id_org`, `password`, `permissoes`, `remember_token`, `created_at`, `updated_at`, `tipo`, `IDProfissional`) VALUES
(1, 'Max Henrique', 'maxhenrique308@gmail.com', NULL, 0, '$2y$12$/2fbkxuWZYNET//AOJoBieyr3drGudBxEUIH1iwgcMqugWcQzOrie', NULL, 'wnkCCljlBFtehvjTcDcM8QeNETwmEGxZi7PkFqU9eNHV9lHAkHwuybJSBKR3', '2024-05-09 12:48:59', '2024-05-09 12:48:59', 0, NULL),
(5, 'Max Henrique', 'testandooo@gmail.comm', NULL, 1, '$2y$12$H1rSGUbvU0uqv7a5JU4h5utfuO2akBAcWBA.jp.va6rchzSuF2v1K', NULL, NULL, '2024-05-13 08:11:39', '2024-05-13 08:12:20', 2, NULL),
(6, 'Secretario 1', 'secretario@gmail.com', NULL, 1, '$2y$12$korMKEXh/E.pIPM8fOJfneBPR9rdLbLjQXFVKx50sQMYhBtwprUPS', NULL, NULL, '2024-05-13 20:23:23', '2024-05-13 20:23:23', 2, NULL),
(34, 'Professor', 'prof@gmail.com', NULL, 1, '$2y$12$vi0VpYjj5zaWaP7kxriLm.fJoPinyBhljY0vZpQf5tCmHDfBA2EjK', NULL, NULL, '2024-05-22 09:53:33', '2024-05-22 09:53:33', 6, 1),
(35, 'Pedagogo', 'peda@gmail.com', NULL, 1, '$2y$12$Lzdz4nK9HNFbjBrViCyxte8Gl4O2ZA4hvfZRE.47kH.iZr5njvM3q', NULL, NULL, '2024-05-22 09:57:47', '2024-05-22 09:57:47', 6, 1),
(36, 'Fessor', 'fsfsfsf@gmail.com', NULL, 1, '$2y$12$46gkbV1hGKeWZk/zZQzWeOtg1be9HQSp5eDLElzIvFfm8rHsXVb8u', NULL, NULL, '2024-05-24 08:39:28', '2024-05-24 20:59:29', 6, 2),
(37, 'DIRETOR TESTE', 'diretor@gmail.com', NULL, 1, '$2y$12$korMKEXh/E.pIPM8fOJfneBPR9rdLbLjQXFVKx50sQMYhBtwprUPS', NULL, NULL, '2024-06-03 19:15:54', '2024-06-03 19:15:54', 4, 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `veiculos`
--

CREATE TABLE `veiculos` (
  `id` int(11) NOT NULL,
  `IDOrganizacao` int(11) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `Marca` varchar(50) NOT NULL,
  `Placa` varchar(7) NOT NULL,
  `Cor` varchar(10) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `KMAquisicao` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `veiculos`
--

INSERT INTO `veiculos` (`id`, `IDOrganizacao`, `Nome`, `Marca`, `Placa`, `Cor`, `created_at`, `updated_at`, `KMAquisicao`) VALUES
(1, 1, 'Mercedes 344', 'Mercedes', '1159', 'Preto', '2024-05-31', '2024-05-31', 771);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `afastados`
--
ALTER TABLE `afastados`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `alteracoes_situacao`
--
ALTER TABLE `alteracoes_situacao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `atividades`
--
ALTER TABLE `atividades`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `atividades_atribuicoes`
--
ALTER TABLE `atividades_atribuicoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `atividades_atribuicoes_ead`
--
ALTER TABLE `atividades_atribuicoes_ead`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `atividades_ead`
--
ALTER TABLE `atividades_ead`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `aulas`
--
ALTER TABLE `aulas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `aulas_ead`
--
ALTER TABLE `aulas_ead`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `auxiliares`
--
ALTER TABLE `auxiliares`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Índices de tabela `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Índices de tabela `calendario`
--
ALTER TABLE `calendario`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cardapio`
--
ALTER TABLE `cardapio`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `comentarios_planual`
--
ALTER TABLE `comentarios_planual`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `comentarios_plsemanal`
--
ALTER TABLE `comentarios_plsemanal`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `diretores`
--
ALTER TABLE `diretores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `disciplinas`
--
ALTER TABLE `disciplinas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `dissertativas_ead`
--
ALTER TABLE `dissertativas_ead`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `escolas`
--
ALTER TABLE `escolas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `estoque`
--
ALTER TABLE `estoque`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `estoque_movimentacao`
--
ALTER TABLE `estoque_movimentacao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Índices de tabela `faltas_justificadas`
--
ALTER TABLE `faltas_justificadas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `faltas_justificadas_profissional`
--
ALTER TABLE `faltas_justificadas_profissional`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `ferias_alunos`
--
ALTER TABLE `ferias_alunos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `ferias_profissionais`
--
ALTER TABLE `ferias_profissionais`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Índices de tabela `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `justificativa_alteracoes`
--
ALTER TABLE `justificativa_alteracoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `matriculas`
--
ALTER TABLE `matriculas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `motoristas`
--
ALTER TABLE `motoristas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `objetivas_ead`
--
ALTER TABLE `objetivas_ead`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `ocorrencias`
--
ALTER TABLE `ocorrencias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `organizacoes`
--
ALTER TABLE `organizacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `paradas`
--
ALTER TABLE `paradas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `paralizacoes`
--
ALTER TABLE `paralizacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Índices de tabela `pedagogos`
--
ALTER TABLE `pedagogos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `planejamentoanual`
--
ALTER TABLE `planejamentoanual`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `planejamentosemanal`
--
ALTER TABLE `planejamentosemanal`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `presenca`
--
ALTER TABLE `presenca`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `professores`
--
ALTER TABLE `professores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `renovacoes`
--
ALTER TABLE `renovacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `responsavel`
--
ALTER TABLE `responsavel`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `reunioes`
--
ALTER TABLE `reunioes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `rodagem`
--
ALTER TABLE `rodagem`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `rotas`
--
ALTER TABLE `rotas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `sabados_letivos`
--
ALTER TABLE `sabados_letivos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Índices de tabela `suspensos`
--
ALTER TABLE `suspensos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `terceirizadas`
--
ALTER TABLE `terceirizadas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `transferencias`
--
ALTER TABLE `transferencias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `turmas`
--
ALTER TABLE `turmas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `turnos`
--
ALTER TABLE `turnos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Índices de tabela `veiculos`
--
ALTER TABLE `veiculos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `afastados`
--
ALTER TABLE `afastados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `alteracoes_situacao`
--
ALTER TABLE `alteracoes_situacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `alunos`
--
ALTER TABLE `alunos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `atividades`
--
ALTER TABLE `atividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `atividades_atribuicoes`
--
ALTER TABLE `atividades_atribuicoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `atividades_atribuicoes_ead`
--
ALTER TABLE `atividades_atribuicoes_ead`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `atividades_ead`
--
ALTER TABLE `atividades_ead`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `aulas`
--
ALTER TABLE `aulas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `aulas_ead`
--
ALTER TABLE `aulas_ead`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `auxiliares`
--
ALTER TABLE `auxiliares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `calendario`
--
ALTER TABLE `calendario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `cardapio`
--
ALTER TABLE `cardapio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `comentarios_planual`
--
ALTER TABLE `comentarios_planual`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `comentarios_plsemanal`
--
ALTER TABLE `comentarios_plsemanal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `diretores`
--
ALTER TABLE `diretores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `disciplinas`
--
ALTER TABLE `disciplinas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `dissertativas_ead`
--
ALTER TABLE `dissertativas_ead`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `escolas`
--
ALTER TABLE `escolas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `estoque`
--
ALTER TABLE `estoque`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `estoque_movimentacao`
--
ALTER TABLE `estoque_movimentacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `faltas_justificadas`
--
ALTER TABLE `faltas_justificadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `faltas_justificadas_profissional`
--
ALTER TABLE `faltas_justificadas_profissional`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ferias_alunos`
--
ALTER TABLE `ferias_alunos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `ferias_profissionais`
--
ALTER TABLE `ferias_profissionais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `justificativa_alteracoes`
--
ALTER TABLE `justificativa_alteracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `matriculas`
--
ALTER TABLE `matriculas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `motoristas`
--
ALTER TABLE `motoristas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `objetivas_ead`
--
ALTER TABLE `objetivas_ead`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ocorrencias`
--
ALTER TABLE `ocorrencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `organizacoes`
--
ALTER TABLE `organizacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `paradas`
--
ALTER TABLE `paradas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `paralizacoes`
--
ALTER TABLE `paralizacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `pedagogos`
--
ALTER TABLE `pedagogos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `planejamentoanual`
--
ALTER TABLE `planejamentoanual`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `planejamentosemanal`
--
ALTER TABLE `planejamentosemanal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `presenca`
--
ALTER TABLE `presenca`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `professores`
--
ALTER TABLE `professores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `renovacoes`
--
ALTER TABLE `renovacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `responsavel`
--
ALTER TABLE `responsavel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `reunioes`
--
ALTER TABLE `reunioes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `rodagem`
--
ALTER TABLE `rodagem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `rotas`
--
ALTER TABLE `rotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `sabados_letivos`
--
ALTER TABLE `sabados_letivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `suspensos`
--
ALTER TABLE `suspensos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `terceirizadas`
--
ALTER TABLE `terceirizadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `transferencias`
--
ALTER TABLE `transferencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `turmas`
--
ALTER TABLE `turmas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `turnos`
--
ALTER TABLE `turnos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de tabela `veiculos`
--
ALTER TABLE `veiculos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
