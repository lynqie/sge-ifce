<?php
session_start();
if (isset($_GET['sair'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}
if (!isset($_SESSION['logado'])) { header("Location: index.php"); exit; }
include 'conexao.php'; 

$perfil = $_SESSION['perfil'];
$nome_usuario = $_SESSION['nome_usuario'];
$disciplina_prof = $_SESSION['disciplina'] ?? '';

$lista_alunos = [];
$res = $conn->query("SELECT id, nome, curso FROM alunos ORDER BY curso, nome");
if($res) { while($row = $res->fetch_assoc()) { $lista_alunos[] = $row; } }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel - SGE IFCE</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --ifce-verde-escuro: #1b5e20; --ifce-verde-claro: #e8f5e9;
            --ifce-vermelho: #c62828; --ifce-vermelho-claro: #ffebee;
            --bg-site: #f4f7f6; --bg-card: #ffffff;
            --texto-principal: #2d3748; --texto-secundario: #718096; --borda: #edf2f7;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { display: flex; height: 100vh; background-color: var(--bg-site); color: var(--texto-principal); overflow: hidden; }
        
        /* SIDEBAR */
        .sidebar { width: 260px; background-color: var(--bg-card); display: flex; flex-direction: column; border-right: 1px solid var(--borda); z-index: 10; }
        .sidebar-logo { padding: 30px 25px; display: flex; align-items: center; gap: 12px; }
        .sidebar-logo i { font-size: 24px; color: var(--ifce-verde-escuro); }
        .sidebar-logo h2 { font-size: 18px; font-weight: 700; letter-spacing: -0.5px; color: var(--texto-principal); }
        
        .menu { list-style: none; padding: 10px 15px; flex: 1; overflow-y: auto;}
        .menu-title { font-size: 11px; font-weight: 700; color: #a0aec0; text-transform: uppercase; letter-spacing: 1px; padding: 15px 18px 5px 18px; }
        .menu li { padding: 12px 18px; margin-bottom: 5px; cursor: pointer; transition: all 0.2s ease; border-radius: 12px; font-weight: 500; font-size: 14px; color: var(--texto-secundario); display: flex; align-items: center; gap: 15px; }
        .menu li i { font-size: 18px; width: 20px; text-align: center; }
        .menu li:hover { background-color: #f8fafc; color: var(--ifce-verde-escuro); }
        .menu li.ativo { background-color: var(--ifce-verde-claro); color: var(--ifce-verde-escuro); font-weight: 600; }
        
        .sidebar-footer { padding: 20px 15px; }
        .btn-help { background-color: var(--ifce-verde-escuro); color: white; border-radius: 16px; padding: 20px; text-align: center; box-shadow: 0 10px 25px rgba(27, 94, 32, 0.2); }
        .btn-help button { background: white; color: var(--ifce-verde-escuro); border: none; padding: 8px 15px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; width: 100%; margin-top: 10px;}

        /* ÁREA PRINCIPAL */
        .area-principal { flex: 1; display: flex; flex-direction: column; overflow: hidden; padding: 0 10px; }
        .header-top { padding: 30px 40px; display: flex; justify-content: space-between; align-items: center; }
        .boas-vindas h1 { font-size: 24px; font-weight: 600; color: var(--texto-principal); }
        .boas-vindas p { font-size: 14px; color: var(--texto-secundario); margin-top: 4px; }
        
        .perfil-box { display: flex; align-items: center; gap: 12px; }
        .perfil-foto { width: 45px; height: 45px; background-color: var(--ifce-verde-escuro); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 18px; text-transform: uppercase;}
        .perfil-info h4 { font-size: 14px; font-weight: 600; }
        .perfil-info p { font-size: 12px; color: var(--texto-secundario); }

        .conteudo { padding: 0 40px 40px 40px; overflow-y: auto; flex: 1; }
        .aba { display: none; animation: fadeIn 0.4s ease; }
        .aba.ativa { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* FORMULÁRIOS */
        .card-form { background: var(--bg-card); padding: 35px; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid var(--borda); margin-bottom: 20px;}
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group.full { grid-column: 1 / -1; }
        
        label { font-size: 13px; font-weight: 600; margin-bottom: 8px; color: var(--texto-principal); text-transform: uppercase; letter-spacing: 0.5px; }
        input, select, textarea { padding: 14px 16px; border: 1px solid #d2d6dc; border-radius: 12px; outline: none; font-size: 14px; background: #f9fafb; transition: 0.3s; }
        input:focus, select:focus, textarea:focus { border-color: var(--ifce-verde-escuro); background: white; box-shadow: 0 0 0 4px var(--ifce-verde-claro); }
        
        .btn-primario { background: var(--ifce-verde-escuro); color: white; border: none; padding: 14px 24px; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 14px; transition: 0.2s; }
        .btn-primario:hover { background: #144017; transform: translateY(-2px); }

        /* TABELA E ELEMENTOS VISUAIS */
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 16px; text-align: left; border-bottom: 1px solid var(--borda); font-size: 14px; }
        th { color: var(--texto-secundario); font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; border-bottom: 2px solid var(--borda); }
        tr:hover { background-color: #f8fafc; }
        
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.5px; }
        .badge-vermelho { background-color: var(--ifce-vermelho-claro); color: var(--ifce-vermelho); }
        .badge-verde { background-color: var(--ifce-verde-claro); color: var(--ifce-verde-escuro); }
        
        .motivo-oculto { display: none; margin-top: 10px; padding: 10px; background: #f1f5f9; border-radius: 8px; font-size: 13px; color: #475569; border-left: 3px solid var(--texto-secundario); }
        .btn-link { background: none; border: none; color: var(--ifce-verde-escuro); font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: underline; padding: 0; }
        .grid-notas { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }

        /* DROPDOWN E MODAL EXCLUSÃO */
        .dropdown { position: relative; display: inline-block; }
        .dropdown-content { display: none; position: absolute; right: 0; background-color: white; min-width: 180px; box-shadow: 0px 10px 25px rgba(0,0,0,0.1); border-radius: 12px; border: 1px solid var(--borda); overflow: hidden; z-index: 100; }
        .dropdown-content a { color: var(--ifce-vermelho); padding: 14px 16px; text-decoration: none; display: block; font-size: 13px; font-weight: 600; }
        .dropdown-content a:hover { background-color: var(--ifce-vermelho-claro); }
        .dropdown.show .dropdown-content { display: block; }

        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center; }
        .modal-box { background: white; width: 400px; padding: 30px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
        .modal-box h3 { color: var(--ifce-vermelho); margin-bottom: 10px; }
        .btn-cancelar { background: #e2e8f0; color: var(--texto-escuro); border: none; padding: 14px 20px; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 14px; }
            /* ESTILOS DO FEED DE AVISOS */
        .feed-aviso { padding: 15px 20px; background: #f8fafc; border-left: 4px solid var(--ifce-verde-escuro); border-radius: 8px; margin-bottom: 12px; display: flex; flex-direction: column; }
        .feed-aviso.alerta { border-left-color: var(--ifce-vermelho); background: #fffafb; }
        .feed-data { font-size: 11px; color: #94a3b8; font-weight: 600; margin-bottom: 4px; }
        .feed-titulo { font-size: 14px; color: #1e293b; font-weight: 600; }
        .feed-desc { font-size: 13px; color: var(--texto-secundario); margin-top: 4px; }
        .btn-ver-mais { background: none; border: 1px solid var(--borda); color: var(--texto-secundario); width: 100%; padding: 12px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn-ver-mais:hover { background: #f1f5f9; color: var(--texto-principal); }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="sidebar-logo">
            <i class="fa-solid fa-layer-group"></i>
            <h2>SGE IFCE</h2>
        </div>
        
        <ul class="menu">
            <li class="item-menu ativo" onclick="abrirAba('aba-inicio', this)"><i class="fa-solid fa-border-all"></i> Início</li>
            
            <?php if ($perfil == 'Coordenadora'): ?>
                <div class="menu-title">Administrativo</div>
                <li class="item-menu" onclick="abrirAba('aba-cadastrar', this)"><i class="fa-solid fa-user-plus"></i> Matricular Estudante</li>
                <li class="item-menu" onclick="abrirAba('aba-listar', this)"><i class="fa-solid fa-list-check"></i> Histórico Global</li>
                <li class="item-menu" onclick="abrirAba('aba-ocorrencia', this)"><i class="fa-solid fa-triangle-exclamation"></i> Lançar Ocorrência</li>
                <div class="menu-title">Acadêmico</div>
                <li class="item-menu" onclick="abrirAba('aba-boletim-geral', this)"><i class="fa-solid fa-graduation-cap"></i> Boletim Geral</li>
            <?php else: ?>
                <div class="menu-title">Diário de Classe (<?php echo $disciplina_prof; ?>)</div>
                <li class="item-menu" onclick="abrirAba('aba-notas-prof', this)"><i class="fa-solid fa-pen-to-square"></i> Lançar Notas</li>
                <li class="item-menu" onclick="abrirAba('aba-freq-prof', this)"><i class="fa-solid fa-calendar-check"></i> Lançar Frequência</li>
                <li class="item-menu" onclick="abrirAba('aba-diario-prof', this)"><i class="fa-solid fa-table"></i> Meu Diário</li>
            <?php endif; ?>
        </ul>

        <div class="sidebar-footer">
            <div class="btn-help">
                <i class="fa-regular fa-circle-question" style="font-size: 24px;"></i>
                <p style="margin-top:5px; font-size:12px;">SGE</p>
                <button onclick="window.location.href='painel.php?sair=1'">Encerrar Sessão</button>
            </div>
        </div>
    </nav>

    <main class="area-principal">
        <header class="header-top">
            <div class="boas-vindas">
                <h1 id="titulo-topo">Início</h1>
                <p>Acompanhe o desempenho e a disciplina escolar hoje.</p>
            </div>
            
            <div class="perfil-box">
                <div class="perfil-info" style="text-align: right;">
                    <h4><?php echo $nome_usuario; ?></h4>
                    <p><?php echo $perfil; ?></p>
                </div>
                <div class="perfil-foto"><?php echo substr($nome_usuario, 0, 1); ?></div>
            </div>
        </header>

        <div class="conteudo">
            
            <?php if(isset($_GET['sucesso'])): ?>
                <div id="alerta-sistema" style="background: var(--ifce-verde-claro); color: var(--ifce-verde-escuro); padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: 600;">
                    <i class="fa-solid fa-check-circle"></i> Ação registrada com sucesso no banco de dados!
                </div>
            <?php endif; ?>
            <?php if(isset($_GET['erro']) && $_GET['erro'] == 'senha'): ?>
                <div id="alerta-erro" style="background: var(--ifce-vermelho-claro); color: var(--ifce-vermelho); padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: 600;">
                    <i class="fa-solid fa-circle-xmark"></i> Erro: Senha do servidor incorreta! A exclusão foi cancelada.
                </div>
            <?php endif; ?>

            <!-- INÍCIO -->
<div id="aba-inicio" class="aba ativa">
    <?php if ($perfil == 'Coordenadora'): ?>
        <?php
        $q_alunos = $conn->query("SELECT COUNT(*) as total FROM alunos");
        $tot_alunos = $q_alunos ? $q_alunos->fetch_assoc()['total'] : 0;
        
        $q_oc = $conn->query("SELECT COUNT(*) as total FROM ocorrencias");
        $tot_oc = $q_oc ? $q_oc->fetch_assoc()['total'] : 0;
        
        $q_turmas = $conn->query("SELECT COUNT(DISTINCT curso) as total FROM alunos");
        $tot_turmas = $q_turmas ? $q_turmas->fetch_assoc()['total'] : 0;

        $sql_feed = "SELECT o.tipo, o.descricao, a.nome FROM ocorrencias o JOIN alunos a ON o.id_aluno = a.id ORDER BY o.id DESC LIMIT 10";
        $res_feed = $conn->query($sql_feed);
        $logs = [];
        if($res_feed && $res_feed->num_rows > 0) {
            while($l = $res_feed->fetch_assoc()) { $logs[] = $l; }
        }
        ?>

        <div style="margin-bottom: 30px;">
            <h2 style="color: var(--ifce-verde-escuro); font-size: 24px;">Visão Administrativa</h2>
            <p style="color: var(--texto-secundario); margin-top: 5px;">Acompanhe os indicadores acadêmicos em tempo real.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background: white; padding: 25px; border-radius: 16px; border: 1px solid var(--borda); border-left: 5px solid var(--ifce-verde-escuro);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="font-size: 12px; color: var(--texto-secundario); font-weight: 700; text-transform: uppercase;">Total de Alunos</p>
                        <h3 style="font-size: 28px; color: var(--texto-principal); margin-top: 5px;"><?php echo $tot_alunos; ?></h3>
                    </div>
                    <div style="background: var(--ifce-verde-claro); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--ifce-verde-escuro); font-size: 20px;"><i class="fa-solid fa-users"></i></div>
                </div>
            </div>

            <div style="background: white; padding: 25px; border-radius: 16px; border: 1px solid var(--borda); border-left: 5px solid var(--ifce-vermelho);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="font-size: 12px; color: var(--texto-secundario); font-weight: 700; text-transform: uppercase;">Ocorrências</p>
                        <h3 style="font-size: 28px; color: var(--texto-principal); margin-top: 5px;"><?php echo $tot_oc; ?></h3>
                    </div>
                    <div style="background: var(--ifce-vermelho-claro); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--ifce-vermelho); font-size: 20px;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                </div>
            </div>

            <div style="background: white; padding: 25px; border-radius: 16px; border: 1px solid var(--borda); border-left: 5px solid #f39c12;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="font-size: 12px; color: var(--texto-secundario); font-weight: 700; text-transform: uppercase;">Turmas Ativas</p>
                        <h3 style="font-size: 28px; color: var(--texto-principal); margin-top: 5px;"><?php echo $tot_turmas; ?></h3>
                    </div>
                    <div style="background: #fef5e7; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #f39c12; font-size: 20px;"><i class="fa-solid fa-chalkboard-user"></i></div>
                </div>
            </div>
        </div>

        <div style="background: white; padding: 30px; border-radius: 16px; border: 1px solid var(--borda);">
            <h3 style="font-size: 16px; margin-bottom: 20px;"><i class="fa-solid fa-clock-rotate-left"></i> Atualizações e Histórico</h3>
            
            <div class="feed-aviso">
                <span class="feed-data">SISTEMA</span>
                <span class="feed-titulo">Bem-vindo ao Novo SGE IFCE</span>
                <span class="feed-desc">O módulo de gestão disciplinar está conectado ao banco de dados em tempo real.</span>
            </div>

            <?php if(count($logs) > 0): ?>
                <?php foreach($logs as $index => $log): 
                    $isOculto = ($index >= 2); // Exibe os 2 primeiros, oculta o resto
                    $classeOculto = $isOculto ? "feed-oculto-coord" : "";
                    $styleOculto = $isOculto ? "display: none;" : "";
                ?>
                <div class="feed-aviso alerta <?php echo $classeOculto; ?>" style="<?php echo $styleOculto; ?>">
                    <span class="feed-data">REGISTRO DE OCORRÊNCIA</span>
                    <span class="feed-titulo"><?php echo $log['tipo']; ?> - Aluno(a): <?php echo $log['nome']; ?></span>
                    <span class="feed-desc">Detalhes: <?php echo $log['descricao'] ?: 'Sem descrição informada.'; ?></span>
                </div>
                <?php endforeach; ?>
                
                <?php if(count($logs) > 2): ?>
                    <button id="btn-hist-coord" class="btn-ver-mais" onclick="toggleFeed('feed-oculto-coord', 'btn-hist-coord')">
                        <i class="fa-solid fa-chevron-down"></i> Ver Todo o Histórico
                    </button>
                <?php endif; ?>
            <?php else: ?>
                <p style="font-size: 13px; color: var(--texto-secundario); text-align: center; padding: 20px;">Nenhum registro disciplinar cadastrado ainda.</p>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <?php
        // Consultas do Painel Acadêmico (Professor)
        $q_meus_alunos = $conn->query("SELECT COUNT(DISTINCT id_aluno) as total FROM notas WHERE disciplina = '$disciplina_prof'");
        $tot_meus_alunos = $q_meus_alunos ? $q_meus_alunos->fetch_assoc()['total'] : 0;
        
        $q_faltas = $conn->query("SELECT COUNT(*) as total FROM frequencia WHERE disciplina = '$disciplina_prof' AND status = 'Falta'");
        $tot_faltas = $q_faltas ? $q_faltas->fetch_assoc()['total'] : 0;
        
        // Histórico de Faltas do Professor
        $sql_feed_prof = "SELECT f.data_aula, a.nome FROM frequencia f JOIN alunos a ON f.id_aluno = a.id WHERE f.disciplina = '$disciplina_prof' AND f.status = 'Falta' ORDER BY f.id DESC LIMIT 10";
        $res_feed_prof = $conn->query($sql_feed_prof);
        $logs_prof = [];
        if($res_feed_prof && $res_feed_prof->num_rows > 0) {
            while($lp = $res_feed_prof->fetch_assoc()) { $logs_prof[] = $lp; }
        }
        ?>

        <div style="margin-bottom: 30px;">
            <h2 style="color: var(--ifce-verde-escuro); font-size: 24px;">Visão Acadêmica</h2>
            <p style="color: var(--texto-secundario); margin-top: 5px;">Painel de controle para a disciplina de <strong><?php echo $disciplina_prof; ?></strong>.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background: white; padding: 25px; border-radius: 16px; border: 1px solid var(--borda); border-left: 5px solid #3b82f6;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="font-size: 12px; color: var(--texto-secundario); font-weight: 700; text-transform: uppercase;">Meus Alunos</p>
                        <h3 style="font-size: 28px; margin-top: 5px;"><?php echo $tot_meus_alunos; ?></h3>
                    </div>
                    <div style="background: #eff6ff; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 20px;"><i class="fa-solid fa-user-graduate"></i></div>
                </div>
            </div>

            <div style="background: white; padding: 25px; border-radius: 16px; border: 1px solid var(--borda); border-left: 5px solid #f59e0b;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="font-size: 12px; color: var(--texto-secundario); font-weight: 700; text-transform: uppercase;">Faltas Registradas</p>
                        <h3 style="font-size: 28px; margin-top: 5px;"><?php echo $tot_faltas; ?></h3>
                    </div>
                    <div style="background: #fef3c7; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #f59e0b; font-size: 20px;"><i class="fa-solid fa-clipboard-user"></i></div>
                </div>
            </div>
        </div>

        <div style="background: white; padding: 30px; border-radius: 16px; border: 1px solid var(--borda);">
            <h3 style="font-size: 16px; margin-bottom: 20px;"><i class="fa-solid fa-clock-rotate-left"></i> Histórico do Diário</h3>
            
            <div class="feed-aviso">
                <span class="feed-data">SISTEMA</span>
                <span class="feed-titulo">Diário Online: <?php echo $disciplina_prof; ?></span>
                <span class="feed-desc">Toda nota e frequência lançada atualiza o Boletim da Coordenação automaticamente.</span>
            </div>

            <?php if(count($logs_prof) > 0): ?>
                <?php foreach($logs_prof as $index => $log): 
                    $isOculto = ($index >= 2);
                    $classeOculto = $isOculto ? "feed-oculto-prof" : "";
                    $styleOculto = $isOculto ? "display: none;" : "";
                ?>
                <div class="feed-aviso alerta <?php echo $classeOculto; ?>" style="<?php echo $styleOculto; ?> border-left-color: #f59e0b; background: #fef3c7;">
                    <span class="feed-data">REGISTRO DE FALTA</span>
                    <span class="feed-titulo">Falta Aplicada: <?php echo $log['nome']; ?></span>
                    <span class="feed-desc">Referente à data da aula informada no sistema.</span>
                </div>
                <?php endforeach; ?>
                
                <?php if(count($logs_prof) > 2): ?>
                    <button id="btn-hist-prof" class="btn-ver-mais" onclick="toggleFeed('feed-oculto-prof', 'btn-hist-prof')">
                        <i class="fa-solid fa-chevron-down"></i> Ver Todo o Histórico
                    </button>
                <?php endif; ?>
            <?php else: ?>
                <p style="font-size: 13px; color: var(--texto-secundario); text-align: center; padding: 20px;">Nenhuma falta recente registrada no seu diário.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

            <!-- COORDENAÇÃO -->
             
            <?php if ($perfil == 'Coordenadora'): ?>
                
                <!-- CADASTRAR ALUNO -->
                <div id="aba-cadastrar" class="aba">
                    <div class="card-form">
                        <h2 style="margin-bottom: 25px; font-size: 20px;">Ficha de Matrícula</h2>
                        <form action="salvar_aluno.php" method="POST" class="form-grid">
                            <div class="form-group full"><label>Nome Completo</label><input type="text" name="nome" required></div>
                            <div class="form-group"><label>Idade</label><input type="number" name="idade" required></div>
                            <div class="form-group">
                                <label>Curso e Ano</label>
                                <select name="curso" required>
                                    <option value="">Selecione o curso e o ano...</option>
                                    <optgroup label="Redes de Computadores">
                                        <option value="1º Ano - Redes">1º Ano - Redes de Computadores</option>
                                        <option value="2º Ano - Redes">2º Ano - Redes de Computadores</option>
                                        <option value="3º Ano - Redes">3º Ano - Redes de Computadores</option>
                                    </optgroup>
                                    <optgroup label="Agropecuária">
                                        <option value="1º Ano - Agropecuária">1º Ano - Agropecuária</option>
                                        <option value="2º Ano - Agropecuária">2º Ano - Agropecuária</option>
                                        <option value="3º Ano - Agropecuária">3º Ano - Agropecuária</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="form-group"><label>E-mail</label><input type="email" name="email" required></div>
                            <div class="form-group"><label>Telefone</label><input type="text" name="telefone" required></div>
                            <div class="form-group full" style="margin-top: 10px;"><button type="submit" class="btn-primario">Salvar Estudante</button></div>
                        </form>
                    </div>
                </div>

                <!-- LISTAR ALUNOS E HISTÓRICO DISCIPLINAR -->
                <div id="aba-listar" class="aba">
                    <div class="card-form" style="padding: 0;">
                        <div style="padding: 25px; border-bottom: 1px solid var(--borda);">
                            <h2 style="font-size: 18px;">Histórico Global</h2>
                            <p style="font-size: 13px; color: var(--texto-secundario);">Gerenciamento de infrações e matrículas.</p>
                        </div>
                        
                        <table>
                            <tr>
                                <th>Aluno / Matrícula</th>
                                <th>Curso</th>
                                <th>Ocorrências</th>
                                <th>Ação</th>
                            </tr>
                            <?php
                            $sql = "SELECT a.id, a.matricula, a.curso, a.nome, 
                                    (SELECT COUNT(*) FROM ocorrencias o WHERE o.id_aluno = a.id) as total_oc
                                    FROM alunos a ORDER BY a.curso ASC, a.nome ASC";
                            
                            $result = $conn->query($sql);
                            if ($result && $result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $id_aluno = $row['id'];
                                    $isLimit = ($row['total_oc'] >= 3);
                                    $rowStyle = $isLimit ? "style='background-color: #fffafb;'" : "";
                                    
                                    echo "<tr $rowStyle>
                                            <td>
                                                <strong>{$row['nome']}</strong><br>
                                                <span style='font-size: 12px; color: var(--texto-secundario);'>Mat: {$row['matricula']}</span>
                                            </td>
                                            <td>{$row['curso']}</td>
                                            <td>";
                                    
                                    if ($isLimit) echo "<span class='badge badge-vermelho'>NÍVEL MÁXIMO ATINGIDO (3+)</span><br>";
                                    else echo "{$row['total_oc']} Registrada(s)<br>";
                                    
                                    if ($row['total_oc'] > 0) {
                                        echo "<button class='btn-link' onclick='toggleMotivo(\"motivo_{$id_aluno}\")'>Ver Detalhes</button>
                                              <div id='motivo_{$id_aluno}' class='motivo-oculto'>";
                                        
                                        $res_motivos = $conn->query("SELECT tipo, descricao FROM ocorrencias WHERE id_aluno = $id_aluno ORDER BY id DESC");
                                        while($motivo = $res_motivos->fetch_assoc()) {
                                            echo "<strong>{$motivo['tipo']}:</strong> {$motivo['descricao']}<br><br>";
                                        }
                                        if ($isLimit) echo "<em>Ação Exigida: Suspensão ou Expulsão</em>";
                                        echo "</div>";
                                    } else {
                                        echo "<span style='color: var(--texto-secundario); font-size: 12px;'>Nenhuma infração</span>";
                                    }
                                    
                                    echo "</td>
                                          <td>
                                              <div class='dropdown' id='drop_container_{$id_aluno}'>
                                                  <i class='fa-solid fa-ellipsis-vertical' style='color: var(--texto-secundario); cursor: pointer; padding: 10px;' onclick='abrirDropdown({$id_aluno})'></i>
                                                  <div id='drop_menu_{$id_aluno}' class='dropdown-content'>
                                                      <a href='#' onclick='abrirModalExclusao({$id_aluno}, \"{$row['nome']}\")'>
                                                          <i class='fa-solid fa-trash-can'></i> Excluir Matrícula
                                                      </a>
                                                  </div>
                                              </div>
                                          </td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' style='text-align:center; padding:30px;'>Nenhum aluno cadastrado.</td></tr>";
                            }
                            ?>
                        </table>
                    </div>
                </div>

                <!-- LANÇAR OCORRÊNCIA -->
                <div id="aba-ocorrencia" class="aba">
                    <div class="card-form">
                        <h2 style="margin-bottom: 25px; font-size: 20px; color: var(--ifce-vermelho);">Registro Disciplinar</h2>
                        <form action="salvar_ocorrencia.php" method="POST" class="form-grid">
                            <div class="form-group full">
                                <label>Aluno Infrator</label>
                                <select name="id_aluno" required>
                                    <option value="">Selecione o aluno...</option>
                                    <?php foreach($lista_alunos as $a) { echo "<option value='{$a['id']}'>{$a['nome']} ({$a['curso']})</option>"; } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Categoria</label>
                                <select name="tipo" required>
                                    <option value="">Selecione...</option>
                                    <option value="Atraso">Atraso</option>
                                    <option value="Fardamento">Fardamento Incompleto</option>
                                    <option value="Celular">Uso de Celular</option>
                                    <option value="Indisciplina">Indisciplina</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Penalidade Sugerida</label>
                                <select name="penalidade">
                                    <option value="Advertência Verbal">Advertência Verbal</option>
                                    <option value="Notificação aos Pais">Notificação aos Pais</option>
                                    <option value="Suspensão">Suspensão</option>
                                </select>
                            </div>
                            <div class="form-group full">
                                <label>Descrição Completa</label>
                                <textarea name="descricao" rows="3" placeholder="Detalhes da infração..."></textarea>
                            </div>
                            <div class="form-group full"><button type="submit" class="btn-primario" style="background: var(--ifce-vermelho);">Registrar Ocorrência</button></div>
                        </form>
                    </div>
                </div>

                <!-- BOLETIM GERAL -->
                <div id="aba-boletim-geral" class="aba">
                    <div class="card-form">
                        <h2 style="margin-bottom: 20px;"><i class="fa-solid fa-magnifying-glass"></i> Consulta de Boletim Acadêmico</h2>
                        
                        <form action="painel.php" method="GET" class="form-grid">
                            <input type="hidden" name="aba" value="boletim">
                            
                            <div class="form-group">
                                <label>1. Filtrar por Turma</label>
                                <select id="select_turma_bg" onchange="filtrarAlunosBG()">
                                    <option value="">Todas as turmas...</option>
                                    <option value="1º Ano - Redes">1º Ano - Redes</option>
                                    <option value="2º Ano - Redes">2º Ano - Redes</option>
                                    <option value="3º Ano - Redes">3º Ano - Redes</option>
                                    <option value="1º Ano - Agropecuária">1º Ano - Agropecuária</option>
                                    <option value="2º Ano - Agropecuária">2º Ano - Agropecuária</option>
                                    <option value="3º Ano - Agropecuária">3º Ano - Agropecuária</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>2. Selecionar Aluno</label>
                                <select name="aluno_boletim_id" id="select_aluno_bg" required>
                                    <option value="">Selecione o aluno...</option>
                                    <?php foreach($lista_alunos as $a) { echo "<option data-curso='{$a['curso']}' value='{$a['id']}'>{$a['nome']}</option>"; } ?>
                                </select>
                            </div>
                            
                            <div class="form-group full">
                                <button type="submit" class="btn-primario">Gerar Boletim Completo</button>
                            </div>
                        </form>
                    </div>

                    <?php 
                    if(isset($_GET['aluno_boletim_id'])): 
                        $id_bol = $conn->real_escape_string($_GET['aluno_boletim_id']);
                        $al_info = $conn->query("SELECT nome, curso FROM alunos WHERE id = $id_bol")->fetch_assoc();
                    ?>
                    <div class="card-form" style="padding: 0; margin-top: 20px;">
                        <div style="padding: 25px; border-bottom: 1px solid var(--borda);">
                            <h2 style="font-size: 18px; color: var(--ifce-verde-escuro);">Boletim Oficial: <?php echo $al_info['nome']; ?></h2>
                            <p style="font-size: 13px; color: var(--texto-secundario);">Turma: <?php echo $al_info['curso']; ?></p>
                        </div>
                        <table>
                            <tr>
                                <th>Disciplina</th>
                                <th>N1</th><th>N2</th><th>N3</th><th>N4</th>
                                <th>Média Anual</th>
                                <th>Faltas</th>
                                <th>Situação</th>
                            </tr>
                            <?php
                            // Busca notas e soma faltas apenas desta matéria
                            $sql_bol = "SELECT disciplina, n1, n2, n3, n4, 
                                        (SELECT COUNT(*) FROM frequencia WHERE id_aluno = $id_bol AND disciplina = notas.disciplina AND status = 'Falta') as faltas 
                                        FROM notas WHERE id_aluno = $id_bol";
                            $res_bol = $conn->query($sql_bol);
                            if($res_bol->num_rows > 0) {
                                while($rb = $res_bol->fetch_assoc()) {
                                    $media = ($rb['n1'] + $rb['n2'] + $rb['n3'] + $rb['n4']) / 4;
                                    $sit = ($media >= 7) ? "<span class='badge badge-verde'>Aprovado</span>" : "<span class='badge badge-vermelho'>Em Exame</span>";
                                    echo "<tr>
                                            <td><strong>{$rb['disciplina']}</strong></td>
                                            <td>{$rb['n1']}</td><td>{$rb['n2']}</td><td>{$rb['n3']}</td><td>{$rb['n4']}</td>
                                            <td><strong>".number_format($media,1)."</strong></td>
                                            <td>{$rb['faltas']}</td>
                                            <td>{$sit}</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' style='text-align:center; padding: 25px;'>Nenhuma nota lançada ainda pelos professores.</td></tr>";
                            }
                            ?>
                        </table>
                    </div>
                    <script>
                        window.onload = function() {
                            document.querySelectorAll('.aba').forEach(aba => aba.classList.remove('ativa'));
                            document.getElementById('aba-boletim-geral').classList.add('ativa');
                        }
                    </script>
                    <?php endif; ?>
                </div>

            <!-- ============================================== -->
            <!-- TELAS DO PROFESSOR -->
            <!-- ============================================== -->
            <?php else: ?>

                <!-- LANÇAR NOTAS -->
                <div id="aba-notas-prof" class="aba">
                    <div class="card-form">
                        <h2 style="margin-bottom: 25px;"><i class="fa-solid fa-pen"></i> Lançamento de Notas - <?php echo $disciplina_prof; ?></h2>
                        <form action="salvar_notas.php" method="POST">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label>Selecionar Aluno da Turma</label>
                                <select name="id_aluno" required>
                                    <option value="">Selecione o aluno...</option>
                                    <?php foreach($lista_alunos as $a) { echo "<option value='{$a['id']}'>{$a['nome']} ({$a['curso']})</option>"; } ?>
                                </select>
                            </div>
                            
                            <label>Notas Bimestrais (0 a 10)</label>
                            <div class="grid-notas" style="margin-bottom: 20px;">
                                <input type="number" step="0.1" max="10" name="n1" placeholder="Nota N1">
                                <input type="number" step="0.1" max="10" name="n2" placeholder="Nota N2">
                                <input type="number" step="0.1" max="10" name="n3" placeholder="Nota N3">
                                <input type="number" step="0.1" max="10" name="n4" placeholder="Nota N4">
                            </div>
                            </div>
                            
                            <button type="submit" class="btn-primario">Salvar Notas no Diário</button>
                        </form>
                    </div>
                </div>

                <!-- ABA: LANÇAR FREQUÊNCIA -->
                <div id="aba-freq-prof" class="aba">
                    <div class="card-form">
                        <h2 style="margin-bottom: 25px;"><i class="fa-solid fa-calendar-check"></i> Registro de Frequência - <?php echo $disciplina_prof; ?></h2>
                        <form action="salvar_frequencia.php" method="POST" class="form-grid">
                            <div class="form-group full">
                                <label>Aluno</label>
                                <select name="id_aluno" required>
                                    <option value="">Selecione o aluno...</option>
                                    <?php foreach($lista_alunos as $a) { echo "<option value='{$a['id']}'>{$a['nome']} ({$a['curso']})</option>"; } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Data da Aula</label>
                                <input type="date" name="data_aula" required>
                            </div>
                            <div class="form-group">
                                <label>Status de Presença</label>
                                <select name="status" required>
                                    <option value="Presença">Presente</option>
                                    <option value="Falta">Faltou</option>
                                </select>
                            </div>
                            <div class="form-group full">
                                <label>Justificativa (Apenas para faltas justificadas)</label>
                                <input type="text" name="justificativa" placeholder="Atestado médico, declaração, etc.">
                            </div>
                            <div class="form-group full">
                                <button type="submit" class="btn-primario">Gravar Frequência</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- MEU DIÁRIO -->
                <div id="aba-diario-prof" class="aba">
                    <div class="card-form" style="padding: 0;">
                        <div style="padding: 25px; border-bottom: 1px solid var(--borda);">
                            <h2 style="font-size: 18px; color: var(--ifce-verde-escuro);">Meu Diário de Classe</h2>
                            <p style="font-size: 13px; color: var(--texto-secundario);">Disciplina: <?php echo $disciplina_prof; ?> (Carga Horária Base: 80h)</p>
                        </div>
                        <table>
                            <tr>
                                <th>Aluno</th>
                                <th>Turma</th>
                                <th>N1</th><th>N2</th><th>N3</th><th>N4</th>
                                <th>Média</th>
                                <th>Faltas Computadas</th>
                                <th>Situação</th>
                            </tr>
                            <?php
                    
                            $sql_diario = "SELECT a.nome, a.curso, n.n1, n.n2, n.n3, n.n4, 
                                           (SELECT COUNT(*) FROM frequencia WHERE id_aluno = a.id AND disciplina = '$disciplina_prof' AND status = 'Falta') as faltas 
                                           FROM alunos a 
                                           JOIN notas n ON a.id = n.id_aluno 
                                           WHERE n.disciplina = '$disciplina_prof' ORDER BY a.curso, a.nome";
                            $res_diario = $conn->query($sql_diario);
                            if($res_diario && $res_diario->num_rows > 0) {
                                while($rd = $res_diario->fetch_assoc()) {
                                    $media = ($rd['n1'] + $rd['n2'] + $rd['n3'] + $rd['n4']) / 4;
                               
                                    $notas_fechadas = ($rd['n1'] !== null && $rd['n1'] !== '') &&
                                                      ($rd['n2'] !== null && $rd['n2'] !== '') &&
                                                      ($rd['n3'] !== null && $rd['n3'] !== '') &&
                                                      ($rd['n4'] !== null && $rd['n4'] !== '');

                                    if ($notas_fechadas) {
                                        $situacao = ($media >= 6.0)
                                            ? "<span class='badge badge-verde'>Aprovado</span>"
                                            : "<span class='badge badge-vermelho'>Reprovado</span>";
                                    } else {
                                        $situacao = "<span style='color: var(--texto-secundario);'>-</span>";
                                    }

                                    $n1 = ($rd['n1'] !== null) ? $rd['n1'] : '-';
                                    $n2 = ($rd['n2'] !== null) ? $rd['n2'] : '-';
                                    $n3 = ($rd['n3'] !== null) ? $rd['n3'] : '-';
                                    $n4 = ($rd['n4'] !== null) ? $rd['n4'] : '-';

                                    echo "<tr>
                                            <td><strong>{$rd['nome']}</strong></td>
                                            <td>{$rd['curso']}</td>
                                            <td>{$n1}</td><td>{$n2}</td><td>{$n3}</td><td>{$n4}</td>
                                            <td><strong>".number_format($media,1)."</strong></td>
                                            <td><span class='badge badge-vermelho'>{$rd['faltas']}</span></td>
                                            <td>{$situacao}</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9' style='text-align:center; padding: 25px;'>Você ainda não lançou nenhuma nota nesta disciplina.</td></tr>";
                            }
                            ?>
                        </table>
                    </div>
                </div>

            <?php endif; ?>
        </div>
    </main>

    <?php if ($perfil == 'Coordenadora'): ?>
    <div id="modalExclusao" class="modal-overlay">
        <div class="modal-box">
            <h3><i class="fa-solid fa-triangle-exclamation"></i> Zona de Perigo</h3>
            <p style="font-size: 14px; color: var(--texto-secundario); margin-bottom: 20px;">
                Excluir <strong id="nome_aluno_modal"></strong> removerá TODO o histórico, notas e faltas.
            </p>
            <form action="excluir_aluno.php" method="POST">
                <input type="hidden" name="id_aluno" id="id_aluno_excluir">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label>Confirme a Senha:</label>
                    <input type="password" name="senha_servidor" required>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn-cancelar" onclick="fecharModal()">Cancelar</button>
                    <button type="submit" class="btn-primario" style="background: var(--ifce-vermelho);">Confirmar Exclusão</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- SCRIPT GERAL -->
    <script>

        function abrirAba(idAba, elementoClicado) {
            window.history.replaceState({}, document.title, "painel.php"); // Limpa alertas da URL
            let alertas = document.querySelectorAll('#alerta-sistema, #alerta-erro');
            alertas.forEach(al => al.style.display = 'none');

            document.querySelectorAll('.aba').forEach(aba => aba.classList.remove('ativa'));
            document.querySelectorAll('.item-menu').forEach(item => item.classList.remove('ativo'));
            
            document.getElementById(idAba).classList.add('ativa');
            elementoClicado.classList.add('ativo');
            document.getElementById('titulo-topo').innerText = elementoClicado.innerText.trim();
        }

        function toggleMotivo(id) {
            let div = document.getElementById(id);
            div.style.display = (div.style.display === "block") ? "none" : "block";
        }

        function abrirDropdown(id) {
            document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('show'));
            document.getElementById('drop_container_' + id).classList.add('show');
        }

        window.onclick = function(event) {
            if (!event.target.matches('.fa-ellipsis-vertical')) {
                document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('show'));
            }
        }

        function abrirModalExclusao(id, nome) {
            document.getElementById('id_aluno_excluir').value = id;
            document.getElementById('nome_aluno_modal').innerText = nome;
            document.getElementById('modalExclusao').style.display = 'flex';
        }

        function fecharModal() {
            document.getElementById('modalExclusao').style.display = 'none';
        }

        function filtrarAlunosBG() {
            let turmaSelecionada = document.getElementById('select_turma_bg').value;
            let selectAluno = document.getElementById('select_aluno_bg');
            let options = selectAluno.options;
            
            for(let i = 1; i < options.length; i++) {
                let cursoDoAluno = options[i].getAttribute('data-curso');
                if(cursoDoAluno.includes(turmaSelecionada) || turmaSelecionada === "") {
                    options[i].style.display = 'block';
                } else {
                    options[i].style.display = 'none';
                }
            }
            selectAluno.value = ""; 

function toggleFeed(classeOculta, idBotao) {
    let elementos = document.querySelectorAll('.' + classeOculta);
    let btn = document.getElementById(idBotao);
    
    if(elementos.length > 0) {
        let isHidden = elementos[0].style.display === 'none';
        elementos.forEach(el => {
            el.style.display = isHidden ? 'flex' : 'none';
        });
        
        if (isHidden) {
            btn.innerHTML = '<i class="fa-solid fa-chevron-up"></i> Ocultar Histórico';
        } else {
            btn.innerHTML = '<i class="fa-solid fa-chevron-down"></i> Ver Todo o Histórico';
        }
    }
}
        }
    </script>
</body>
</html>