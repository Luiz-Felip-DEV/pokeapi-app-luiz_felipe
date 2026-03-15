set -e

echo "🚀 Iniciando build..."
echo "📅 $(date)"

if [ ! -f artisan ]; then
  echo "❌ artisan não encontrado."
  exit 1
fi

echo "📦 Instalando dependências..."
composer install \
  --no-dev \
  --prefer-dist \
  --no-interaction \
  --optimize-autoloader

echo "🧹 Limpando caches..."
php artisan optimize:clear || true

echo "🔐 Ajustando permissões..."
chmod -R 775 storage bootstrap/cache || true

echo "⏳ Aguardando MySQL..."

MAX_ATTEMPTS=10
ATTEMPT=1

until php artisan tinker --execute="DB::connection()->getPdo();" >/dev/null 2>&1; do
  if [ $ATTEMPT -ge $MAX_ATTEMPTS ]; then
    echo "❌ Não foi possível conectar ao banco."
    exit 1
  fi

  echo "Tentativa $ATTEMPT/$MAX_ATTEMPTS - Banco indisponível..."
  ATTEMPT=$((ATTEMPT+1))
  sleep 5
done

echo "✅ Banco conectado com sucesso!"

echo "🗄️ Executando migrations..."
php artisan migrate --force

echo "🌱 Executando seeders..."
php artisan db:seed --force || true

php artisan storage:link || true

echo "⚡ Otimizando aplicação..."

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🎉 Deploy concluído com sucesso!"
echo "📅 $(date)"