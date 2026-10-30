// Arma las páginas igual que c_controller.php (_layouts, _contenido, remplazar), solo con los menús de la app offline.
const fs = require('fs');
const path = require('path');
const db = require('./db');
const sesion = require('./sesion');
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
    '#menuVentas#': itemMenu('v_ventas_offline', 'Ventas', 'bMenuVentas', 'fas fa-dollar-sign'),
    '#menuCajas#': itemMenu('v_cajas', 'Caja', 'bMenuCajas', 'fas fa-cash-register'),
    '#menuUsuarios#': sesion.puede('Usuarios') ? itemMenu('v_usuarios_offline', 'Usuarios', 'bMenuUsuarios', 'fas fa-users') : ''
  };
  pagina = pagina.replace(/#menu[A-Za-z]+#/g, (marca) => menus[marca] || '');

  // Secciones del menú que no existen en la app
  pagina = pagina.replace(/<li class="nav-item pcoded-menu-caption">\s*<label>(Compras|Facturación)<\/label>\s*<\/li>/g, '');
  // Checador de precios, configuración y PayPal requieren internet
  pagina = pagina.replace(/<a href="#linkChecador#"[\s\S]*?<\/a>/, '');
  pagina = pagina.replace(/<li><a href="javascript:void\(0\)" class="dropdown-item cargarVista"\s+carga="v_configuracion"[\s\S]*?<\/li>/, '');
  pagina = pagina.replace(/<script\s+src="https:\/\/www\.paypal\.com[^>]*><\/script>/g, '');

  pagina = pagina.replace('#suscripcion#', estadoOffline());
  pagina = pagina.replace('#fotoCuenta#', 'vistas/assets/images/default.jpg');
  pagina = pagina.replace('#emailCuenta#', html(sesion.usuario().Correo));
  pagina = pagina.replace('</head>', '<link rel="stylesheet" href="offline/offline.css">\n</head>');
  pagina = pagina.replace(/<\/body>/, '<script src="offline/offline.js"></script>\n</body>');
  return pagina;
}

function contenido(vista) {
  if (vista === 'v_cajas') {
    // Misma vista de la web, sin botones de administración
    return leer('vistas/v_cajas.php').replace('#bCortes#', '').replace('#bAgregar#', '');
  }
  const propias = ['v_ventas_offline', 'v_productos_offline', 'v_inventario_offline', 'v_promociones_offline', 'v_usuarios_offline'];
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
