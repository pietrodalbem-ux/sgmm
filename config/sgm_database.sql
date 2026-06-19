    -- ==========================================================
    -- SISTEMA DE GESTÃO DE MANUTENÇÃO (SGM) - SENAI
    -- Script de Criação do Banco de Dados
    -- Escopo: Gestão de Chamados por Ambiente
    -- Banco: MySQL / MariaDB
    -- Versão Unificada (2.0 - Soft Delete & Security)
    -- ==========================================================

    -- Configurações de Sessão para evitar erros durante a criação
    SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
    SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
    SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

    CREATE DATABASE IF NOT EXISTS sgm_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    USE sgm_db;

    -- 1. ESTRUTURA ORGANIZACIONAL
    CREATE TABLE IF NOT EXISTS departamentos (
        id_departamento INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        sigla VARCHAR(10),
        responsavel VARCHAR(100),
        deleted_at DATETIME NULL,
        deleted_by INT NULL
    );

    CREATE TABLE IF NOT EXISTS blocos (
        id_bloco INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(50) NOT NULL,
        descricao TEXT,
        deleted_at DATETIME NULL,
        deleted_by INT NULL
    );

    CREATE TABLE IF NOT EXISTS ambientes (
        id_ambiente INT AUTO_INCREMENT PRIMARY KEY,
        id_bloco INT NOT NULL,
        nome VARCHAR(100) NOT NULL,
        ponto_referencia VARCHAR(255),
        deleted_at DATETIME NULL,
        deleted_by INT NULL,
        FOREIGN KEY (id_bloco) REFERENCES blocos(id_bloco)
    );

    -- 2. GESTÃO DE USUÁRIOS (Controle de Acesso)
    CREATE TABLE IF NOT EXISTS usuarios (
        id_usuario INT AUTO_INCREMENT PRIMARY KEY,
        id_departamento INT,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        cpf VARCHAR(14) UNIQUE,
        telefone VARCHAR(20),
        senha_hash VARCHAR(255) NOT NULL,
        perfil ENUM('admin', 'gestor', 'tecnico', 'solicitante') NOT NULL,
        foto_perfil VARCHAR(255) DEFAULT 'assets/img/default-user.png',
        ativo TINYINT(1) DEFAULT 1,
        ultimo_login DATETIME,
        data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        deleted_at DATETIME NULL,
        deleted_by INT NULL,
        FOREIGN KEY (id_departamento) REFERENCES departamentos(id_departamento)
    );

    -- 3. GESTÃO DE ATIVOS (Equipamentos)
    CREATE TABLE IF NOT EXISTS categorias_equipamento (
        id_categoria INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        icone VARCHAR(50),
        deleted_at DATETIME NULL,
        deleted_by INT NULL
    );

    CREATE TABLE IF NOT EXISTS equipamentos (
        id_equipamento INT AUTO_INCREMENT PRIMARY KEY,
        id_ambiente INT,
        id_categoria INT,
        nome VARCHAR(100) NOT NULL,
        marca VARCHAR(50),
        modelo VARCHAR(50),
        numero_serie VARCHAR(100) UNIQUE,
        data_aquisicao DATE,
        status ENUM('operacional', 'manutencao', 'baixado') DEFAULT 'operacional',
        deleted_at DATETIME NULL,
        deleted_by INT NULL,
        FOREIGN KEY (id_ambiente) REFERENCES ambientes(id_ambiente),
        FOREIGN KEY (id_categoria) REFERENCES categorias_equipamento(id_categoria)
    );

    -- 4. GESTÃO DE MANUTENÇÃO (Chamados)
    CREATE TABLE IF NOT EXISTS tipo_servico (
        id_tipo_servico INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        descricao TEXT,
        tempo_estimado_horas INT DEFAULT 1,
        deleted_at DATETIME NULL,
        deleted_by INT NULL
    );

    CREATE TABLE IF NOT EXISTS chamados (
        id_chamado INT AUTO_INCREMENT PRIMARY KEY,
        id_solicitante INT NOT NULL,
        id_tecnico INT DEFAULT NULL,
        id_ambiente INT NOT NULL,
        id_equipamento INT DEFAULT NULL,
        id_tipo_servico INT NOT NULL,
        titulo VARCHAR(150) NOT NULL,
        descricao_problema TEXT NOT NULL,
        tipo_manutencao ENUM('corretiva', 'preventiva', 'preditiva') DEFAULT 'corretiva',
        prioridade ENUM('baixa', 'media', 'alta', 'critica') DEFAULT 'media',
        status ENUM('aberto', 'triagem', 'em_andamento', 'aguardando_peca', 'concluido', 'cancelado') DEFAULT 'aberto',
        data_abertura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        data_inicio_atendimento DATETIME,
        data_previsao_conclusao DATETIME DEFAULT NULL,
        data_conclusao DATETIME,
        feedback_solicitante TEXT,
        avaliacao_estrelas TINYINT(1) CHECK (avaliacao_estrelas BETWEEN 1 AND 5),
        deleted_at DATETIME NULL,
        deleted_by INT NULL,
        FOREIGN KEY (id_solicitante) REFERENCES usuarios(id_usuario),
        FOREIGN KEY (id_tecnico) REFERENCES usuarios(id_usuario),
        FOREIGN KEY (id_ambiente) REFERENCES ambientes(id_ambiente),
        FOREIGN KEY (id_equipamento) REFERENCES equipamentos(id_equipamento),
        FOREIGN KEY (id_tipo_servico) REFERENCES tipo_servico(id_tipo_servico)
    );

    -- 5. MATERIAIS E ESTOQUE
    CREATE TABLE IF NOT EXISTS materiais (
        id_material INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        unidade_medida VARCHAR(20),
        quantidade_estoque INT DEFAULT 0,
        estoque_minimo INT DEFAULT 5,
        deleted_at DATETIME NULL,
        deleted_by INT NULL
    );

    CREATE TABLE IF NOT EXISTS materiais_chamado (
        id_material_chamado INT AUTO_INCREMENT PRIMARY KEY,
        id_chamado INT NOT NULL,
        id_material INT NOT NULL,
        quantidade INT NOT NULL,
        FOREIGN KEY (id_chamado) REFERENCES chamados(id_chamado),
        FOREIGN KEY (id_material) REFERENCES materiais(id_material)
    );

    -- 6. COMUNICAÇÃO E HISTÓRICO
    CREATE TABLE IF NOT EXISTS chamados_anexos (
        id_anexo INT AUTO_INCREMENT PRIMARY KEY,
        id_chamado INT NOT NULL,
        caminho_arquivo VARCHAR(255) NOT NULL,
        tipo_anexo VARCHAR(50) DEFAULT 'evidencia',
        data_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_chamado) REFERENCES chamados(id_chamado)
    );

    CREATE TABLE IF NOT EXISTS chamados_historico (
        id_historico INT AUTO_INCREMENT PRIMARY KEY,
        id_chamado INT NOT NULL,
        id_usuario INT NOT NULL,
        acao VARCHAR(255) NOT NULL,
        observacao TEXT,
        data_acao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_chamado) REFERENCES chamados(id_chamado),
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
    );

    CREATE TABLE IF NOT EXISTS notificacoes (
        id_notificacao INT AUTO_INCREMENT PRIMARY KEY,
        id_usuario INT NOT NULL,
        titulo VARCHAR(100),
        mensagem TEXT,
        lida TINYINT(1) DEFAULT 0,
        link_acesso VARCHAR(255),
        data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
    );

    -- 7. DADOS INICIAIS (SEEDER)
    INSERT INTO departamentos (nome, sigla, responsavel) VALUES 
    ('Infraestrutura e Manutenção', 'INFRA', 'Eng. Roberto'),
    ('Tecnologia da Informação', 'TI', 'Ana Paula'),
    ('Administrativo', 'ADM', 'Carlos Silva');

    INSERT INTO blocos (nome, descricao) VALUES 
    ('Bloco Central', 'Sede administrativa e laboratórios base'),
    ('Oficinas', 'Área de treinamento prático de mecânica e elétrica'),
    ('Área de Lazer', 'Pátio, refeitório e quadras');

    INSERT INTO ambientes (id_bloco, nome, ponto_referencia) VALUES 
    (1, 'Auditório 01', 'Próximo à recepção'),
    (1, 'Laboratório de Redes', 'Andar superior'),
    (2, 'Oficina de Tornearia', 'Galpão 02'),
    (3, 'Refeitório Central', 'Ao lado da quadra');

    INSERT INTO tipo_servico (nome, descricao, tempo_estimado_horas) VALUES 
    ('Elétrica Geral', 'Reparos em tomadas, fiação e iluminação', 2),
    ('Hidráulica', 'Vazamentos e tubulações', 3),
    ('Infraestrutura TI', 'Pontos de rede e racks', 4),
    ('Climatização', 'Limpeza e reparo de Ar-condicionado', 5);

    -- Nota: a coluna data_previsao_conclusao já está definida no CREATE TABLE chamados (linha 113)

    -- Usuários Padrão
    -- Admin: admin@senai.br / 123
    INSERT INTO usuarios (nome, email, senha_hash, perfil, id_departamento) VALUES 
    ('Eduarda Administradora', 'admin@senai.br', '$2y$10$TSwflPXEJ731SnJOTZ/CpeEueRzIBqDBCJeWBSYB9mojR9GLOj/jy', 'admin', 1),
    ('Gestor de Operações', 'gestor@senai.br', '$2y$10$TSwflPXEJ731SnJOTZ/CpeEueRzIBqDBCJeWBSYB9mojR9GLOj/jy', 'gestor', 1),
    ('Técnico de Manutenção', 'tecnico@senai.br', '$2y$10$TSwflPXEJ731SnJOTZ/CpeEueRzIBqDBCJeWBSYB9mojR9GLOj/jy', 'tecnico', 1),
    ('Colaborador Solicitante', 'solicitante@senai.br', '$2y$10$TSwflPXEJ731SnJOTZ/CpeEueRzIBqDBCJeWBSYB9mojR9GLOj/jy', 'solicitante', 3);

    -- Restaurar configurações de sessão
    SET SQL_MODE=@OLD_SQL_MODE;
    SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
    SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
