$projectPath = "C:\Users\snaid\OneDrive\Escritorio\docker-lamp"
$browserSyncExecutable = "C:\Users\snaid\AppData\Roaming\npm\browser-sync.cmd"  # Ajusta la ruta según tu instalación

Start-Process -FilePath $browserSyncExecutable -ArgumentList "start --proxy localhost:8001 --files '$($projectPath)\administrador\**\*'" -NoNewWindow -Wait
