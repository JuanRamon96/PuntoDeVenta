// Arma las páginas igual que c_controller.php (_layouts, _contenido, remplazar), solo con los menús de la app offline.
const fs = require('fs');
const path = require('path');
const db = require('./db');
const sesion = require('./sesion');
const sync = require('./sync');
const { html, fechaHumana } = require('./util');

let web = null;

function iniciar(opciones) {
  web = opciones.web;
}

function leer(relativo) {
  return fs.readFileSync(path.join(web, relativo), 'utf8');
}

function itemMenu(carga, titulo, id, icono) {
  return `<li class="nav-item">
    <a href="javascript:void(0)" class="nav-link cargarVista" carga="${carga}" titulo="${titulo}" id="${id}"><span class="pcoded-micon"><i class="${icono}"></i></span><span class="pcoded-mtext">${titulo}</span></a>
  </li>`;
}

function estadoOffline() {
  const enlace = db.ajuste('enlace') || {};
  const ultima = db.ajuste('ultimaSync');
  return `<span class="vt-offline-estado" id="vtOfflineEstado" title="Dispositivo: ${html(enlace.dispositivo ? enlace.dispositivo.nombre : '')}">
    <i class="fas fa-laptop"></i> <b>${html(enlace.caja ? enlace.caja.nombre : '')}</b>
    <span class="text-muted ms-2">Última sincronización: <span id="vtUltimaSync">${ultima ? fechaHumana(ultima) : 'nunca'}</span></span>
  </span>`;
}

function layout() {
  let pagina = leer('vistas/v_html.php');

  const menus = {
    '#menuProductos#': sesion.puede('Productos') ? itemMenu('v_productos_offline', 'Productos', 'bMenuProductos', 'fas fa-box') : '',
    '#menuInventario#': sesion.puede('Inventario') ? itemMenu('v_inventario_offline', 'Inventario', 'bMenuInventario', 'fas fa-clipboard-list') : '',
    '#menuPromociones#': sesion.puede('Promociones') ? itemMenu('v_promociones_offline', 'Promociones', 'bMenuPromociones', 'fa-solid fa-tags') : '',
    '#menuClientes#': itemMenu('v_clientes_offline', 'Clientes', 'bMenuClientes', 'fas fa-people-arrows'),
    '#menuVentas#': itemMenu('v_ventas', 'Ventas', 'bMenuVentas', 'fas fa-dollar-sign'),
    '#menuUsuarios#': (sesion.puede('Usuarios') ? itemMenu('v_usuarios_offline', 'Usuarios', 'bMenuUsuarios', 'fas fa-users') : '') +
      itemMenu('v_impresora_offline', 'Impresora', 'bMenuImpresora', 'fas fa-print')
  };
  pagina = pagina.replace(/#menu[A-Za-z]+#/g, (marca) => menus[marca] || '');

  // Secciones del menú que no existen en la app
  pagina = pagina.replace(/<li class="nav-item pcoded-menu-caption">\s*<label>(Compras|Facturación)<\/label>\s*<\/li>/g, '');
  // Checador de precios, configuración y PayPal requieren internet
  // En lugar del checador de precios: botones generales para bajar cambios y subir ventas
  pagina = pagina.replace(/<a href="#linkChecador#"[\s\S]*?<\/a>/, `<a href="javascript:void(0)" class="pop-search btn btn-outline-secondary btn-sm ml-1 bSincronizarOffline" title="Bajar productos, inventario, promociones, clientes y usuarios"><i class="fas fa-rotate"></i> Sincronizar</a>
    <a href="javascript:void(0)" class="pop-search btn btn-outline-secondary btn-sm ml-1 bSubirVentasOffline" title="Subir las ventas de este equipo"><i class="fas fa-cloud-arrow-up"></i> Subir ventas (<span class="vtPendientesSubir">${sync.pendientes()}</span>)</a>`);
  pagina = pagina.replace(/<li><a href="javascript:void\(0\)" class="dropdown-item cargarVista"\s+carga="v_configuracion"[\s\S]*?<\/li>/,
    '<li><a href="javascript:void(0)" class="dropdown-item" id="bDesenlazarEquipo"><i class="fas fa-link-slash"></i> Desenlazar equipo</a></li>');
  pagina = pagina.replace(/<script\s+src="https:\/\/www\.paypal\.com[^>]*><\/script>/g, '');

  pagina = pagina.replace('#suscripcion#', estadoOffline());
  pagina = pagina.replace('#fotoCuenta#', 'vistas/assets/images/default.jpg');
  pagina = pagina.replace('#emailCuenta#', html(sesion.usuario().Correo));
  pagina = pagina.replace('</head>', '<link rel="stylesheet" href="offline/offline.css">\n</head>');
  // Sin módulo de cajas: caja.js da clic en #bMenuCajas cuando la caja está cerrada y offline.js pide el monto de apertura.
  // Va fuera del menú lateral porque pcoded.js detiene los clics de sus enlaces.
  pagina = pagina.replace(/<\/body>/, '<a href="javascript:void(0)" id="bMenuCajas" class="d-none"></a>\n<script src="offline/offline.js"></script>\n</body>');
  return pagina;
}

function contenido(vista) {
  if (vista === 'v_cajas') {
    // Misma vista de la web, sin botones de administración
    return leer('vistas/v_cajas.php').replace('#bCortes#', '').replace('#bAgregar#', '');
  }
  if (vista === 'v_ventas') {
    // Misma pantalla de ventas de la web, con las ventas del equipo que aún no se suben
    const caja = db.existeTabla('cajas') ? db.uno('SELECT ID_Caja, Nombre FROM cajas LIMIT 1') : null;
    return leer('vistas/v_ventas.php').replace('#cajas#', caja ? `<option value="${caja.ID_Caja}">${html(caja.Nombre)}</option>` : '');
  }
  const propias = ['v_clientes_offline', 'v_productos_offline', 'v_inventario_offline', 'v_promociones_offline', 'v_usuarios_offline', 'v_impresora_offline'];
  if (propias.includes(vista)) {
    return leer(`offline/vistas/${vista}.html`);
  }
  return '<div class="alert alert-info">Esta sección solo está disponible en el sistema web.</div>';
}

function login() {
  // Misma pantalla de la web; se quitan registro y "olvidé mi contraseña", que necesitan internet
  let pagina = leer('vistas/v_login.php');
  pagina = pagina.replace('</head>', '<link rel="stylesheet" href="offline/offline.css">\n</head>');
  pagina = pagina.replace(/<\/body>/, '<script src="offline/login-offline.js"></script>\n</body>');
  return pagina;
}

module.exports = { iniciar, layout, contenido, login };
