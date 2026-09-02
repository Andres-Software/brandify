<!DOCTYPE html>
<html lang="pt-BR">
<body style="font-family: sans-serif; color: #1f2937;">
    <h1 style="font-size: 18px;">Backup Brandify</h1>

    <p>Segue em anexo o backup dos dados do Brandify, gerado em {{ $generatedAt->format('d/m/Y \à\s H:i') }}.</p>

    <p>Arquivos anexados:</p>
    <ul>
        <li><strong>directories.csv</strong> — pastas cadastradas</li>
        <li><strong>proposals.csv</strong> — propostas cadastradas</li>
    </ul>

    <p>Este é um email automático. Guarde os anexos em local seguro.</p>

    <p>{{ config('app.name') }}</p>
</body>
</html>
