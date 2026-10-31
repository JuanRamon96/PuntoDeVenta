// Copia la interfaz del sistema web (app/vistas) a escritorio/web, para que la app se vea igual y no haya dos copias que mantener.
// Se corre antes de iniciar o compilar: npm run copiar
const fs = require('fs');
const path = require('path');

const raiz = path.join(__dirname, '..', '..', 'app');
const destino = path.join(__dirname, '..', 'web');

// Imágenes de cada cuenta: la app las baja al sincronizar
const omitir = ['productos', 'usuarios', 'configuracion', 'clasificaciones', 'gastos', 'merma'].map((c) => path.join(raiz, 'vistas', 'assets', 'images', c));

function copiar(origen, dest) {
  if (omitir.includes(origen)) {
    return;
  }
  const info = fs.statSync(origen);
  if (info.isDirectory()) {
    fs.mkdirSync(dest, { recursive: true });
    for (const hijo of fs.readdirSync(origen)) {
      copiar(path.join(origen, hijo), path.join(dest, hijo));
    }
  } else {
    fs.copyFileSync(origen, dest);
  }
}

fs.rmSync(path.join(destino, 'vistas'), { recursive: true, force: true });
fs.rmSync(path.join(destino, 'controladores'), { recursive: true, force: true });
copiar(path.join(raiz, 'vistas'), path.join(destino, 'vistas'));
// Plantillas de tickets: solo se leen sus estilos
fs.mkdirSync(path.join(destino, 'controladores', 'pdf'), { recursive: true });
for (const f of ['ticketVenta.php', 'ticketCaja.php', 'ticketPago.php']) {
  fs.copyFileSync(path.join(raiz, 'controladores', 'pdf', f), path.join(destino, 'controladores', 'pdf', f));
}
console.log('Interfaz web copiada a', destino);
