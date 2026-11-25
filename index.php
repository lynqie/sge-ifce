<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $conn->real_escape_string($_POST['usuario']);
    $senha = $conn->real_escape_string($_POST['senha']);
    
    $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND senha = '$senha'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['logado'] = true;
        $_SESSION['perfil'] = $user['perfil'];
        $_SESSION['nome_usuario'] = $user['nome'];
        $_SESSION['disciplina'] = $user['disciplina'];
        header("Location: painel.php");
        exit;
    } else {
        $erro_login = "Usuário ou senha incorretos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>SGE IFCE - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --ifce-verde: #2e7d32; --ifce-escuro: #1b5e20; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { display: flex; height: 100vh; background-color: white; }
        
        /* Lado Esquerdo - Marca */
        .login-left { 
            flex: 1; 
            background: linear-gradient(135deg, rgba(27,94,32,0.9), rgba(46,125,50,0.85)), url('https://vestibulares.estrategia.com/portal/wp-content/uploads/2023/11/ifce-sisu-enem.jpeg') center/cover; 
            display: flex; flex-direction: column; justify-content: center; align-items: center; 
            color: white; padding: 40px; text-align: center; 
        }
        .login-left h1 { font-size: 42px; margin-bottom: 15px; font-weight: 700; letter-spacing: -1px; }
        .login-left p { font-size: 16px; opacity: 0.9; max-width: 400px; line-height: 1.5; }
        
        /* Lado Direito - Formulário */
        .login-right { 
            flex: 1; display: flex; justify-content: center; align-items: center; background: #f8fafc; 
        }
        .login-box { width: 100%; max-width: 380px; padding: 20px; }
        .login-box h2 { font-size: 28px; color: #1e293b; margin-bottom: 30px; font-weight: 700; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;}
        .input-box { position: relative; }
        .input-box i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
        .input-box input { 
            width: 100%; padding: 15px 15px 15px 45px; border: 1px solid #cbd5e1; 
            border-radius: 12px; font-size: 15px; transition: 0.3s; background: white; 
        }
        .input-box input:focus { border-color: var(--ifce-verde); outline: none; box-shadow: 0 0 0 4px rgba(46,125,50,0.1); }
        
        .btn-login { 
            width: 100%; padding: 16px; background: var(--ifce-escuro); color: white; 
            border: none; border-radius: 12px; font-size: 15px; font-weight: 600; 
            cursor: pointer; transition: 0.3s; margin-top: 10px; 
        }
        .btn-login:hover { background: #144017; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(27,94,32,0.2); }
    </style>
</head>
<body>
    <div class="login-left">
        <i class="fa-solid fa-shield-halved" style="font-size: 65px; margin-bottom: 25px;"></i>
        <h1>SGE | IFCE</h1>
        <p>Plataforma integrada para monitoramento acadêmico e disciplinar.</p>
    </div>
    <div class="login-right">
        <div class="login-box">
            <h2>Acesse sua conta</h2>
            
            <?php if(isset($erro_login)): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; font-weight: 600; border-left: 4px solid #ef4444;">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo $erro_login; ?>
                </div>
            <?php endif; ?>

            <form action="index.php" method="POST">
                <div class="form-group">
                    <label>Usuário</label>
                    <div class="input-box">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="usuario" placeholder="Digite seu usuário" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Senha</label>
                    <div class="input-box">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="senha" placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" class="btn-login">Entrar no Sistema</button>
            </form>
        </div>
    </div>
</body>
</html>