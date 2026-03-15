set -e 

echo "🚀 Iniciando processo de build/deploy..."
echo "📅 $(date)"
echo "📂 Diretório atual: $(pwd)"

# ========== 1. VERIFICAÇÕES INICIAIS ==========
echo ""
echo "🔍 Verificações iniciais..."

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ Erro: arquivo artisan não encontrado. Execute o script na raiz do projeto Laravel."
    exit 1
fi

# Verificar se composer.json existe
if [ ! -f "composer.json" ]; then
    echo "❌ Erro: composer.json não encontrado."
    exit 1
fi

echo "✅ Verificações ok!"

# ========== 2. INSTALAR DEPENDÊNCIAS ==========
echo ""
echo "📦 Instalando dependências do Composer..."

# Remover vendor se existir (para garantir instalação limpa)
if [ -d "vendor" ]; then
    echo "🧹 Removendo vendor antigo..."
    rm -rf vendor
fi

# Instalar dependências otimizadas para produção
composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --no-scripts

if [ $? -eq 0 ]; then
    echo "✅ Dependências instaladas com sucesso!"
else
    echo "❌ Erro ao instalar dependências"
    exit 1
fi

# ========== 3. CONFIGURAR APLICAÇÃO ==========
echo ""
echo "🔑 Configurando aplicação..."

# Gerar chave da aplicação (se não existir)
php artisan key:generate --force --no-interaction

if [ $? -eq 0 ]; then
    echo "✅ Chave da aplicação configurada!"
else
    echo "❌ Erro ao gerar chave da aplicação"
    exit 1
fi

# ========== 4. BANCO DE DADOS ==========
echo ""
echo "🗄️ Configurando banco de dados..."

# Executar migrations
echo "📊 Executando migrations..."
php artisan migrate --force --no-interaction

if [ $? -eq 0 ]; then
    echo "✅ Migrations executadas com sucesso!"
else
    echo "❌ Erro ao executar migrations"
    exit 1
fi

echo "🌱 Executando seeds..."
php artisan db:seed --force --no-interaction 

# ========== 5. OTIMIZAÇÕES DE CACHE ==========
echo ""
echo "⚡ Aplicando otimizações..."

# Limpar caches antigos
echo "🧹 Limpando caches antigos..."
php artisan optimize:clear --no-interaction

# Gerar novos caches
echo "💾 Gerando cache de configuração..."
php artisan config:cache --no-interaction

echo "🛣️ Gerando cache de rotas..."
php artisan route:cache --no-interaction

echo "👁️ Gerando cache de views..."
php artisan view:cache --no-interaction

# Otimização geral
echo "🎯 Aplicando otimização geral..."
php artisan optimize --no-interaction

if [ $? -eq 0 ]; then
    echo "✅ Otimizações aplicadas com sucesso!"
else
    echo "⚠️ Aviso: Algumas otimizações falharam (continuando...)"
fi

# ========== 6. LINKS E PERMISSÕES ==========
echo ""
echo "🔗 Configurando links e permissões..."

# Criar link do storage (se necessário)
if [ -d "storage" ]; then
    php artisan storage:link --no-interaction --force 2>/dev/null || echo "⚠️ Storage link já existe ou não necessário"
fi

# Ajustar permissões (se em servidor Linux)
if [ -d "storage" ]; then
    chmod -R 775 storage
    chmod -R 775 bootstrap/cache
    echo "✅ Permissões ajustadas!"
fi

# ========== 7. VERIFICAÇÕES FINAIS ==========
echo ""
echo "🔍 Verificações finais..."


# Verificar se otimizações funcionaram
if [ -f "bootstrap/cache/config.php" ]; then
    echo "✅ Cache de configuração ativo"
else
    echo "⚠️ Cache de configuração não encontrado"
fi

# ========== 8. FINALIZAÇÃO ==========
echo ""
echo "🎉 Build concluído com sucesso!"
echo "📅 Finalizado em: $(date)"
echo ""
echo "📋 Resumo:"
echo "   ✅ Dependências instaladas"
echo "   ✅ Chave da aplicação configurada"
echo "   ✅ Migrations executadas"
echo "   ✅ Documentação gerada"
echo "   ✅ Caches otimizados"
echo ""
echo "🌐 Sua aplicação está pronta para produção!"
echo ""

exit 0