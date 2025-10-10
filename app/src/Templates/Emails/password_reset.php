<?php

/** @var object $user */
return "
    <html>
        <body style='font-family: Arial, Helvetica, sans-serif; color: #333;'>
            <div
                style='max-width:600px;margin:20px auto;border:1px solid #e6e6e6;padding:24px;border-radius:8px;background:#fff;'>
                <h2 style='color:#0b5ed7;margin:0 0 12px;'>📚 Acesso às Plataformas Acadêmicas - Unilago</h2>
                <p style='margin:0 0 16px;'>Olá, {$user->NOME}!</p>

                <p style='margin:0 0 12px;'>Seja bem-vindo(a) à <strong>Unilago</strong>! Para acompanhar sua vida acadêmica,
                    utilize as plataformas abaixo com os respectivos
                    dados de acesso:</p>

                <div style='background: #f8f9fa;padding:12px;border-radius:6px;margin-bottom:12px;'>
                    <strong>Área do Aluno</strong><br>
                    Login: <strong>{$user->LOGIN}</strong><br>
                    Senha: <strong>{$user->SENHA}</strong> <br>
                    <small><i>Em caso de esquecimento, use a opção 'Esqueci minha senha'</i></small>
                </div>

                <div style='background:#f8f9fa;padding:12px;border-radius:6px;margin-bottom:16px;'>
                    <p style='margin:0 0 12px;'>Em caso de dúvidas ou dificuldades, não hesite em nos contatar.</p><br>
                    <strong>WhatsApp:</strong> (17) 3354-6001<br>
                    <strong>E-mail:</strong> atendimento@unilago.edu.br
                </div>

                <p style='margin:18px 0 0;color:#888;font-size:12px;'>Desejamos um ótimo semestre! 💙<br><strong>Equipe
                        Unilago</strong></p>
            </div>
        </body>
    </html>
";
