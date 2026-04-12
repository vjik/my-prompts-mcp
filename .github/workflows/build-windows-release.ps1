$ErrorActionPreference = "Stop"

$SpcDir = "$env:RUNNER_TEMP\static-php-cli"
$BuildDir = "$env:RUNNER_TEMP\builder"
$Box = "$env:RUNNER_TEMP\box.phar"
$Spc = "$SpcDir\bin\spc"

# Box
Invoke-WebRequest -Uri "https://github.com/box-project/box/releases/download/4.7.0/box.phar" -OutFile $Box

# static-php-cli
git clone https://github.com/crazywhalecc/static-php-cli.git --depth=1 $SpcDir
Set-Location $SpcDir
composer install --no-dev

# Build micro.sfx
New-Item -Path $BuildDir -ItemType Directory -Force
Set-Location $BuildDir
php $Spc download micro --with-php=8.5.4 --for-extensions=phar --prefer-pre-built
php $Spc doctor --auto-fix
php $Spc build "phar" --build-micro

# Build PHAR
Set-Location $env:GITHUB_WORKSPACE
composer install --no-dev --no-plugins --optimize-autoloader
php $Box compile

# Combine binary
New-Item -Path "$env:GITHUB_WORKSPACE\build" -ItemType Directory -Force
php $Spc micro:combine `
    --with-micro="$BuildDir\buildroot\bin\micro.sfx" `
    --output="$env:GITHUB_WORKSPACE\build\$env:BINARY_NAME" `
    "$env:GITHUB_WORKSPACE\build\my-prompts-mcp.phar"
