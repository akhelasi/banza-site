param(
  [Parameter(Mandatory = $true)]
  [string]$TargetPath
)

$sourceRoot = Split-Path -Parent $PSScriptRoot
$targetRoot = Resolve-Path -LiteralPath $TargetPath -ErrorAction SilentlyContinue

if (-not $targetRoot) {
  New-Item -ItemType Directory -Force -Path $TargetPath | Out-Null
  $targetRoot = Resolve-Path -LiteralPath $TargetPath
}

$items = @(
  "AGENTS.md",
  ".agents",
  ".codex",
  "docs",
  "templates"
)

foreach ($item in $items) {
  $src = Join-Path $sourceRoot $item
  $dst = Join-Path $targetRoot $item
  if (Test-Path -LiteralPath $src) {
    Copy-Item -LiteralPath $src -Destination $dst -Recurse -Force
  }
}

Write-Host "Codex starter pack installed to $targetRoot"
